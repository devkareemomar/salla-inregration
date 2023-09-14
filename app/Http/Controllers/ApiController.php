<?php

namespace App\Http\Controllers;

use App\Models\ComingEvent;
use App\Models\Merchant;
use App\Models\Template;
use App\Models\Workspace;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        // $this->middleware('auth');
    }

    public function callback(Request $request)
    {
        Log::info($request->all());
        return  $this->jsonResponse()->setStatus(true)
            ->setMessage("this workspace is not ready")
            ->setCode(200);
    }
    /**
     * call webhook function.
     */
    public function webhook(Request $request)
    {

        Log::info($request);

        try {

            if ($request->event == 'app.store.authorize') {
                // create or update   authorize customers 
                $this->storMerchant($request);
            }

            if ($request->event == 'app.settings.updated') {
                //  activate workspace   
                $this->UpdateWorkSpace($request);
            }

            if (Str::startsWith($request->event, 'order.')) {
                //  activate workspace   

                $this->sendMessage($request);
            }

            return $this->jsonResponse()->setStatus(true)
                ->setCode(200);
        } catch (\Exception $e) {

            return $this->jsonResponse()->setStatus(false)
                ->setMessage($e->getMessage())
                ->setCode(200);
        }
    }

    // store Merchant 
    protected function storMerchant($request)
    {

        $merchantName = $this->getMerchantName($request->data['access_token']);

        // save marchent 
        DB::table('merchants')->where([
            'merchant' => $request->merchant,
        ])->updateOrInsert([
            'merchant' => $request->merchant,
        ], [
            'access_token' => $request->data['access_token'],
            'refresh_token' => $request->data['refresh_token'],
            'name' => $merchantName,
        ]);
    }

    // return Merchant Name
    protected function getMerchantName($token)
    {
        $url = 'https://api.salla.dev/admin/v2/oauth2/user/info';
        $authorization = "Authorization: Bearer " . $token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result = json_decode($result)->data->name;
    }

    // send message whatsapp
    public function sendMessage($request)
    {

        $data = dot($request->all());
        $merchant = $data['merchant'];
        $mobile_code = $data['data.customer.mobile_code'];
        $mobile = $mobile_code . $data['data.customer.mobile'];
        if ($data['event'] == 'order.status.updated') {
            $mobile =  $data['data.order.customer.mobile'];
        }

        $lastEvent = ComingEvent::where([
            'order_id' => $data[OrderService::events($data['event'], 'order_id')],
            'status' => $data[OrderService::events($data['event'], 'status')]
        ])->first();

        
        if (!$lastEvent) {
            $url = 'https://api.respond.io/v2/contact/phone:' . $mobile . '/message';
            $workspace = Workspace::whereHas('merchant', function ($query) use ($merchant) {
                return $query->where('merchant', '=', $merchant);
            })->first();
            $token = $workspace->token;

            $checkContact = $this->CheckContactExist($token, $mobile);
            if (!json_decode($checkContact, true)['phone']) {
                $this->createContact($request);
            }

            if ($workspace->is_ready == 0) {
                return  $this->jsonResponse()->setStatus(false)
                    ->setMessage("this workspace is not ready")
                    ->setCode(422);
            }

            $template = Template::whereHas('workspace', function ($query) use ($workspace) {
                return $query->where('id', '=', $workspace->id);
            })->where('event', $request->event)->first();

            $array = json_decode($template->parameters, true);
            $parameters = '';

            foreach ($array as $key => $parameter) {

                if ($key === array_key_last($array)) {

                    $parameters .= '{
                            "type": "text",
                            "text": "' . $data[$parameter] . '"
                                        }';
                } else {
                    $parameters .= '{
                                            "type": "text",
                                            "text": "' . $data[$parameter] . '"
                                        },';
                }
            }


            $payload = '{
                                    "channelId": ' . $workspace->channelId . ',
                                    "message": {
                                        "type": "whatsapp_template",
                                        "template": {
                                            "name": "' . $template->name . '",
                                            "languageCode": "' . $template->lang . '",
                                    "components": [
                                        {
                                            "type": "body",
                                            "text": " ' . $template->content . '",
                                            "parameters": [' . $parameters . ']
                                        }
                                        ]
                                    }
                            }
                        }';

            Log::info($payload);

            $authorization = "Authorization: Bearer " . $token;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $this->saveEvent($data, json_decode($result, true)['messageId']);
            Log::info($result);
        }

        // dd($result);



    }

    private function saveEvent($data, $messageId)
    {
        ComingEvent::create([
            'merchant_id'       => $data['merchant'],
            'event'             => $data['event'],
            'order_id'          => $data[OrderService::events($data['event'], 'order_id')],
            'status'            => $data[OrderService::events($data['event'], 'status')],
            'customer_name'     => $data[OrderService::events($data['event'], 'customer_name')],
            'customer_phone'    => $data[OrderService::events($data['event'], 'customer_phone')],
            'message'           => ($messageId) ? 'sent' : 'failed',
            'event_json'        => json_encode($data),
        ]);
    }

    // create contact in workspace
    public function createContact($request)
    {
        $merchant = $request->merchant;
        $first_name = $request->data['customer']['first_name'];
        $last_name = $request->data['customer']['last_name'];
        $mobile = $request->data['customer']['mobile_code'] . $request->data['customer']['mobile'];
        $url = 'https://api.respond.io/v2/contact/phone:' . $mobile;

        $token = Workspace::whereHas('merchant', function ($query) use ($merchant) {
            return $query->where('merchant', '=', $merchant);
        })->first()->token;

        $authorization = "Authorization: Bearer " . $token;

        $ch = curl_init($url);
        $payload = '{
                        "firstName": "' . $first_name . '",
                        "lastName": "' . $last_name . '",
                        "phone": "' . $mobile . '"
                      }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        Log::info($result);
    }


    //  return order details for bot
    public function orderDetails($orderId)
    {
        try {
            $url = 'https://api.salla.dev/admin/v2/orders/' . $orderId;

            $workspace = Workspace::where('token', request()->header('token'))->where('channelId', request()->header('channelId'))->with('merchant')->first();
            $token = $workspace->merchant->access_token;
            $authorization = "Authorization: Bearer " . $token;

            if ($workspace->is_ready == 0) {
                return $this->jsonResponse()->setStatus(false)
                    ->setMessage("this workspace is not ready")
                    ->setCode(422);
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            // $result = json_encode(json_decode($result,true));

            Log::info($result);

            return $result;
        } catch (\Exception $e) {

            return $this->jsonResponse()->setStatus(false)
                ->setMessage($e->getMessage())
                ->setCode(404);
        }
    }


    public function validationToken(Request $request)
    {
        // Log::info($request);
        $token = $request->data['token'];

        if ($token) {
            $workspace = Workspace::where('token', $token)->first();

            if (isset($workspace) && $workspace->is_ready == 0) {

                return response()->json([
                    'status' => 422,
                    'success' => false,
                    'code' => 'error',
                    'message' => 'alert.invalid.fields',
                    'fields' => ['token' => 'This code has not been published, please contact the application developerز']
                ], 422);
            }
        }
    }


    //   activate workspace   
    protected function UpdateWorkSpace($request)
    {
        Merchant::where('merchant', $request->merchant)->update([
            'email' => $request->data['settings']['email'],
            'phone' => $request->data['settings']['phone'],
        ]);

        $token = $request->data['settings']['token'];

        if ($token) {
            Workspace::where('token', $token)->update(['is_active' => 1]);
        }
    }

    protected function CheckContactExist($token, $mobile)
    {
        $url = 'https://api.respond.io/v2/contact/phone:' . $mobile;
        $authorization = "Authorization: Bearer " . $token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
