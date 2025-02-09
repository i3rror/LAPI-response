<?php

namespace MA\LaravelApiResponse\Exceptions;

use Error;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
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

class Handler extends ExceptionHandler
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
     * @return JsonResponse|StreamedJsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        // Check if request expects json
        if($request->expectsJson()){
            // Check if app debug is enabled to return traces
            if ((bool)config('app.debug')) {
                // Set data
                $data = collect([
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ]);

                return $this->apiResponse([
                    'status_code' => 500,
                    'message' => $e->getMessage(),
                    'data' => $data,
                ]);
            }

            // Not found http exception OR Method not allowed http exception
            if ($e instanceof NotFoundHttpException || $e instanceof MethodNotAllowedHttpException || $e instanceof ModelNotFoundException) {
                return $this->apiNotFound();
            }

            // Status code 0
            if ($e instanceof HttpException) {
                return $this->apiBadRequest();
            }

            // Server error
            if (($e instanceof Error || $e instanceof ParseError)) {
                // Return server error response
                return $this->apiResponse([
                    'type' => 'server error'
                ]);
            }
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
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? $this->apiUnauthenticated()
            : redirect()->guest(route('login'));
    }
}
