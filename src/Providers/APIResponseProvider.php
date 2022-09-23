<?php

namespace MA\LaravelApiResponse\Providers;

use Illuminate\Support\ServiceProvider;

class APIResponseProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/response.php' => config_path('response.php'),
            ], 'config');
        }
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \MA\LaravelApiResponse\Exceptions\Handler::class
        );
    }
}
