<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function sendResponse(string $message, $result = [], int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (! empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }

    public function sendError(string $message, $result = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($result)) {
            $response['errors'] = $result;
        }

        return response()->json($response, $code);
    }
}
