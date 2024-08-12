<?php

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

if (!function_exists('now')) {
    /**
     * Create a new Carbon instance for the current time.
     *
     * @param DateTimeZone|string|null $tz
     * @return Carbon
     */
    function now(DateTimeZone|string|null $tz = null): Carbon
    {
        return Date::now($tz);
    }
}

if (!function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param array|string|View|null $content
     * @param int $status
     * @param array $headers
     * @return Response|Application|ResponseFactory
     * @throws BindingResolutionException
     */
    function response(View|array|string|null $content = '', int $status = 200, array $headers = []): Response|Application|ResponseFactory
    {
        $factory = app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}


if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param null $key
     * @param mixed|null $default
     * @return mixed|Repository
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function config($key = null, mixed $default = null): mixed
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


if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|Application
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     */
    function app(string $abstract = null, array $parameters = []): mixed
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}