<?php

namespace App\Http\Controllers\Api\Traits;

trait BaseResponseTrait
{
    public function respond($status, $respond_data, $message)
    {
        return ['status' => $status, 'data' => $respond_data, 'message' => $message];
    }

    public function succeed($respond_data = [], $message = 'Request success!')
    {
        return $this->respond(true, $respond_data, $message);
    }

    public function failed($message = 'Request failed!', $respond_data = [])
    {
        return $this->respond(false, $respond_data, $message);
    }

}
