<?php

namespace MA\LaravelApiResponse\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use MA\LaravelApiResponse\Traits\APIResponseTrait;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    use APIResponseTrait;

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
        if (!$e instanceof HttpResponseException) {
            $customHandlers = collect(config('response.customErrorHandlers', []))
                ->filter(fn($handler) => is_array($handler) && count($handler) > 0)
                ->all();

            foreach ($customHandlers as $key => $handler) {
                if (isset($handler['message'])) {
                    if (is_callable($handler['message'])) {
                        $message = $handler['message']($e);
                    } else {
                        $message = $handler['message'];
                    }
                } else {
                    $message = null;
                }

                if ($e instanceof $key) {
                    $exception = $this->mergeWithExtras([
                        'throw_exception' => true,
                        'status_code' => $handler['statusCode'] ?? $e->getCode(),
                        'message' => $message,
                        'data' => $handler['data'] ?? null,
                        'response_headers' => $this->isHttpException($e) ? $e->getHeaders() : [],
                    ], $handler['extra'] ?? [], $handler['errors'] ?? [])
                        ->all();
                    return apiResponse($exception);
                }
            }
        }

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
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        // Set errors
        $errors = config('response.returnValidationErrorsKeys', true) ?
            $exception->validator->errors()->toArray() :
            $exception->validator->errors()->all();

        // Set errors
        $errorsCollection = collect($errors)
            ->filter(function ($value, $key) {
                return !empty($value);
            });

        // Set errors collection
        if ($errorsCollection->isNotEmpty()) {
            $errorsCollection = collect([
                'errors' => $errorsCollection->toArray(),
            ]);
        }

        // Get error code
        if ((bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiValidate', 'VALIDATION_FAILED'));
        } else {
            $errorCode = null;
        }

        return apiResponse([
            'status_code' => $exception->status,
            'throw_exception' => true,
            'message' => $exception->getMessage(),
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $exception->getResponse(),
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
            'status_code' => $this->getStatusCode($e),
            'throw_exception' => true,
            'message' => $message,
            'data' => $data->toArray(),
            'response_headers' => $this->isHttpException($e) ? $e->getHeaders() : [],
        ]);
    }

    /**
     * @param Throwable $e
     * @return int
     */
    protected function getStatusCode(Throwable $e)
    {
        if ($this->isHttpException($e)) {
            return $e->getStatusCode();
        }

        // Set client error status codes
        $statusCodes = [
            HttpFoundationResponse::HTTP_BAD_REQUEST,
            HttpFoundationResponse::HTTP_UNAUTHORIZED,
            HttpFoundationResponse::HTTP_PAYMENT_REQUIRED,
            HttpFoundationResponse::HTTP_FORBIDDEN,
            HttpFoundationResponse::HTTP_NOT_FOUND,
            HttpFoundationResponse::HTTP_METHOD_NOT_ALLOWED,
            HttpFoundationResponse::HTTP_NOT_ACCEPTABLE,
            HttpFoundationResponse::HTTP_PROXY_AUTHENTICATION_REQUIRED,
            HttpFoundationResponse::HTTP_REQUEST_TIMEOUT,
            HttpFoundationResponse::HTTP_CONFLICT,
            HttpFoundationResponse::HTTP_GONE,
            HttpFoundationResponse::HTTP_LENGTH_REQUIRED,
            HttpFoundationResponse::HTTP_PRECONDITION_FAILED,
            HttpFoundationResponse::HTTP_REQUEST_ENTITY_TOO_LARGE,
            HttpFoundationResponse::HTTP_REQUEST_URI_TOO_LONG,
            HttpFoundationResponse::HTTP_UNSUPPORTED_MEDIA_TYPE,
            HttpFoundationResponse::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE,
            HttpFoundationResponse::HTTP_EXPECTATION_FAILED,
            HttpFoundationResponse::HTTP_I_AM_A_TEAPOT,
            HttpFoundationResponse::HTTP_MISDIRECTED_REQUEST,
            HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY,
            HttpFoundationResponse::HTTP_LOCKED,
            HttpFoundationResponse::HTTP_FAILED_DEPENDENCY,
            HttpFoundationResponse::HTTP_TOO_EARLY,
            HttpFoundationResponse::HTTP_UPGRADE_REQUIRED,
            HttpFoundationResponse::HTTP_PRECONDITION_REQUIRED,
            HttpFoundationResponse::HTTP_TOO_MANY_REQUESTS,
            HttpFoundationResponse::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE,
            HttpFoundationResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS,
        ];

        // Return status code if it is in the list
        if (in_array($e->getCode(), $statusCodes)) {
            return config('response.renderClientErrorsStatusCode', false) ? $e->getCode() : HttpFoundationResponse::HTTP_BAD_REQUEST;
        }

        // Return default status code
        return HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Merge additional data and errors into the base collection while normalizing
     * and deduplicating error messages.
     *
     * @param array $base The base collection to merge data into.
     * @param array $extra Additional data to merge into the collection.
     * @param array $extraErrors Additional error messages to merge into the "errors" key.
     * @return Collection The modified base collection with merged data and errors.
     */
    protected function mergeWithExtras(array $base, array $extra = [], array $extraErrors = []): Collection
    {
        $base = collect($base);
        $currentExtra = (array)$base->get('extra', []);
        $incomingExtra = (array)($extra ?? []);

        // Collect error sources (may be keyed or non-keyed)
        $errorSources = [
            $currentExtra['errors'] ?? [],
            $incomingExtra['errors'] ?? [],
            $extraErrors ?? [],
        ];

        // Normalize message(s) to an array of unique non-empty strings
        $normalizeMessages = static function ($value): array {
            $stack = [is_array($value) ? $value : [$value]];
            $out = [];
            while ($stack) {
                $node = array_pop($stack);
                foreach ($node as $v) {
                    if (is_array($v)) {
                        $stack[] = $v;
                    } elseif ($v instanceof Stringable) {
                        $out[] = (string)$v;
                    } elseif (is_scalar($v)) {
                        $str = (string)$v;
                        if ($str !== '') {
                            $out[] = $str;
                        }
                    }
                }
            }
            return array_values(array_unique($out));
        };

        // Merge errors preserving keys when provided
        $numericMessages = [];    // list: ["msg1", "msg2"]
        $keyedMessages = [];    // map:  "field" => "msg" or ["msg1", "msg2"]

        foreach ($errorSources as $source) {
            foreach ((array)$source as $key => $val) {
                $msgs = $normalizeMessages($val);

                if ($msgs === []) {
                    continue;
                }

                if (is_string($key)) {
                    // Merge into keyed bucket
                    if (!array_key_exists($key, $keyedMessages)) {
                        // Reduce to string if single message
                        $keyedMessages[$key] = count($msgs) === 1 ? $msgs[0] : $msgs;
                        continue;
                    }

                    // Existing value may be string or array
                    $existing = $keyedMessages[$key];
                    $existingArr = is_array($existing) ? $existing : [$existing];
                    $merged = array_values(array_unique(array_merge($existingArr, $msgs)));
                    // Reduce to string if single message
                    $keyedMessages[$key] = count($merged) === 1 ? $merged[0] : $merged;
                } else {
                    // Non-keyed -> append to numeric list
                    foreach ($msgs as $m) {
                        $numericMessages[$m] = true; // use keys to dedupe
                    }
                }
            }
        }

        // Convert numeric messages set to list
        $numericMessages = array_values(array_keys($numericMessages));

        // Build final errors array:
        // numeric entries first, then keyed entries
        $finalErrors = [];
        foreach ($numericMessages as $m) {
            $finalErrors[] = $m;
        }
        foreach ($keyedMessages as $k => $v) {
            $finalErrors[$k] = $v;
        }

        // Merge extra excluding its own errors (we already processed them)
        $finalExtra = array_merge(
            $currentExtra,
            array_diff_key($incomingExtra, ['errors' => true])
        );

        if (!empty($finalErrors)) {
            $finalExtra['errors'] = $finalErrors;
        } else {
            unset($finalExtra['errors']);
        }

        if (empty($finalExtra)) {
            $base->forget('extra');
        } else {
            $base->put('extra', $finalExtra);
        }

        return $base;
    }
}
