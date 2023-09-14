<?php


namespace App\Classes;

use App\Exceptions\QueryExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class JsonResponse implements Responsable
{

    private $status = false;

    private $message = null;

    private $code = null;

    private $result = null;

    private $errors = null;


    /**
     * set status
     *> 
     * @param bool $status
     * 
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * get status
     * 
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * set message
     * 
     * @param string $message
     * 
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * get message
     * 
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * set code
     * 
     * @param int $code
     * 
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * get code
     * 
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * set results
     * 
     * @param array $data
     * 
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * get result
     * 
     * @return array|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * set errors
     * 
     * @param array $errors
     * 
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * get errors
     * 
     * @return array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * Exception Response
     *
     * @param Exception $exception
     * 
     * @return ServiceResponse
     */
    public function responseFromException(Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return $this->setStatus(false)
                ->setMessage('authentication required.')
                ->setCode(401);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->setStatus(false)
                ->setMessage('User does not have any of the necessary access rights.')
                ->setCode(403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->setStatus(false)
                ->setMessage('resource not found.')
                ->setCode(404);
        }

        if ($exception instanceof MethodNotFoundException) {
            return $this->setStatus(false)
                ->setMessage('invalid method.')
                ->setCode(405);
        }

        if ($exception instanceof HttpException) {
            return $this->setStatus(false)
                ->setMessage($exception->getMessage())
                ->setCode($exception->getStatusCode());
        }

        if ($exception instanceof ValidationException) {
            return $this->setStatus(false)
                ->setMessage("validation error.")
                ->setCode(422)
                ->setErrors($exception->errors());
        }

        if (config('app.debug')) {
            dd($exception);
            return $this->setStatus(false)
                ->setMessage($exception->getMessage())
                ->setCode(500)
                ->setErrors($exception);
        }

        return $this->setStatus(false)
            ->setMessage('unexpected error.')
            ->setCode(500);
    }

    /**
     * return response data as array
     * 
     * @return array
     */
    public function toArray()
    {
        $responseData = [
            "success" => $this->getStatus(),
            "code" => $this->getCode(),
            "message" => $this->getMessage(),
        ];

        if ($this->getResult()) {
            $responseData["result"] = $this->getResult();
        }

        if ($this->getErrors()) {
            $responseData["errors"] = $this->getErrors();
        }

        return $responseData;
    }


    public function toResponse($request)
    {
        return response()->json($this->toArray(), $this->getCode());
    }
}
