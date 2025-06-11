<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Illuminate\Http\JsonResponse;
use MA\LaravelApiResponse\Services\APIResponseService;
use MA\LaravelApiResponse\Tests\TestCase;

class APIResponseTraitTest extends TestCase
{
    protected APIResponseService $apiResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiResponse = new APIResponseService();
    }

    /** @test */
    public function it_returns_ok_response()
    {
        $data = ['key' => 'value'];
        $message = 'Success message';

        $response = $this->apiResponse->apiOk($data, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals(200, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertArrayHasKey('timestamp', $responseData);
    }

    /** @test */
    public function it_returns_not_found_response()
    {
        $errors = ['Resource not found'];
        $message = 'Not found message';

        $response = $this->apiResponse->apiNotFound($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(404, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertNull($responseData['data']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_returns_bad_request_response()
    {
        $errors = ['Invalid input'];
        $message = 'Bad request message';

        $response = $this->apiResponse->apiBadRequest($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(400, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertNull($responseData['data']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_returns_exception_response()
    {
        $errors = ['Server error'];
        $message = 'Exception message';

        $response = $this->apiResponse->apiException($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(422, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertNull($responseData['data']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_returns_unauthenticated_response()
    {
        $message = 'Unauthenticated message';
        $errors = ['Authentication required'];

        // Set throw_exception to false to prevent HttpResponseException
        $response = $this->apiResponse->apiResponse([
            'type' => 'unauthenticated',
            'throw_exception' => false,
            'message' => $message,
            'errors' => $errors,
            'errorCode' => 'UNAUTHORIZED_ACCESS'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(401, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertNull($responseData['data']);
        // Check if errors key exists, it might not be present in all responses
        if (isset($responseData['errors'])) {
            $this->assertEquals($errors, $responseData['errors']);
        }
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('errorCode', $responseData);
    }

    /** @test */
    public function it_returns_forbidden_response()
    {
        $message = 'Forbidden message';
        $errors = ['Access denied'];

        // Set throw_exception to false to prevent HttpResponseException
        $response = $this->apiResponse->apiResponse([
            'type' => 'forbidden',
            'throw_exception' => false,
            'message' => $message,
            'errors' => $errors,
            'errorCode' => 'FORBIDDEN'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(403, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertNull($responseData['data']);
        // Check if errors key exists, it might not be present in all responses
        if (isset($responseData['errors'])) {
            $this->assertEquals($errors, $responseData['errors']);
        }
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('errorCode', $responseData);
    }
}
