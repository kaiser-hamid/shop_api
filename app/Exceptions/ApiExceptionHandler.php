<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;

class ApiExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage(),
                    'data' => null,
                    'errors' => $exception->errors()
                ], 422);
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => false,
                    'message' => 'No record found',
                    'data' => null,
                    'errors' => null
                ], 404);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Route Not Found',
                    'data' => null,
                    'errors' => null
                ], 404);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                    'data' => null,
                    'errors' => null
                ], 401);
            }

            // For any other exceptions
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage() ?: 'Server Error',
                'data' => null,
                'errors' => $exception->getTrace()
            ], 500);
        }

        return parent::render($request, $exception);
    }
    


}