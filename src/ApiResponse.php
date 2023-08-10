<?php

namespace Miladev\ApiResponse;

trait ApiResponse
{
    public function successResponse($data = [], $message = "Success", $statusCode = 200,$header = [])
    {
        return response([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode,$header);
    }

    public function failResponse($message = "Error", $statusCode = 400, $header = [])
    {
        return response([
            'status' => 'error',
            'message' => $message,
        ], $statusCode,$header);
    }
}