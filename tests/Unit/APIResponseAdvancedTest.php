<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Generator;
use Illuminate\Http\JsonResponse;
use MA\LaravelApiResponse\Services\APIResponseService;
use MA\LaravelApiResponse\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;

class APIResponseAdvancedTest extends TestCase
{
    protected APIResponseService $apiResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiResponse = new APIResponseService();
    }

    /** @test */
    public function it_returns_debug_response()
    {
        $data = ['debug' => 'information', 'nested' => ['key' => 'value']];

        // Set throw_exception to false to prevent HttpResponseException
        $response = $this->apiResponse->apiResponse([
            'type' => 'Exception',
            'throw_exception' => false,
            'message' => 'Die and dump',
            'data' => $data
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(422, $responseData['statusCode']);
        $this->assertEquals('Die and dump', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertArrayHasKey('timestamp', $responseData);
    }

    /** @test */
    public function it_returns_stream_response()
    {
        $generator = $this->createGenerator();
        $message = 'Streaming data';

        $response = $this->apiResponse->apiStreamResponse($generator, $message);

        $this->assertInstanceOf(StreamedJsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // We can't easily test the streamed content, but we can verify the response is a StreamedJsonResponse
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_returns_custom_response()
    {
        $data = ['custom' => 'data'];
        $message = 'Custom message';
        $statusCode = 201;

        $response = $this->apiResponse->apiResponse([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals($statusCode, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertArrayHasKey('timestamp', $responseData);
    }

    /** @test */
    public function it_returns_response_with_extra_data()
    {
        $data = ['main' => 'data'];
        $extra = [
            'extra' => [
                'nested' => 'value',
                'array' => [1, 2, 3]
            ]
        ];

        $response = $this->apiResponse->apiResponse([
            'data' => $data,
            'extra' => $extra,
        ]);

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($data, $responseData['data']);
        $this->assertEquals('value', $responseData['extra']['nested']);
        $this->assertEquals([1, 2, 3], $responseData['extra']['array']);
    }

    /** @test */
    public function it_returns_response_with_error_code()
    {
        $response = $this->apiResponse->apiResponse([
            'type' => 'badrequest',
            'throw_exception' => false,
            'message' => 'Bad request with error code',
            'errorCode' => 'BAD_REQUEST',
        ]);

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(400, $responseData['statusCode']);
        $this->assertEquals('Bad request with error code', $responseData['message']);
        $this->assertArrayHasKey('errorCode', $responseData);
        $this->assertEquals('BAD_REQUEST', $responseData['errorCode']);
    }

    /** @test */
    public function it_returns_response_with_filtered_data()
    {
        $data = [
            'name' => 'John',
            'email' => 'john@example.com',
            'empty_string' => '',
            'null_value' => null,
        ];

        $response = $this->apiResponse->apiResponse([
            'data' => $data,
            'filter_data' => true,
        ]);

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $responseData['data']);
        $this->assertArrayHasKey('email', $responseData['data']);
        $this->assertArrayNotHasKey('empty_string', $responseData['data']);
        $this->assertArrayNotHasKey('null_value', $responseData['data']);
    }

    /**
     * Create a sample generator for testing stream responses
     */
    private function createGenerator(): Generator
    {
        for ($i = 0; $i < 5; $i++) {
            yield ['id' => $i, 'value' => "Item $i"];
        }
    }
}
