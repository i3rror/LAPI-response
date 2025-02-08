<?php

namespace MA\LaravelApiResponse\Exceptions;

use Error;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use MA\LaravelApiResponse\Traits\APIResponseTrait;
use ParseError;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class LumenHandler extends ExceptionHandler
{
    use APIResponseTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Symfony\Component\HttpFoundation\Response
    {
        // Not found http exception
        if ($e instanceof NotFoundHttpException && $request->wantsJson()) {
            return $this->apiResponse(['type' => 'not found']);
        }

        // Method not allowed http exception
        if ($e instanceof MethodNotAllowedHttpException && $request->wantsJson()) {
            return $this->apiResponse(['type' => 'not found']);
        }

        // Status code 0
        if ($e instanceof HttpException && $request->wantsJson()) {
            return $this->apiBadRequest();
        }

        // Server error
        if (($e instanceof Error || $e instanceof ParseError) && $request->wantsJson()) {
            // Check if app debug is enabled to return traces
            if (config('app.debug')) {
                // Set data
                $data = new Collection([
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ]);

                return $this->apiResponse([
                    'type' => 'server error',
                    'message' => $e->getMessage(),
                    'data' => $data,
                ]);
            }

            // Return server error response
            return $this->apiResponse([
                'type' => 'server error'
            ]);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|StreamedJsonResponse
     */
    protected function unauthenticated(Request $request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? $this->apiResponse(['type' => 'unauthenticated'])
            : redirect()->guest(route('login'));
    }
}
