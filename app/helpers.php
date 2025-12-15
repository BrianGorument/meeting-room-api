<?php

if (!function_exists('json_success')) {
    /**
     * Return a new JSON success response.
     *
     * @param  array  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    function json_success(string $message = 'Success', array $data = [], int $statusCode = 200)
    {
        $responseData = ['message' => $message];
        if (!empty($data)) {
            $responseData['data'] = $data;
        }
        return response()->json($responseData, $statusCode);
    }
}

if (!function_exists('json_error')) {
    /**
     * Return a new JSON error response.
     *
     * @param  string  $Message
     * @param  string  $Error
     * @param  int  $statusCode
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function json_error(string $Message, string $Error, int $statusCode = 400, array $errors = [])
    {
        $responseData = [
            'Error' => $Error,
            'Massage' => $Message,
        ];
        if (!empty($errors)) {
            $responseData['errors'] = $errors;
        }
        return response()->json($responseData, $statusCode);
    }
}
