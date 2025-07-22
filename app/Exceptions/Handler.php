<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenMismatchException::class,
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

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        // Log additional context for debugging
        if ($this->shouldReport($exception)) {
            \Log::error('Exception occurred', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): Response
    {
        // Handle API requests differently
        if ($request->expectsJson()) {
            return $this->renderJsonResponse($request, $exception);
        }

        // Handle specific exception types
        if ($exception instanceof AuthenticationException) {
            return $this->handleAuthenticationException($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->handleAuthorizationException($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($request, $exception);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedHttpException($request, $exception);
        }

        if ($exception instanceof TooManyRequestsHttpException) {
            return $this->handleTooManyRequestsHttpException($request, $exception);
        }

        if ($exception instanceof TokenMismatchException) {
            return $this->handleTokenMismatchException($request, $exception);
        }

        // Handle database exceptions
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return $this->handleDatabaseException($request, $exception);
        }

        // Handle custom POS exceptions
        if ($exception instanceof \App\Exceptions\InsufficientStockException) {
            return $this->handleInsufficientStockException($request, $exception);
        }

        if ($exception instanceof \App\Exceptions\PaymentProcessingException) {
            return $this->handlePaymentProcessingException($request, $exception);
        }

        // Handle general HTTP exceptions
        if ($exception instanceof HttpException) {
            return $this->handleHttpException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Render JSON response for API requests.
     */
    protected function renderJsonResponse($request, Throwable $exception): Response
    {
        $status = 500;
        $message = 'Internal Server Error';
        $errors = [];

        if ($exception instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = 'Forbidden';
        } elseif ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'Validation Error';
            $errors = $exception->errors();
        } elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Not Found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'Method Not Allowed';
        } elseif ($exception instanceof TooManyRequestsHttpException) {
            $status = 429;
            $message = 'Too Many Requests';
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP Error';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status' => $status,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        return response()->json($response, $status);
    }

    /**
     * Handle authentication exceptions.
     */
    protected function handleAuthenticationException($request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return redirect()->guest(route('login'))
            ->with('error', 'Please log in to access this page.');
    }

    /**
     * Handle authorization exceptions.
     */
    protected function handleAuthorizationException($request, AuthorizationException $exception): Response
    {
        return response()->view('errors.403', [
            'message' => $exception->getMessage() ?: 'Access Denied',
        ], 403);
    }

    /**
     * Handle validation exceptions.
     */
    protected function handleValidationException($request, ValidationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }

        return redirect()->back()
            ->withErrors($exception->errors())
            ->withInput();
    }

    /**
     * Handle not found exceptions.
     */
    protected function handleNotFoundHttpException($request, NotFoundHttpException $exception): Response
    {
        return response()->view('errors.404', [
            'message' => 'The requested resource was not found.',
        ], 404);
    }

    /**
     * Handle method not allowed exceptions.
     */
    protected function handleMethodNotAllowedHttpException($request, MethodNotAllowedHttpException $exception): Response
    {
        return response()->view('errors.405', [
            'message' => 'Method not allowed for this endpoint.',
        ], 405);
    }

    /**
     * Handle too many requests exceptions.
     */
    protected function handleTooManyRequestsHttpException($request, TooManyRequestsHttpException $exception): Response
    {
        return response()->view('errors.429', [
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $exception->getRetryAfter(),
        ], 429);
    }

    /**
     * Handle token mismatch exceptions.
     */
    protected function handleTokenMismatchException($request, TokenMismatchException $exception): Response
    {
        return redirect()->back()
            ->with('error', 'Your session has expired. Please try again.')
            ->withInput();
    }

    /**
     * Handle database exceptions.
     */
    protected function handleDatabaseException($request, \Illuminate\Database\QueryException $exception): Response
    {
        // Log the database error
        \Log::error('Database query error', [
            'sql' => $exception->getSql(),
            'bindings' => $exception->getBindings(),
            'error' => $exception->getMessage(),
        ]);

        // Check for common database errors
        $errorCode = $exception->getCode();
        $message = 'A database error occurred. Please try again.';

        if ($errorCode === '23000') {
            $message = 'This operation violates database constraints. Please check your data and try again.';
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return response()->view('errors.database', [
            'message' => $message,
        ], 500);
    }

    /**
     * Handle insufficient stock exceptions.
     */
    protected function handleInsufficientStockException($request, \App\Exceptions\InsufficientStockException $exception): Response
    {
        return redirect()->back()
            ->with('error', $exception->getMessage())
            ->withInput();
    }

    /**
     * Handle payment processing exceptions.
     */
    protected function handlePaymentProcessingException($request, \App\Exceptions\PaymentProcessingException $exception): Response
    {
        return redirect()->back()
            ->with('error', $exception->getMessage())
            ->withInput();
    }

    /**
     * Handle general HTTP exceptions.
     */
    protected function handleHttpException($request, HttpException $exception): Response
    {
        $status = $exception->getStatusCode();
        $message = $exception->getMessage() ?: 'An error occurred';

        if (view()->exists("errors.{$status}")) {
            return response()->view("errors.{$status}", [
                'message' => $message,
            ], $status);
        }

        return response()->view('errors.generic', [
            'status' => $status,
            'message' => $message,
        ], $status);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        return $this->handleAuthenticationException($request, $exception);
    }
} 