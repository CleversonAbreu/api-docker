<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\OtpService;
use App\Services\UserService;
use App\Repositories\OtpRepository;
use App\Exceptions\EmailNotFoundException;

class OtpServiceTest extends TestCase
{
    private $userServiceMock;
    private $otpRepositoryMock;
    private $otpService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userServiceMock = $this->createMock(UserService::class);
        $this->otpRepositoryMock = $this->createMock(OtpRepository::class);

    
        $this->otpService = new OtpService(
            $this->otpRepositoryMock,
            $this->userServiceMock
        );
    }

    public function testGenerateOtpSuccess()
    {
      
        $this->userServiceMock
            ->method('findUserByEmail')
            ->with('user@example.com')
            ->willReturn(['id' => 1, 'email' => 'user@example.com']);

        $this->otpRepositoryMock
            ->method('saveOtp')
            ->willReturn(true);

 
        $result = $this->otpService->generateOtp('user@example.com');

   
        $this->assertTrue($result);
    }

    public function testGenerateOtpThrowsEmailNotFoundException()
    {
        $this->userServiceMock
            ->method('findUserByEmail')
            ->with('invalid@example.com')
            ->willReturn(null);

        $this->expectException(EmailNotFoundException::class);

        $this->otpService->generateOtp('invalid@example.com');
    }

    public function testGenerateOtpFailsToSend()
    {
        $this->userServiceMock
            ->method('findUserByEmail')
            ->with('user@example.com')
            ->willReturn(['id' => 1, 'email' => 'user@example.com']);

        $this->otpRepositoryMock
            ->method('saveOtp')
            ->willReturn(false);

        $result = $this->otpService->generateOtp('user@example.com');

        $this->assertFalse($result);
    }

    public function testValidateOtpSuccess()
    {
        $this->otpRepositoryMock
            ->method('getOtpByCode')
            ->with('123456')
            ->willReturn(['id' => 1, 'code' => '123456', 'email' => 'user@example.com']);

        $result = $this->otpService->validateOtp('123456');

        $this->assertTrue($result);
    }
}
