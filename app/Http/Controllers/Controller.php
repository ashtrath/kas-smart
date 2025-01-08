<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function sendResponse($message, $result = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (! empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, 200);
    }

    public function sendError($message, $result = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }
}
