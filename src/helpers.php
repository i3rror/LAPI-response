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
    function apiOk(mixed $data = null, ?string $message = null)
    {
        return ApiResponse::apiOk($data, $message);
    }
}

if (!function_exists('apiNotFound')) {
    /**
     * Handle an API not found response.
     *
     * @param array|string $errors The error details or messages.
     * @param string|null $message An optional custom error message.
     * @param bool $throw_exception Whether to throw an exception or not.
     * @param string|int|\UnitEnum|null $errorCode An optional error code.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiNotFound(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null
    )
    {
        return ApiResponse::apiNotFound($errors, $message, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiBadRequest')) {
    /**
     * Handle an API bad request response.
     *
     * @param array|string $errors The errors associated with the bad request.
     * @param string|null $message An optional custom error message.
     * @param bool $throw_exception Whether to throw an exception for the bad request.
     * @param string|int|UnitEnum|null $errorCode A specific error code to associate with the bad request.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiBadRequest(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null
    )
    {
        return ApiResponse::apiBadRequest($errors, $message, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiException')) {
    /**
     * Handle and generate an API exception response.
     *
     * @param array|string $errors List of errors or error message.
     * @param string|null $message An optional custom error message.
     * @param bool $throw_exception Indicates whether to throw the exception.
     * @param string|int|UnitEnum|null $errorCode Specific error code for the exception.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiException(
        array|string             $errors = [],
        ?string                  $message = null,
        bool                     $throw_exception = true,
        string|int|UnitEnum|null $errorCode = null
    )
    {
        return ApiResponse::apiException($errors, $message, $throw_exception, $errorCode);
    }
}

if (!function_exists('apiUnauthenticated')) {
    /**
     * Create an unauthenticated API response.
     *
     * @param string|null $message The error message or messages to include in the response.
     * @param array|string $errors The specific error details to include in the response.
     * @param string|int|UnitEnum|null $errorCode The error code to associate with the response.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiUnauthenticated(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null
    )
    {
        return ApiResponse::apiUnauthenticated($message, $errors, $errorCode);
    }
}

if (!function_exists('apiForbidden')) {
    /**
     * Generates an API response indicating a forbidden action.
     *
     * @param string|null $message The custom error message, if any.
     * @param array|string $errors A list of errors or a single error message.
     * @param string|int|\UnitEnum|null $errorCode The specific error code, if applicable.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiForbidden(
        ?string                  $message = null,
        array|string             $errors = [],
        string|int|UnitEnum|null $errorCode = null
    )
    {
        return ApiResponse::apiForbidden($message, $errors, $errorCode);
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
        return ApiResponse::apiPaginate($pagination, $appends, $reverse_data);
    }
}

if (!function_exists('apiValidate')) {
    /**
     * Validate input data using the provided rules and messages, with optional attribute names.
     *
     * @param array|\Illuminate\Http\Request $request The input data to be validated.
     * @param array $rules The validation rules to apply to the data.
     * @param array $messages Optional custom error messages.
     * @param array $attributes Optional custom attribute names.
     * @return \Illuminate\Http\JsonResponse
     */
    function apiValidate(array|Request $request, array $rules, array $messages = [], array $attributes = [])
    {
        return ApiResponse::apiValidate($request, $rules, $messages, $attributes);
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
        return ApiResponse::apiDD($data);
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
        return ApiResponse::apiStreamResponse($generator, $message, $statusCode);
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
        return ApiResponse::apiResponse($arg, $data, $guards);
    }
}
