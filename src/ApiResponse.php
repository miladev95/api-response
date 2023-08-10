<?php

namespace Miladev\ApiResponse;

trait ApiResponse
{
    public function successResponse($data, $message = "Success", $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function failResponse($message = "Error", $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}