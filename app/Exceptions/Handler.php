<?php

namespace App\Exceptions;

use App\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use ReflectionException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
        TokenMismatchException::class,
        NotFoundHttpException::class,
        ReflectionException::class
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        if (!in_array(get_class($exception), $this->dontReport)) {
            Log::error("ExHandler: " . $exception->getMessage() . ", code: " . $exception->getCode() . ", full: $exception");
        }
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            echo view('errors.404');
            die();
        }
        return parent::render($request, $exception);
    }
}
