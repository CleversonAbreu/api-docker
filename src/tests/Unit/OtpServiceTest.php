<?php

namespace Tests\Unit\Services;

use App\Services\OtpService;
use App\Repositories\OtpRepository;
use App\Services\UserService;
use App\Exceptions\OtpException\FailedToGenerateOtpException;
use App\Exceptions\OtpException\FailedToSendOtpException;
use App\Exceptions\OtpException\InvalidOtpException;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    protected $otpRepository;
    protected $userService;
    protected $otpService;

    public function setUp(): void
    {
        parent::setUp();

        $this->otpRepository = Mockery::mock(OtpRepository::class);
        $this->userService = Mockery::mock(UserService::class);
        $this->otpService = new OtpService($this->otpRepository, $this->userService);
    }

    public function testGenerateOtpSuccess()
    {
        $input = ['phone_or_email' => 'test@example.com', 'type_generate' => 'SIGNUP'];

        // Mocking the user service response
        $this->userService->shouldReceive('verifyEmail')->with('test@example.com')->andReturn(true);

        // Mocking the OTP repository method to return true (successful OTP creation)
        $this->otpRepository->shouldReceive('updateOrCreateOtp')->once()->with('test@example.com', Mockery::any(), Mockery::any());

        // Mocking the OTP send method to return true
        Mail::shouldReceive('raw')->once()->andReturn(true);

        // Execute the service method
        $response = $this->otpService->generateOtp($input);

        // Assert response status and message
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(__('messages.otp_generated'), $response->getContent());
    }

    public function testGenerateOtpFailure_EmailNotFound()
    {
        $input = ['phone_or_email' => 'test@example.com', 'type_generate' => 'CHANGE_PASSWORD'];

        // Mocking the user service response for non-existing email
        $this->userService->shouldReceive('verifyEmail')->with('test@example.com')->andReturn(false);
        
        $response = $this->otpService->generateOtp($input);

        // Assert response status and error message for email not found
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertStringContainsString(__('messages.required_field', ['attribute' => 'phone_or_email']), $response->getContent());
    }

    public function testGenerateOtpFailure_FailedToGenerateOtpException()
    {
        $input = ['phone_or_email' => 'test@example.com'];

        // Mocking the user service response
        $this->userService->shouldReceive('verifyEmail')->with('test@example.com')->andReturn(true);
        
        // Mocking the OTP repository method to throw an exception
        $this->otpRepository->shouldReceive('updateOrCreateOtp')->andThrow(new FailedToGenerateOtpException());
        
        $response = $this->otpService->generateOtp($input);

        // Assert response status and error message for OTP generation failure
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString(__('messages.otp_failed'), $response->getContent());
    }

    public function testValidateOtpSuccess()
    {
        $input = ['phone_or_email' => 'test@example.com', 'otp_code' => '123456'];

        // Mocking OTP repository to return a valid OTP
        $this->otpRepository->shouldReceive('findValidOtp')->with('test@example.com', '123456')->andReturn(true);

        // Mocking OTP repository to delete OTP after validation
        $this->otpRepository->shouldReceive('deleteOtp')->once();

        $response = $this->otpService->validateOtp($input);

        // Assert response status and message
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString(__('messages.otp_validated'), $response->getContent());
    }

    public function testValidateOtpFailure_InvalidOtpException()
    {
        $input = ['phone_or_email' => 'test@example.com', 'otp_code' => '123456'];

        // Mocking OTP repository to return null (OTP not found)
        $this->otpRepository->shouldReceive('findValidOtp')->with('test@example.com', '123456')->andReturn(null);

        $response = $this->otpService->validateOtp($input);

        // Assert response status and error message for invalid OTP
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString(__('messages.otp_invalid'), $response->getContent());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
