<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * 성공 JSON 응답을 반환한다.
     */
    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        $payload = ['success' => true];

        if ($message !== '') {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    /**
     * 리소스 생성 성공(201 Created) JSON 응답을 반환한다.
     */
    protected function created(mixed $data = null, string $message = '생성되었습니다.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * 실패 JSON 응답을 반환한다.
     */
    protected function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
