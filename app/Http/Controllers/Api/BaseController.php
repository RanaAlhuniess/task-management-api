<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Return generic json response with the given data.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, int $statusCode = 200, array $headers = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * Respond with error.
     *
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message, int $statusCode = 404): \Illuminate\Http\JsonResponse
    {
        return $this->respond([
            'message' => $message
        ], $statusCode);
    }
}
