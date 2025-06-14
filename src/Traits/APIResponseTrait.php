<?php

namespace MA\LaravelApiResponse\Traits;

use Generator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use MA\LaravelApiResponse\Enums\ErrorCodesEnum;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use UnitEnum;

trait APIResponseTrait
{
    /**
     * The ok response
     * @param $data mixed|null
     * @param string|null $message
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiOk(mixed $data = null, ?string $message = null, array $headers = []): JsonResponse|StreamedJsonResponse
    {
        return $this->apiResponse([
            'data' => $data,
            'message' => $message,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The not found response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiNotFound(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
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

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiNotFound', 'RESOURCE_NOT_FOUND'));
        }

        return $this->apiResponse([
            'type' => 'notfound',
            'throw_exception' => $throw_exception,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The bad request response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|null|UnitEnum $errorCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiBadRequest(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|null|UnitEnum $errorCode = null,
        array                    $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
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

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiBadRequest', 'BAD_REQUEST'));
        }

        return $this->apiResponse([
            'type' => 'Bad Request',
            'throw_exception' => $throw_exception,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param array|string $errors
     * @param string|null $message
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiException(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
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

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiException', 'SERVER_ERROR'));
        }

        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => $throw_exception,
            'data' => null,
            'message' => $message,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param null|string $message
     * @param array|string $errors
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiUnauthenticated(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
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

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiUnauthenticated', 'UNAUTHORIZED_ACCESS'));
        }

        return $this->apiResponse([
            'type' => 'unauthenticated',
            'throw_exception' => true,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * The exception response
     * @param string|null $message
     * @param array|string $errors
     * @param string|int|UnitEnum|null $errorCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiForbidden(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null,
        array                    $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
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

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiForbidden', 'FORBIDDEN'));
        }

        return $this->apiResponse([
            'type' => 'forbidden',
            'throw_exception' => true,
            'message' => $message,
            'data' => null,
            'errors' => $errorsCollection->toArray(),
            'errorCode' => $errorCode,
            'response_headers' => $headers,
        ]);
    }

    /**
     * Paginate data
     * @param AnonymousResourceCollection|LengthAwarePaginator $pagination
     * @param array $appends Extra data to append to the response
     * @param bool $reverse_data Reverse data
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiPaginate(
        LengthAwarePaginator|AnonymousResourceCollection $pagination,
        array                                            $appends = [],
        bool                                             $reverse_data = false,
        array                                            $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
        // Set pagination data
        $isFirst = $pagination->onFirstPage();
        $isLast = $pagination->onLastPage();
        $isNext = $pagination->hasMorePages();
        $isPrevious = (($pagination->currentPage() - 1) > 0);

        $current = $pagination->currentPage();
        $last = $pagination->lastpage();
        $next = ($isNext ? $current + 1 : null);
        $previous = ($isPrevious ? $current - 1 : null);

        $data = $pagination->items();

        // Reverse data
        if ($reverse_data) {
            $data = array_reverse($data);
        }

        // If no page found
        if ($current > $last) {
            return $this->apiResponse(['type' => 'not found']);
        }

        // Set extra
        $extra = $appends + [
                'pagination' => [
                    'meta' => [
                        'page' => [
                            "current" => $current,
                            "first" => 1,
                            "last" => $last,
                            "next" => $next,
                            "previous" => $previous,

                            "per" => $pagination->perPage(),
                            "from" => $pagination->firstItem(),
                            "to" => $pagination->lastItem(),

                            "count" => $pagination->count(),
                            "total" => $pagination->total(),

                            "isFirst" => $isFirst,
                            "isLast" => $isLast,
                            "isNext" => $isNext,
                            "isPrevious" => $isPrevious,
                        ],
                    ],
                    "links" => [
                        "path" => $pagination->path(),
                        "first" => $pagination->url(1),
                        "next" => ($isNext ? $pagination->url($next) : null),
                        "previous" => ($isPrevious ? $pagination->url($previous) : null),
                        "last" => $pagination->url($last),
                    ],
                ],
            ];

        // Remove pagination links
        if (config('response.hideMetaPaginationLinks', true) && isset($extra['pagination'])) {
            unset($extra['pagination']['links']);
        }

        return $this->apiRawResponse(data: $data, extra: $extra, responseHeaders: $headers);
    }

    /**
     * Validate
     * @param array|Request $request
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     * @return array|JsonResponse|StreamedJsonResponse
     */
    public function apiValidate(
        array|Request $request,
        array         $rules,
        array         $messages = [],
        array         $attributes = []
    ): array|JsonResponse|StreamedJsonResponse
    {
        // Check if data is a request instance
        if ($request instanceof Request) {
            $request = $request->all();
        }

        $validator = app(Factory::class)->make(
            $request, $rules, $messages, $attributes
        );

        // If validation fails
        if ($validator->fails()) {

            // Set errors
            $errors = config('response.returnValidationErrorsKeys', true) ?
                $validator->errors()->toArray() :
                $validator->errors()->all();
            if ((bool)config('response.returnDefaultErrorCodes', true)) {
                $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiValidate', 'VALIDATION_FAILED'));
            } else {
                $errorCode = null;
            }

            return $this->apiBadRequest($errors, null, true, $errorCode);
        }

        return $validator->validated();
    }

