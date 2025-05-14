<?php

namespace App\Exceptions;

use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{

    use ResponseHelper;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $lang = Auth::check() ? Auth::user()->preferred_language : 'en';


        // Handle AuthorizationException
        if ($exception instanceof AuthorizationException) {
            return $this->Error([], __('auth.unauthorized',[],$lang), 403);
        }

        // Handle NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            return $this->Error([], __('message.resource_not_found',[],$lang), 404);
        }

        // Handle ValidationException
        if ($exception instanceof ValidationException) {
            return $this->Validation($exception->errors(), __('message.validation_error',[],$lang), 422);
        }

        // Default handling for other exceptions
        return $this->Error([], $exception->getMessage(), 500);
    }
}
