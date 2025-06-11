<?php

namespace MA\LaravelApiResponse\Tests\Unit;

use Exception;
use MA\LaravelApiResponse\Enums\ErrorCodesEnum;
use MA\LaravelApiResponse\Tests\TestCase;

class ErrorCodesEnumTest extends TestCase
{
    /** @test */
    public function it_returns_error_code_by_name()
    {
        $errorCode = ErrorCodesEnum::getProperty('VALIDATION_FAILED');
        
        $this->assertInstanceOf(ErrorCodesEnum::class, $errorCode);
        $this->assertEquals(ErrorCodesEnum::VALIDATION_FAILED, $errorCode);
        $this->assertEquals('VALIDATION_FAILED', $errorCode->name);
        $this->assertEquals(1001, $errorCode->value);
    }

    /** @test */
    public function it_returns_error_code_by_value()
    {
        $errorCode = ErrorCodesEnum::getProperty(1001);
        
        $this->assertInstanceOf(ErrorCodesEnum::class, $errorCode);
        $this->assertEquals(ErrorCodesEnum::VALIDATION_FAILED, $errorCode);
        $this->assertEquals('VALIDATION_FAILED', $errorCode->name);
        $this->assertEquals(1001, $errorCode->value);
    }

    /** @test */
    public function it_throws_exception_for_invalid_error_code()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Enum 'MA\LaravelApiResponse\Enums\ErrorCodesEnum::INVALID_CODE' not found!");
        
        ErrorCodesEnum::getProperty('INVALID_CODE');
    }

    /** @test */
    public function it_has_all_required_error_codes()
    {
        // Test a sample of important error codes
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1000)); // INVALID_CREDENTIALS
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1001)); // VALIDATION_FAILED
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1002)); // UNAUTHORIZED_ACCESS
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1003)); // RESOURCE_NOT_FOUND
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1004)); // SERVER_ERROR
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1005)); // BAD_REQUEST
        $this->assertNotNull(ErrorCodesEnum::tryFrom(1006)); // FORBIDDEN
    }

    /** @test */
    public function it_returns_correct_error_code_values()
    {
        $this->assertEquals(1000, ErrorCodesEnum::INVALID_CREDENTIALS->value);
        $this->assertEquals(1001, ErrorCodesEnum::VALIDATION_FAILED->value);
        $this->assertEquals(1002, ErrorCodesEnum::UNAUTHORIZED_ACCESS->value);
        $this->assertEquals(1003, ErrorCodesEnum::RESOURCE_NOT_FOUND->value);
        $this->assertEquals(1004, ErrorCodesEnum::SERVER_ERROR->value);
        $this->assertEquals(1005, ErrorCodesEnum::BAD_REQUEST->value);
        $this->assertEquals(1006, ErrorCodesEnum::FORBIDDEN->value);
    }
}