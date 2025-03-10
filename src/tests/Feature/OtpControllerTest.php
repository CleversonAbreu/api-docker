<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\OtpController;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;

class OtpControllerTest extends TestCase
{
    protected $otpController;
    protected $otpServiceMock;

    public function setUp(): void
    {
        parent::setUp();
        
        // Creating the mock for OtpService
        $this->otpServiceMock = Mockery::mock(OtpService::class);
        
        // Injecting the mock into the controller
        $this->otpController = new OtpController($this->otpServiceMock);
    }

    public function testGenerateOtp()
    {
        // Input data for the method
        $input = [
            'phone_or_email' => 'cre1@gmail.com',
            'type_generate' => 'CHANGE_PASSWORD'
        ];
        
        // Expecting the generateOtp method to be called once with the input data
        $this->otpServiceMock
            ->shouldReceive('generateOtp')
            ->once()
            ->with($input)
            ->andReturn(new JsonResponse(['message' => 'OTP code generated successfully'], 200));

        // Creating the simulated request
        $request = Request::create('/api/otp/generate', 'POST', $input);

        // Calling the method
        $response = $this->otpController->generate($request);

        // Verifying the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['message' => 'OTP code generated successfully'], $response->getData(true));
    }

    public function testValidateOtp()
    {
        // Input data for the method
        $input = [
            'phone_or_email' => 'cre1@gmail.com',
            'otp_code' => '959120'
        ];
        
        // Expecting the validateOtp method to be called once with the input data
        $this->otpServiceMock
            ->shouldReceive('validateOtp')
            ->once()
            ->with($input)
            ->andReturn(new JsonResponse(['message' => 'OTP code validated successfully'], 200));

        // Creating the simulated request
        $request = Request::create('/api/otp/validate', 'POST', $input);

        // Calling the method
        $response = $this->otpController->validateOtp($request);

        // Verifying the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['message' => 'OTP code validated successfully'], $response->getData(true));
    }

    public function tearDown(): void
    {
        Mockery::close(); // Closing the mock after the test
    }
}
