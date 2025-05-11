<?php

namespace App\Http\Responses;
use App\Constants\StatusCodes;

class ApiResponse
{
    public static function sendResponse(
        bool $success,
        int $statusCode,
        string $message,
        $data = null
    ) {

        if ($data == null) {
            $data = [
                'success' => ($statusCode == StatusCodes::HTTP_OK) ? true : false,
            ];
        }
        return response()->json([
            'success' => $success,
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function sendError(
        bool $success,
        int $statusCode,
        string $message,
        $data = null
    ) {
        return response()->json([
            'success' => $success,
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}