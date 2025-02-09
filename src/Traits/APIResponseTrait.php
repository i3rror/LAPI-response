<?php

namespace MA\LaravelApiResponse\Traits;

use Generator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use MA\LaravelApiResponse\Enums\ErrorCodesEnum;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use UnitEnum;

trait APIResponseTrait
{
    /**
     * The ok response
     * @param $data mixed|null
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiOk(mixed $data = null): JsonResponse|StreamedJsonResponse
    {
        return $this->apiResponse([
            'data' => $data,
        ]);
    }

    /**
     * The not found response
     * @param null $errors
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiNotFound($errors = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null): JsonResponse|StreamedJsonResponse
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiNotFound', 'RESOURCE_NOT_FOUND'));
        }

        return $this->apiResponse([
            'type' => 'notfound',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
            'errorCode' => $errorCode,
        ]);
    }

    /**
     * The bad request response
     * @param null $errors
     * @param bool $throw_exception
     * @param string|int|null|UnitEnum $errorCode
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiBadRequest($errors = null, bool $throw_exception = true, string|int|null|UnitEnum $errorCode = null): JsonResponse|StreamedJsonResponse
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiBadRequest', 'BAD_REQUEST'));
        }

        return $this->apiResponse([
            'type' => 'Bad Request',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
            'errorCode' => $errorCode,
        ]);
    }

    /**
     * The exception response
     * @param null $errors
     * @param bool $throw_exception
     * @param string|int|UnitEnum|null $errorCode
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiException($errors = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null): JsonResponse|StreamedJsonResponse
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        // Set a default value if error code not sent
        if (!$errorCode && (bool)config('response.returnDefaultErrorCodes', true)) {
            $errorCode = $this->getErrorCode(config('response.errorCodesDefaults.apiException', 'SERVER_ERROR'));
        }

        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
            'errorCode' => $errorCode,
        ]);
    }

    /**
     * The exception response
     * @param null $message
     * @param array|string|null $errors
     * @param string|int|UnitEnum|null $errorCode
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiUnauthenticated($message = null, array|string $errors = null, string|int|UnitEnum|null $errorCode = null): JsonResponse|StreamedJsonResponse
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
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
            'errors' => $errors,
            'errorCode' => $errorCode,
        ]);
    }

    /**
     * The exception response
     * @param null $message
     * @param array|string|null $errors
     * @param string|int|UnitEnum|null $errorCode
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiForbidden($message = null, array|string $errors = null, string|int|UnitEnum|null $errorCode = null): JsonResponse|StreamedJsonResponse
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
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
            'errors' => $errors,
            'errorCode' => $errorCode,
        ]);
    }

    /**
     * Paginate data
     * @param AnonymousResourceCollection|LengthAwarePaginator $pagination
     * @param array $appends Extra data to append to the response
     * @param bool $reverse_data Reverse data
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiPaginate(LengthAwarePaginator|AnonymousResourceCollection $pagination, array $appends = [], bool $reverse_data = false): JsonResponse|StreamedJsonResponse
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

        return $this->apiRawResponse($data, null, $extra);
    }

    /**
     * Validate
     * @param array|Request $data
     * @param $roles
     * @param array $messages
     * @param array $customAttributes
     * @return array|JsonResponse|StreamedJsonResponse
     */
    public function apiValidate(array|Request $data, $roles, array $messages = [], array $customAttributes = []): array|JsonResponse|StreamedJsonResponse
    {
        // Check if data is a request instance
        if ($data instanceof Request) {
            $data = $data->all();
        }

        // Validate data
        $validator = Validator::make($data, $roles, $messages, $customAttributes);

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

            return $this->apiBadRequest($errors, true, $errorCode);
        }

        return $validator->validated();
    }

    /**
     * Die and debug
     * @param $data
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiDD($data): JsonResponse|StreamedJsonResponse
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
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiStreamResponse(Generator $generator, string $message = null, int $statusCode = Response::HTTP_OK): JsonResponse|StreamedJsonResponse
    {
        return $this->apiResponse([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $generator,
            'isStream' => true,
        ]);
    }

    /**
     * @param array|string|null $arg [type, filter_data, throw_exception, message, data]
     * @param null $data
     * @param array $guards
     * @return JsonResponse|StreamedJsonResponse
     */
    public function apiResponse(array|string $arg = null, $data = null, array $guards = []): JsonResponse|StreamedJsonResponse
    {
        // Set attributes
        $type = isset($arg['type']) && !!$this->checkGetType($arg['type']) ? $arg['type'] : null;
        $filter_data = isset($arg['filter_data']) && (bool)$arg['filter_data'];
        $throw_exception = !isset($arg['throw_exception']) || (bool)$arg['throw_exception'];
        $message = $arg['message'] ?? null;
        $errorCode = $arg['errorCode'] ?? null;
        $isStream = $arg['isStream'] ?? false;
        $extra = $arg['extra'] ?? [];
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

        // Check if errors
        if (isset($arg['errors'])) {
            $response = $this->apiRawResponse($data, $message, $arg['errors'], $status_code, $errorCode, $isStream);
        } else {
            $response = $this->apiRawResponse($data, $message, $extra, $status_code, $errorCode, $isStream);
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
     * @param null $message
     * @param array $extra
     * @param int $status_code
     * @param null|UnitEnum|int|string $errorCode
     * @param bool $isStream
     * @return JsonResponse|StreamedJsonResponse
     */
    private function apiRawResponse(mixed $data = null, $message = null, array $extra = [], int $status_code = Response::HTTP_OK, null|UnitEnum|int|string $errorCode = null, bool $isStream = false)
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

        return $isStream ? response()->streamJson($response, $status_code) : response()->json($response, $status_code);
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
     * @param $array
     * @param string $callback
     * @return mixed
     */
    private function removeNullArrayValues($array, string $callback = ''): mixed
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
