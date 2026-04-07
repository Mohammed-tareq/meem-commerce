<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception)
    {
        // Return JSON for API requests
        if ($this->shouldReturnJson($request, $exception)) {
            return $this->handleJsonResponse($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Determine if the exception should return JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return bool
     */
    protected function shouldReturnJson($request, Throwable $exception): bool
    {
        return $request->expectsJson() ||
               str_contains($request->getPathInfo(), '/api') ||
               $request->is('api/*');
    }

    /**
     * Handle JSON exception responses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleJsonResponse($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Internal Server Error';
        $errors = [];

        // Handle ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = $exception->getMessage() ?: 'Resource Not Found';
        }
        // Handle NotFoundHttpException
        elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = $exception->getMessage() ?: 'Not Found';
        }
        // Handle MethodNotAllowedHttpException
        elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'Method Not Allowed';
        }
        // Handle AuthorizationException
        elseif ($exception instanceof AuthorizationException) {
            $statusCode = 403;
            $message = $exception->getMessage() ?: 'This action is unauthorized';
        }
        // Handle ValidationException
        elseif ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'Validation Failed';
            $errors = $exception->validator->errors()->all();
        }
        // Handle HttpException (includes various HTTP status codes)
        elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP Exception';
        }
        // Handle other exceptions
        else {
            $statusCode = 500;
            $message = config('app.debug') ? $exception->getMessage() : 'Internal Server Error';
        }

        return response()->json([
            'message' => $message,
            'status' => false,
        ], $statusCode);
    }
}