    /**
     * Die and debug
     * @param mixed $data
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiDD(mixed $data): JsonResponse|StreamedJsonResponse
    {
        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => true,
            'message' => 'Die and dump',
            'data' => $data,
        ]);
    }

    /**
     * @param Generator $generator
     * @param string|null $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiStreamResponse(
        Generator $generator,
        ?string   $message = null,
        int       $statusCode = Response::HTTP_OK,
        array     $headers = []
    ): JsonResponse|StreamedJsonResponse
    {
        return $this->apiResponse([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $generator,
            'isStream' => true,
            'response_headers' => $headers,
        ]);
    }

    /**
     * @param array|string|null $arg [type, filter_data, throw_exception, message, data]
     * @param mixed $data
     * @param array $guards
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiResponse(array|string|null $arg = null, mixed $data = null, array $guards = []): JsonResponse|StreamedJsonResponse
    {
        // Set attributes
        $type = isset($arg['type']) && !!$this->checkGetType($arg['type']) ? $arg['type'] : null;
        $filter_data = isset($arg['filter_data']) && (bool)$arg['filter_data'];
        $throw_exception = !isset($arg['throw_exception']) || (bool)$arg['throw_exception'];
        $message = $arg['message'] ?? null;
        $errorCode = $arg['errorCode'] ?? null;
        $isStream = $arg['isStream'] ?? false;
        $extra = $arg['extra'] ?? [];
        $responseHeaders = $arg['response_headers'] ?? [];
        if (array_key_exists('data', $extra)) {
            $extra['renamedDataAttributeInArray'] = $extra['data'];
            unset($extra['data']);
        }

        // Handle type
        if (is_null($type) && (!is_null($arg) && !is_array($arg) && !is_null($data))) {
            // Set type
            $type = $arg;
        }

        // Handle data
        if (is_null($data) && isset($arg['data'])) {
            $data = $arg['data'];
        } elseif (
            is_null($data) &&
            !is_null($arg) &&
            (!is_array($arg) ||
                !(
                    isset($arg['type']) ||
                    isset($arg['filter_data']) ||
                    isset($arg['message'])
                )
            )
        ) {
            $data = $arg;
        }

        // Set status code
        if (is_null($type)) {
            $status_code = $arg['status_code'] ?? Response::HTTP_OK;
        } else {
            $status_code = $this->setStatusCode($type);
        }

        // Filter data[]
        $data = ((is_array($data) && !!$filter_data) ? $this->removeNullArrayValues($data) : $data);

        // Set data if sent as array
        if (is_array($data) && array_key_exists('data', $data) && sizeof($data) === 1) {
            $data = $data['data'];
        }

        // Check if errors is sent
        $errors = collect($arg['errors'] ?? [])
            ->filter(fn($item) => !empty($item));

        // Merge extra with errors
        if ($errors->isEmpty()) {
            $errors->merge($extra);
        }

        // Check if errors
        if (isset($arg['errors'])) {
            $response = $this->apiRawResponse($data, $message, $arg['errors'], $status_code, $errorCode, $isStream, $responseHeaders);
        } else {
            $response = $this->apiRawResponse($data, $message, $extra, $status_code, $errorCode, $isStream, $responseHeaders);
        }

        // Throw exceptions
        if (in_array($status_code, config('response.apiExceptionCodes', [])) && $throw_exception) {
            throw new HttpResponseException($response);
        }

        return $response;
    }

    /**
     * The row response
     * @param mixed|null $data
     * @param string|null $message
     * @param array $extra
     * @param int $status_code
     * @param null|UnitEnum|int|string $errorCode
     * @param bool $isStream
     * @param array $responseHeaders
     * @return JsonResponse|StreamedJsonResponse
     */
    private function apiRawResponse(
        mixed                    $data = null,
        ?string                  $message = null,
        array                    $extra = [],
        int                      $status_code = Response::HTTP_OK,
        null|UnitEnum|int|string $errorCode = null,
        bool                     $isStream = false,
        array                    $responseHeaders = [],
    )
    {
        // Filter data[]
        $data = (is_array($data) && config('response.removeNullDataValues', false) ? $this->removeNullArrayValues($data) : $data);

        // Check if data is an empty array
        if (config('response.setNullEmptyData', false)) {
            if (
                (is_string($data) && !strlen($data)) ||
                (is_array($data) && !sizeof($data))
            ) {
                $data = null;
            }
        }

        // Set response
        $response = collect([
            'status' => $this->setStatus($status_code),
            'statusCode' => $status_code,
            'timestamp' => now()->timestamp,
            'message' => ($message == null ? $this->setMessage($status_code) : $message),
            'data' => $data,
        ]);

        // Check if error codes enabled
        if (config('response.enableErrorCodes', true) && $errorCode) {
            // Get error codes type
            $errorCodesType = config('response.errorCodesType', 'string');
            if (!in_array($errorCodesType, ['string', 'integer'])) {
                $errorCodesType = 'string';
            }

            // Get error code if not enum
            if (!$errorCode instanceof UnitEnum) {
                $errorCode = $this->getErrorCode($errorCode);
            }

            // Set error code
            $errorCode = $errorCodesType === 'string' ? $errorCode->name : $errorCode->value;

            $response->put('errorCode', $errorCode);
        }

        // Convert to array
        $response = $response->toArray();

        // Set extra response data
        if (!!sizeof($extra)) {
            $response = $this->arrayMergeRecursiveDistinct($response, $extra);
        }

        // Return data
        return $isStream ? response()->streamJson($response, $status_code, $responseHeaders) : response()->json($response, $status_code, $responseHeaders);
    }

