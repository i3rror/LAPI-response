<?php

namespace MA\LaravelApiResponse\Tests;

use MA\LaravelApiResponse\Exceptions\Handler;
use MA\LaravelApiResponse\Providers\APIResponseProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            APIResponseProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set up the response configuration
        $app['config']->set('response.removeNullDataValues', false);
        $app['config']->set('response.setNullEmptyData', true);
        $app['config']->set('response.returnValidationErrorsKeys', true);
        $app['config']->set('response.apiSuccessCodes', [200, 201, 202]);
        $app['config']->set('response.apiExceptionCodes', [409, 422, 400, 401, 403]);
        $app['config']->set('response.enableErrorCodes', true);
        $app['config']->set('response.errorCodes', \MA\LaravelApiResponse\Enums\ErrorCodesEnum::class);
        $app['config']->set('response.errorCodesType', 'string');
        $app['config']->set('response.returnDefaultErrorCodes', true);
        $app['config']->set('response.hideMetaPaginationLinks', true);
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', Handler::class);
    }
}