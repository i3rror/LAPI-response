<?php

namespace MA\LaravelApiResponse\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse|RedirectResponse|HttpFoundationResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? apiUnauthenticated()
            : redirect()->guest(route('login'));
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param Request $request
     * @param ValidationException $exception
     * @return JsonResponse|StreamedJsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return apiResponse([
            'status_code' => $exception->status,
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ]);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        $data = collect();
        if ((bool)config('app.debug')) {
            // Set data
            $data = $data->merge([
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => (new Collection($e->getTrace()))->map(fn($trace) => Arr::except($trace, ['args']))->all(),
            ]);
            $message = $e->getMessage();
        } else {
            $message = $this->isHttpException($e) ? $e->getMessage() : 'Server Error';
        }

        return apiResponse([
            'status_code' => $this->isHttpException($e) ? $e->getStatusCode() : 500,
            'throw_exception' => true,
            'message' => $message,
            'data' => $data->toArray(),
            'response_headers' => $this->isHttpException($e) ? $e->getHeaders() : [],
        ]);
    }
}
