<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MA\LaravelApiResponse\Services\APIResponseService;
use MA\LaravelApiResponse\Tests\TestCase;

class APIResponseValidationTest extends TestCase
{
    protected APIResponseService $apiResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiResponse = new APIResponseService();
    }

    /** @test */
    public function it_validates_valid_data()
    {
        // Create test data
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
        ];

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];

        // Validate the data
        $result = $this->apiResponse->apiValidate($data, $rules);

        // Assert the result is an array of validated data
        $this->assertIsArray($result);
        $this->assertEquals($data, $result);
    }

    /** @test */
    public function it_returns_validation_errors_for_invalid_data()
    {
        $this->expectException(HttpResponseException::class);

        // Create test data with invalid values
        $data = [
            'name' => '', // Empty name (required)
            'email' => 'not-an-email', // Invalid email format
            'age' => 16, // Below minimum age
        ];

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];

        // Validate the data
        $response = $this->apiResponse->apiValidate($data, $rules);

        // Assert the response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert the response structure
        $this->assertFalse($responseData['status']);
        $this->assertEquals(400, $responseData['statusCode']);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertNull($responseData['data']);
        $this->assertArrayHasKey('errors', $responseData);

        // Assert validation error messages
        $this->assertArrayHasKey('errors', $responseData);
        // The structure might vary, so check if there's a nested 'errors' key
        $errors = isset($responseData['errors']['errors']) ? 
            $responseData['errors']['errors'] : 
            $responseData['errors'];

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('age', $errors);
    }

    /** @test */
    public function it_validates_request_object()
    {
        // Create a mock request
        $request = Request::create('/test', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
        ]);

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];

        // Validate the request
        $result = $this->apiResponse->apiValidate($request, $rules);

        // Assert the result is an array of validated data
        $this->assertIsArray($result);
        $this->assertEquals($request->all(), $result);
    }

    /** @test */
    public function it_validates_with_custom_messages()
    {
        $this->expectException(HttpResponseException::class);

        // Create test data with invalid values
        $data = [
            'name' => '', // Empty name (required)
            'email' => 'not-an-email', // Invalid email format
            'age' => 16, // Below minimum age
        ];

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];

        // Define custom error messages
        $messages = [
            'name.required' => 'The name field is mandatory.',
            'email.email' => 'Please provide a valid email address.',
            'age.min' => 'You must be at least 18 years old.',
        ];

        // Validate the data
        $response = $this->apiResponse->apiValidate($data, $rules, $messages);

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert custom validation error messages
        $this->assertArrayHasKey('errors', $responseData);
        // The structure might vary, so check if there's a nested 'errors' key
        $errors = isset($responseData['errors']['errors']) ? 
            $responseData['errors']['errors'] : 
            $responseData['errors'];

        $this->assertStringContainsString('mandatory', $errors['name'][0]);
        $this->assertStringContainsString('valid email', $errors['email'][0]);
        $this->assertStringContainsString('18 years old', $errors['age'][0]);
    }

    /** @test */
    public function it_validates_with_custom_attributes()
    {
        $this->expectException(HttpResponseException::class);

        // Create test data with invalid values
        $data = [
            'name' => '', // Empty name (required)
            'email' => 'not-an-email', // Invalid email format
            'age' => 16, // Below minimum age
        ];

        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'age' => 'required|integer|min:18',
        ];

        // Define custom attribute names
        $attributes = [
            'name' => 'full name',
            'email' => 'email address',
            'age' => 'user age',
        ];

        // Validate the data
        $response = $this->apiResponse->apiValidate($data, $rules, [], $attributes);

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert validation error messages use custom attribute names
        $this->assertArrayHasKey('errors', $responseData);
        // The structure might vary, so check if there's a nested 'errors' key
        $errors = isset($responseData['errors']['errors']) ? 
            $responseData['errors']['errors'] : 
            $responseData['errors'];

        $this->assertStringContainsString('full name', $errors['name'][0]);
        $this->assertStringContainsString('email address', $errors['email'][0]);
        $this->assertStringContainsString('user age', $errors['age'][0]);
    }
}
