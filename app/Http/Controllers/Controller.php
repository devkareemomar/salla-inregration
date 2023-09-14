<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Classes\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $jsonResponse;

    public function jsonResponse(): JsonResponse
    {
        if ($this->jsonResponse) {
            return $this->jsonResponse;
        }

        return $this->jsonResponse = new JsonResponse();
    }
}
