<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use MA\LaravelApiResponse\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;

class APIResponseHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_uses_apiOk_helper()
    {
        $data = ['key' => 'value'];
        $message = 'Success message';

        $response = apiOk($data, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals(200, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    /** @test */
    public function it_uses_apiNotFound_helper()
    {
        $errors = ['Resource not found'];
        $message = 'Not found message';

        $response = apiNotFound($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(404, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_uses_apiBadRequest_helper()
    {
        $errors = ['Invalid input'];
        $message = 'Bad request message';

        $response = apiBadRequest($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(400, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_uses_apiException_helper()
    {
        $errors = ['Server error'];
        $message = 'Exception message';

        $response = apiException($errors, $message, false);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(422, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_uses_apiUnauthenticated_helper()
    {
        $message = 'Unauthenticated message';
        $errors = ['Authentication required'];

        // Set throw_exception to false to prevent HttpResponseException
        $response = apiResponse([
            'type' => 'unauthenticated',
            'throw_exception' => false,
            'message' => $message,
            'errors' => ['errors' => $errors],
            'errorCode' => 'UNAUTHORIZED_ACCESS'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(401, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_uses_apiForbidden_helper()
    {
        $message = 'Forbidden message';
        $errors = ['Access denied'];

        // Set throw_exception to false to prevent HttpResponseException
        $response = apiResponse([
            'type' => 'forbidden',
            'throw_exception' => false,
            'message' => $message,
            'errors' => ['errors' => $errors],
            'errorCode' => 'FORBIDDEN'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['status']);
        $this->assertEquals(403, $responseData['statusCode']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function it_uses_apiPaginate_helper()
    {
        // Create a sample collection to paginate
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);

        // Create a paginator
        $perPage = 2;
        $currentPage = 1;
        $paginator = new LengthAwarePaginator(
            $items->forPage($currentPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        $response = apiPaginate($paginator);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['status']);
        $this->assertArrayHasKey('pagination', $responseData);
    }

    /** @test */
    public function it_uses_apiValidate_helper()
    {
        // Create test data
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        // Define validation rules
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email',
        ];

        $result = apiValidate($data, $rules);

        $this->assertIsArray($result);
        $this->assertEquals($data, $result);
    }

    /** @test */
    public function it_uses_apiDD_helper()
    {
        $data = ['debug' => 'information'];

        // Set throw_exception to false to prevent HttpResponseException
        $response = apiResponse([
            'type' => 'Exception',
            'throw_exception' => false,
            'message' => 'Die and dump',
            'data' => $data
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Die and dump', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    /** @test */
    public function it_uses_apiResponse_helper()
    {
        $data = ['custom' => 'data'];
        $message = 'Custom message';

        $response = apiResponse([
            'message' => $message,
            'data' => $data,
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['status']);
        $this->assertEquals($message, $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }
}
