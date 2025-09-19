<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {   
        // dd(get_class($e), $request->is('api/*'));
        
        if ($e instanceof ValidationException) {
            return new JsonResponse([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }

        return parent::render($request, $e);
    }
}
