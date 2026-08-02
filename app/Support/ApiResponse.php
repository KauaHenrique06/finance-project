<?php

namespace App\Support;

class ApiResponse
{
    /**
     * Create a new class instance.
     */
    public static function success($data = null, $message = null, int $code = 200) {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($data = null, $message = null, int $code = 400) {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
