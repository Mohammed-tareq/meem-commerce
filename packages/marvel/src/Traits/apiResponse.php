<?php

namespace Marvel\Traits;

trait ApiResponse
{
    public function apiresponse($message, $status, $success = true, $data = [])
    {
        $result = [
            'status' => $status,
            'message' => $message,
            'success' => $success,
        ];
        if (!empty($data)) $result['data'] = $data;
        return response()->json($result, $status);
    }
}