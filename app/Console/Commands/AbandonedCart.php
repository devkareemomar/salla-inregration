<?php

namespace App\Console\Commands;

use App\Models\Merchant;
use App\Models\Template;
use App\Models\Workspace;
use Error;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AbandonedCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abandoned:cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'abandoned cart description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->checkAbandonedCart();
    }

    // check Abandoned Cart
    public function checkAbandonedCart()
    {
        try {

            $workspaces = Workspace::with('merchant')->get();

            foreach ($workspaces as $workspace) {

                if ($workspace->is_ready == 1) {
                    $url = 'https://api.salla.dev/admin/v2/carts/abandoned';
                    $authorization = "Authorization: Bearer " . $workspace->merchant->access_token;

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    Log::info($result);



                    $result = json_decode($result, true);

                    if ($result['data']) {
                        $workspace_token = $workspace->token;
                        $channelId = $workspace->channelId;
                        foreach ($result['data'] as $data) {
                            $this->sendMessage($data, $workspace_token,$channelId);
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            Log::info($e->getMessage());
            return  $e->getMessage();
        }
    }

    // send message whatsapp
    public function sendMessage($data, $workspace_token,$channelId)
    {
        $data = dot($data);
        $mobile = $data['customer.mobile'];


        $url = 'https://api.respond.io/v2/contact/phone:' . $mobile . '/message';


        $authorization = "Authorization: Bearer " . $workspace_token;

        $template = Template::whereHas('workspace', function ($query) use ($workspace_token) {
            return $query->where('token', '=', $workspace_token);
        })->where('event', 'abandoned.cart')->first();

        $array = json_decode($template->parameters, true);

        $parameters = '';

        foreach ($array as $key => $parameter) {

            $param =  $data[$parameter];

            if ($parameter == 'items') {
                $param =  count($data[$parameter]);
            }

            if ($key === array_key_last($array)) {

                $parameters .= '{
                                        "type": "text",
                                        "text": "' . $param . '"
                                    }';
            } else {
                $parameters .= '{
                                        "type": "text",
                                        "text": "' . $param . '"
                                    },';
            }
        }

        $payload = '{
                "channelId": ' . $channelId .',
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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        Log::info($result);

        // dd($result);

        //     $result = json_decode($result);
        //     if ($result->status == 'error') {
        //         $this->createContact($data,$workspace_token);
        //    }  

        return json_decode($result, true);
    }

    // create contact in workspace
    public function createContact($data, $workspace_token)
    {
        $first_name = $data['customer.name'];
        $last_name  = $data['customer.name'];
        $mobile     = $data['customer.mobile'];
        $url = 'https://social.bevatel.com/api/v1/contact/';


        $authorization = "Authorization: Bearer " . $workspace_token;

        $ch = curl_init($url);
        $payload = '{
                        "custom_fields": [
                            {
                                "name": "phone",
                                "value": "' . $mobile . '"
                            },
                            {
                                "name": "firstName",
                                "value": "' . $first_name . '"
                            },
                            {
                                "name": "lastName",
                                "value": "' . $last_name . '"
                            }
                        ]
                    }';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        Log::info($result);

        // return $result;
    }
}
