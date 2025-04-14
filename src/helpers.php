<?php

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Date;
use MA\LaravelApiResponse\Facades\ApiResponse;

if (!function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param \DateTimeZone|string|null $tz
     * @return \Illuminate\Support\Carbon
     */
    function now($tz = null)
    {
        return Date::now($tz);
    }
}

if (!function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param \Illuminate\Contracts\View\View|string|array|null $content
     * @param int $status
     * @param array $headers
     * @return ($content is null ? \Illuminate\Contracts\Routing\ResponseFactory : \Illuminate\Http\Response)
     */
    function response($content = null, $status = 200, array $headers = [])
    {
        if (class_exists('\Laravel\Lumen\Http\ResponseFactory')) {
            $factory = new Laravel\Lumen\Http\ResponseFactory;
        } else {
            $factory = app(\Illuminate\Contracts\Routing\ResponseFactory::class);
        }

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content ?? '', $status, $headers);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array<string, mixed>|string|null $key
     * @param mixed $default
     * @return ($key is null ? \Illuminate\Config\Repository : ($key is string ? mixed : null))
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (!function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param string|null $to
     * @param int $status
     * @param array $headers
     * @param bool|null $secure
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (!function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, $parameters, $absolute);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->configPath($path);
    }
}

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @template TClass
     *
     * @param string|class-string<TClass>|null $abstract
     * @param array $parameters
     * @return ($abstract is class-string<TClass> ? TClass : ($abstract is null ? \Illuminate\Foundation\Application : mixed))
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->path($path);
    }
}

if (!function_exists('apiOk')) {
    /**
     * Handle a successful API response.
     *
     * @param mixed|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    function apiOk(mixed $data = null)
    {
        return APIResponse::apiOk($data);
    }
}

if (!function_exists('apiNotFound')) {
    /**
     * Handle an API not found response.
     *
     * @param array|string|null $errors Errors or messages describing the issue.
     * @param bool $throw_exception Indicates if an exception should be thrown.
     * @param string|int|UnitEnum|null $errorCode Custom error code associated with the response.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiNotFound(array|string|null $errors = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiNotFound($errors, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiBadRequest')) {
    /**
     * Handle an API bad request response.
     *
     * @param array|string|null $errors The errors associated with the bad request.
     * @param bool $throw_exception Whether to throw an exception for the bad request.
     * @param string|int|\UnitEnum|null $errorCode A specific error code to associate with the bad request.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiBadRequest(array|string|null $errors = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiBadRequest($errors, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiException')) {
    /**
     * Handle and generate an API exception response.
     *
     * @param array|string|null $errors List of errors or error message.
     * @param bool $throw_exception Indicates whether to throw the exception.
     * @param string|int|UnitEnum|null $errorCode Specific error code for the exception.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiException(array|string|null $errors = null, bool $throw_exception = true, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiException($errors, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiUnauthenticated')) {
    /**
     * Create an unauthenticated API response.
     *
     * @param array|string|null $message The error message or messages to include in the response.
     * @param array|string|null $errors The specific error details to include in the response.
     * @param string|int|UnitEnum|null $errorCode The error code to associate with the response.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiUnauthenticated(array|string|null $message = null, array|string $errors = null, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiUnauthenticated($message, $errors, $errorCode);
    }
}

if (!function_exists('apiUnauthenticated')) {
    /**
     * Handle an unauthenticated API response with a forbidden status.
     *
     * @param array|string|null $message The error message or messages to include in the response.
     * @param array|string|null $errors Specific error details to include in the response.
     * @param string|int|\UnitEnum|null $errorCode The error code associated with the response.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiUnauthenticated(array|string|null $message = null, array|string $errors = null, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiForbidden($message, $errors, $errorCode);
    }
}

if (!function_exists('apiForbidden')) {
    /**
     * Generate a response indicating that the requested API action is forbidden.
     *
     * @param array|string|null $message Optional message providing additional context about the forbidden action.
     * @param array|string|null $errors Optional detailed error information.
     * @param string|int|\UnitEnum|null $errorCode Optional error code associated with the forbidden response.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiForbidden(array|string|null $message = null, array|string $errors = null, string|int|UnitEnum|null $errorCode = null)
    {
        return APIResponse::apiForbidden($message, $errors, $errorCode);
    }
}

if (!function_exists('apiPaginate')) {
    /**
     * Paginate data for API responses.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Http\Resources\Json\AnonymousResourceCollection $pagination
     * @param array $appends
     * @param bool $reverse_data
     * @return \Illuminate\Http\JsonResponse
     */
    function apiPaginate(LengthAwarePaginator|AnonymousResourceCollection $pagination, array $appends = [], bool $reverse_data = false)
    {
        return APIResponse::apiPaginate($pagination, $appends, $reverse_data);
    }
}

if (!function_exists('apiValidate')) {
    /**
     * Validate input data using the provided rules and messages, with optional attribute names.
     *
     * @param array|\Illuminate\Http\Request $data The input data to be validated.
     * @param array $rules The validation rules to apply to the data.
     * @param array $messages Optional custom error messages.
     * @param array $attributes Optional custom attribute names.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiValidate(array|Request $data, array $rules, array $messages = [], array $attributes = [])
    {
        return APIResponse::apiValidate($data, $rules, $messages, $attributes);
    }
}

if (!function_exists('apiDD')) {
    /**
     * Dump and die the provided data using the API response format.
     *
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function apiDD(mixed $data)
    {
        return APIResponse::apiDD($data);
    }
}

if (!function_exists('apiStreamResponse')) {
    /**
     * Stream an API response using a generator.
     *
     * @param \Generator $generator The generator providing the streamable data.
     * @param string|null $message An optional message to include in the response.
     * @param int $statusCode The HTTP status code to use for the response.
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedJsonResponse
     */
    function apiStreamResponse(Generator $generator, ?string $message = null, int $statusCode = Response::HTTP_OK)
    {
        return APIResponse::apiStreamResponse($generator, $message, $statusCode);
    }
}

if (!function_exists('apiResponse')) {
    /**
     * Generate an API response.
     *
     * @param array|string|null $arg The argument used for the response, can be an array, string, or null.
     * @param mixed $data The data to include in the response.
     * @param array $guards An array of guards to apply to the response.
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedJsonResponse
     */
    function apiResponse(array|string|null $arg = null, mixed $data = null, array $guards = [])
    {
        return APIResponse::apiResponse($arg, $data, $guards);
    }
}
