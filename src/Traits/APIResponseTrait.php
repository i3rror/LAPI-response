<?php

namespace MA\LaravelApiResponse\Traits;

use HttpResponseException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response as Res;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

trait APIResponseTrait
{
    /**
     * The ok response
     * @param $data
     * @return Application|ResponseFactory|Res
     */
    public function apiOk($data)
    {
        return $this->apiResponse([
            'data' => $data,
        ]);
    }

    /**
     * The not found response
     * @param $errors
     * @param bool $throw_exception
     * @return Application|ResponseFactory|Res
     */
    public function apiNotFound($errors = null, bool $throw_exception = true)
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        return $this->apiResponse([
            'type' => 'notfound',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
        ]);
    }

    /**
     * The bad request response
     * @param $errors
     * @param bool $throw_exception
     * @return Application|ResponseFactory|Res
     */
    public function apiBadRequest($errors = null, bool $throw_exception = true)
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        return $this->apiResponse([
            'type' => 'Bad Request',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
        ]);
    }

    /**
     * The exception response
     * @param $errors
     * @param bool $throw_exception
     * @return Application|ResponseFactory|Res
     */
    public function apiException($errors = null, bool $throw_exception = true)
    {
        // Set errors
        if (!is_null($errors)) {
            $errors = [
                'errors' => (is_array($errors) ? $errors : [$errors])
            ];
        }

        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => $throw_exception,
            'data' => null,
            'errors' => $errors,
        ]);
    }

    /**
     * Paginate data
     * @param LengthAwarePaginator|AnonymousResourceCollection $pagination
     * @param bool $reverse_data
     * @return Application|ResponseFactory|Res
     */
    public function apiPaginate($pagination, bool $reverse_data = false)
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
        $extra = [
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

        return $this->apiRawResponse($data, null, $extra);
    }

    /**
     * Validate
     * @param array|Request $data
     * @param $roles
     * @param array $messages
     * @param array $customAttributes
     * @return array|Application|ResponseFactory|Res
     */
    public function apiValidate($data, $roles, array $messages = [], array $customAttributes = [])
    {
        // Check if data is a request instance
        if ($data instanceof Request) {
            $data = $data->only(array_keys($roles));
        }

        // Validate data
        $validator = Validator::make($data, $roles, $messages, $customAttributes);

        // If validation fails
        if ($validator->fails()) {
            return $this->apiBadRequest($validator->errors(), true);
        }

        return $validator->validated();
    }

    /**
     * Die and debug
     * @param $data
     * @return Application|ResponseFactory|Res
     */
    public function apiDD($data)
    {
        return $this->apiResponse([
            'type' => 'Exception',
            'throw_exception' => true,
            'message' => 'Die and dump',
            'data' => $data,
        ]);
    }

    /**
     * @param array|string|null $arg [type, filter_data, throw_exception, message, data]
     * @param null $data
     * @param array $guards
     * @return Application|ResponseFactory|Res
     */
    public function apiResponse($arg = null, $data = null, array $guards = [])
    {
        // Set attributes
        $type = isset($arg['type']) && !!$this->checkGetType($arg['type']) ? $arg['type'] : null;
        $filter_data = isset($arg['filter_data']) && (bool)$arg['filter_data'];
        $throw_exception = !isset($arg['throw_exception']) || (bool)$arg['throw_exception'];
        $message = $arg['message'] ?? null;

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
            $status_code = $arg['status_code'] ?? Res::HTTP_OK;
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
            $response = $this->apiRawResponse($data, $message, $arg['errors'], $status_code);
        } else {
            $response = $this->apiRawResponse($data, $message, [], $status_code);
        }

        // Throw exceptions
        if (in_array($status_code, config('response.apiExceptionCodes', [])) && $throw_exception) {
            throw new HttpResponseException($response);
        }

        return $response;
    }

    /**
     * The row response
     * @param $data
     * @param $message
     * @param array $extra
     * @param int $status_code
     * @return Application|ResponseFactory|Res
     */
    private function apiRawResponse($data = null, $message = null, array $extra = [], int $status_code = Res::HTTP_OK)
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

        $response = [
            'status' => $this->setStatus($status_code),
            'statusCode' => $status_code,
            'timestamp' => now()->timestamp,
            'message' => ($message == null ? $this->setMessage($status_code) : $message),
            'data' => $data,
        ];

        // Set extra response data
        if (!!sizeof($extra)) {
            $response = array_merge_recursive_distinct($response, $extra);
        }

        return response($response, $status_code);
    }

    /**
     * Check and get the type
     * @param int|string $type
     * @return array|string|string[]|void
     */
    private function checkGetType($type = 'OK')
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
    private function setStatusCode($type = 'OK'): int
    {
        // Get type
        $type = $this->checkGetType($type);
        if (is_numeric($type)) {
            $status_code = $type;
        } else {
            switch ($type) {
                case 'created':
                    $status_code = Res::HTTP_CREATED;
                    break;
                case 'accepted':
                    $status_code = Res::HTTP_ACCEPTED;
                    break;
                case 'notfound':
                    $status_code = Res::HTTP_NOT_FOUND;
                    break;
                case 'conflict':
                    $status_code = Res::HTTP_CONFLICT;
                    break;
                case 'badrequest':
                    $status_code = Res::HTTP_BAD_REQUEST;
                    break;
                case 'exception':
                    $status_code = Res::HTTP_UNPROCESSABLE_ENTITY;
                    break;
                case 'unauthenticated':
                case 'unauthorized':
                    $status_code = Res::HTTP_UNAUTHORIZED;
                    break;
                default:
                    $status_code = Res::HTTP_OK;
                    break;
            }
        }
        return $status_code;
    }

    /**
     * Set status from status_code
     * @param int $status_code
     * @return bool
     */
    private function setStatus(int $status_code = Res::HTTP_OK): bool
    {
        return in_array($status_code, config('response.apiSuccessCodes', []));
    }

    /**
     * Set message from status_code
     * @param int $status_code
     * @return string
     */
    private function setMessage(int $status_code = Res::HTTP_OK): string
    {
        switch ($status_code) {
            case Res::HTTP_OK:
                $message = 'OK';
                break;
            case Res::HTTP_CREATED:
                $message = 'Created';
                break;
            case Res::HTTP_ACCEPTED:
                $message = 'Accepted';
                break;
            case Res::HTTP_NOT_FOUND:
                $message = 'Not found!';
                break;
            case Res::HTTP_INTERNAL_SERVER_ERROR:
                $message = 'Internal server error!';
                break;
            case Res::HTTP_UNPROCESSABLE_ENTITY:
                $message = 'Unprocessable entity!';
                break;
            case Res::HTTP_UNAUTHORIZED:
                $message = 'Unauthorized!';
                break;
            case Res::HTTP_NO_CONTENT:
                $message = 'No content!';
                break;
            case Res::HTTP_BAD_REQUEST:
                $message = 'Bad Request!';
                break;
            case Res::HTTP_CONFLICT:
                $message = 'Conflict!';
                break;

            default:
                $message = 'Error';
                break;
        }

        return $message;
    }

    /**
     * Remove null values from array
     * @param $array
     * @param string $callback
     * @return mixed
     */
    private function removeNullArrayValues($array, string $callback = '')
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
}
