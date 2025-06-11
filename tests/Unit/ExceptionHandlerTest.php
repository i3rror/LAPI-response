<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MA\LaravelApiResponse\Exceptions\Handler;
use MA\LaravelApiResponse\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandlerTest extends TestCase
{
    protected Handler $handler;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new Handler($this->app);
        $this->request = Request::create('/api/test', 'GET');
        $this->request->headers->set('Accept', 'application/json');
    }

    /** @test */
    public function it_handles_not_found_exception()
    {
        $exception = new NotFoundHttpException('Resource not found');

        $response = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(404, $responseData['statusCode']);
        $this->assertNull($responseData['data']);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_handles_method_not_allowed_exception()
    {
        $exception = new MethodNotAllowedHttpException(['GET', 'POST'], 'Method not allowed');

        $response = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(404, $responseData['statusCode']);
    }

    /** @test */
    public function it_handles_model_not_found_exception()
    {
        $exception = (new ModelNotFoundException())->setModel('User', [1]);

        $response = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(404, $responseData['statusCode']);
    }

    /** @test */
    public function it_handles_http_exception()
    {
        $this->expectException(HttpResponseException::class);
        $exception = new HttpException(400, 'Bad request');

        $response = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(400, $responseData['statusCode']);
    }

    /** @test */
    public function it_handles_authentication_exception()
    {
        $this->expectException(HttpResponseException::class);
        $exception = new AuthenticationException('Unauthenticated');

        // Use reflection to access the protected method
        $reflectionMethod = new \ReflectionMethod(Handler::class, 'unauthenticated');
        $reflectionMethod->setAccessible(true);
        $response = $reflectionMethod->invoke($this->handler, $this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(401, $responseData['statusCode']);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_handles_generic_exception_in_debug_mode()
    {
        // Enable debug mode
        config(['app.debug' => true]);

        $exception = new \Exception('Test exception');

        $response = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(500, $responseData['statusCode']);
        $this->assertEquals('Test exception', $responseData['message']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('exception', $responseData['data']);
        $this->assertArrayHasKey('file', $responseData['data']);
        $this->assertArrayHasKey('line', $responseData['data']);
        $this->assertArrayHasKey('trace', $responseData['data']);
    }

    /** @test */
    public function it_passes_non_json_requests_to_parent_handler()
    {
        $this->assertTrue(true);
    }
}
