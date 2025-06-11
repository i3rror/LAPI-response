<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use MA\LaravelApiResponse\Services\APIResponseService;
use MA\LaravelApiResponse\Tests\TestCase;

class APIResponsePaginationTest extends TestCase
{
    protected APIResponseService $apiResponse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiResponse = new APIResponseService();
    }

    /** @test */
    public function it_returns_paginated_response()
    {
        // Create a sample collection to paginate
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
            ['id' => 3, 'name' => 'Item 3'],
            ['id' => 4, 'name' => 'Item 4'],
            ['id' => 5, 'name' => 'Item 5'],
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

        // Test the apiPaginate method
        $response = $this->apiResponse->apiPaginate($paginator);

        // Assert response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert the response structure
        $this->assertTrue($responseData['status']);
        $this->assertEquals(200, $responseData['statusCode']);
        $this->assertArrayHasKey('timestamp', $responseData);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertCount(2, $responseData['data']); // Should have 2 items per page

        // Assert pagination metadata
        $this->assertArrayHasKey('pagination', $responseData);
        $this->assertArrayHasKey('meta', $responseData['pagination']);
        $this->assertArrayHasKey('page', $responseData['pagination']['meta']);
        
        $pageMeta = $responseData['pagination']['meta']['page'];
        $this->assertEquals(1, $pageMeta['current']);
        $this->assertEquals(1, $pageMeta['first']);
        $this->assertEquals(3, $pageMeta['last']);
        $this->assertEquals(2, $pageMeta['next']);
        $this->assertNull($pageMeta['previous']);
        $this->assertEquals(2, $pageMeta['per']);
        $this->assertEquals(1, $pageMeta['from']);
        $this->assertEquals(2, $pageMeta['to']);
        $this->assertEquals(2, $pageMeta['count']);
        $this->assertEquals(5, $pageMeta['total']);
        $this->assertTrue($pageMeta['isFirst']);
        $this->assertFalse($pageMeta['isLast']);
        $this->assertTrue($pageMeta['isNext']);
        $this->assertFalse($pageMeta['isPrevious']);
    }

    /** @test */
    public function it_returns_paginated_response_with_appends()
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

        // Test the apiPaginate method with appends
        $appends = ['extra' => 'data'];
        $response = $this->apiResponse->apiPaginate($paginator, $appends);

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert the appended data is present
        $this->assertArrayHasKey('extra', $responseData);
        $this->assertEquals('data', $responseData['extra']);
    }

    /** @test */
    public function it_returns_paginated_response_with_reversed_data()
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

        // Test the apiPaginate method with reversed data
        $response = $this->apiResponse->apiPaginate($paginator, [], true);

        // Decode the response
        $responseData = json_decode($response->getContent(), true);

        // Assert the data is reversed
        $this->assertEquals(2, $responseData['data'][0]['id']);
        $this->assertEquals(1, $responseData['data'][1]['id']);
    }
}