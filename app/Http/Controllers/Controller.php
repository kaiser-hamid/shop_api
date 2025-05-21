<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function successResponse($message = 'Success', $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null
        ], 200);
    }

    public function errorResponse($message = 'Error', $errors = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $code);
    }

    public function notFoundResponse($message = 'Not Found', $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], 404);
    }

    public function validationErrorResponse($message = 'Validation Error', $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], 422);
    }
    

}