    /**
     * Check and get the type
     * @param int|string $type
     * @return array|string|string[]|void
     */
    private function checkGetType(int|string $type = 'OK')
    {
        // If not string
        if (!is_numeric($type) && !is_string($type)) {
            return;
        }
        if (!is_numeric($type)) {
            $type = trim($type);
            $type = strtolower($type);
            $type = str_replace('-', '', $type);
            $type = str_replace('.', '', $type);
            $type = str_replace(' ', '', $type);
        }
        $types = [
            'ok',
            'created',
            'accepted',
            'notfound',
            'conflict',
            'badrequest',
            'exception',
            'unauthenticated',
            'unauthorized',
            'forbidden',
            'servererror',
            'error',
        ];

        if (in_array($type, $types) || in_array($type, config('response.statusCodes', []))) {
            return $type;
        } else {
            return 'ok';
        }
    }

    /**
     * Set status from status_code
     * @param int|string $type
     * @return int
     */
    private function setStatusCode(int|string $type = 'OK'): int
    {
        // Get type
        $type = $this->checkGetType($type);
        if (is_numeric($type)) {
            $status_code = $type;
        } else {
            $status_code = match ($type) {
                'created' => Response::HTTP_CREATED,
                'accepted' => Response::HTTP_ACCEPTED,
                'notfound' => Response::HTTP_NOT_FOUND,
                'conflict' => Response::HTTP_CONFLICT,
                'badrequest' => Response::HTTP_BAD_REQUEST,
                'exception' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'unauthenticated', 'unauthorized' => Response::HTTP_UNAUTHORIZED,
                'forbidden' => Response::HTTP_FORBIDDEN,
                'servererror', 'error' => Response::HTTP_INTERNAL_SERVER_ERROR,
                default => Response::HTTP_OK,
            };
        }
        return $status_code;
    }

    /**
     * Set status from status_code
     * @param int $status_code
     * @return bool
     */
    private function setStatus(int $status_code = Response::HTTP_OK): bool
    {
        return in_array($status_code, config('response.apiSuccessCodes', []));
    }

    /**
     * Set message from status_code
     * @param int $status_code
     * @return string
     */
    private function setMessage(int $status_code = Response::HTTP_OK): string
    {
        return match ($status_code) {
            Response::HTTP_OK => 'OK',
            Response::HTTP_CREATED => 'Created',
            Response::HTTP_ACCEPTED => 'Accepted',
            Response::HTTP_NOT_FOUND => 'Not found!',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal server error!',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Unprocessable entity!',
            Response::HTTP_UNAUTHORIZED => 'Unauthenticated!',
            Response::HTTP_FORBIDDEN => 'Unauthorized!',
            Response::HTTP_NO_CONTENT => 'No content!',
            Response::HTTP_BAD_REQUEST => 'Bad Request!',
            Response::HTTP_CONFLICT => 'Conflict!',
            default => 'Error',
        };
    }

    /**
     * Remove null values from array
     * @param array $array
     * @param string $callback
     * @return array
     */
    private function removeNullArrayValues(array $array, string $callback = ''): array
    {
        foreach ($array as $key => & $value) {
            if (is_array($value)) {
                $value = $this->removeNullArrayValues($value, $callback);
            } else {
                if (!empty($callback)) {
                    if (!$callback($value)) {
                        unset($array[$key]);
                    }
                } else {
                    if ((is_string($value) and !(bool)$value) or is_null($value)) {
                        unset($array[$key]);
                    }
                }
            }
        }
        unset($value);

        return $array;
    }

    /**
     * @param array<int|string, mixed> $array1
     * @param array<int|string, mixed> $array2
     *
     * @return array<int|string, mixed>
     */
    private function arrayMergeRecursiveDistinct(array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Get error code
     * @param string|int $errorCode
     * @return UnitEnum
     */
    private function getErrorCode(string|int $errorCode): UnitEnum
    {
        // Set a default value if error code not sent
        $errorCodesEnum = config('response.errorCodes', ErrorCodesEnum::class);

        // Set error code enum
        if (!$errorCodesEnum instanceof UnitEnum) {
            $errorCodesEnum = ErrorCodesEnum::class;
        }

        return call_user_func([$errorCodesEnum, 'getProperty'], $errorCode);
    }
}
