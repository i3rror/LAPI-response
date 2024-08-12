<?php

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Date;

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