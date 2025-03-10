<?php

namespace App\Services;

use App\Api\ApiMessages;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use App\Exceptions\CustomException;
use App\Exceptions\OtpException\EmailNotFoundException;
use App\Exceptions\OtpException\EmailAlreadyExistsException;
use App\Exceptions\OtpException\FailedToGenerateOtpException;
use App\Exceptions\OtpException\FailedToSendOtpException;
use App\Exceptions\OtpException\InvalidOtpException;
use App\Constants\OtpTypeGenerate;

class OtpService
{
    protected $otpRepository;
    protected $userService;

    public function __construct(OtpRepository $otpRepository, UserService $userService)
    {
        $this->otpRepository = $otpRepository;
        $this->userService = $userService;
    }

    public function generateOtp(array $input): JsonResponse
    {
        try {
            // Validate 'phone_or_email'
            if (empty($input['phone_or_email'])) {
                throw new \InvalidArgumentException(__('messages.required_field', ['attribute' => 'phone_or_email']));
            }
    
            $phoneOrEmail = $input['phone_or_email'];
    
            // Verify e-mail and check if exists
            $verificationResult = $this->userService->verifyEmail($phoneOrEmail);
            
            // Validate type_validate
            $this->validateEmailBasedOnType($phoneOrEmail, $input['type_generate'] ?? null);
        
            // Create and save OTP
            $otpCode = $this->createAndSaveOtp($phoneOrEmail);
    
            if (!$otpCode) {
                throw new FailedToGenerateOtpException();
            }
    
            // send otp
            if ($this->sendOtp($phoneOrEmail, $otpCode) === false) {
                throw new FailedToSendOtpException();
            }
    
            // Return success response
            return response()->json([
                'message' => __('messages.otp_generated'),
                'otp' => $otpCode,
            ], 200);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_type' => $e->getErrorType(),
            ], $e->getCode());
        } catch (\InvalidArgumentException $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 422);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), $e->getCode() ?: 400);
        }
    }
    

    public function validateOtp(array $input): JsonResponse
    {
        try {
       
            // Validate 'phone_or_email' and 'otp_code'
            $this->validateInput($input, ['phone_or_email', 'otp_code']);

     
            // Find valid OTP in database
            $otp = $this->otpRepository->findValidOtp($input['phone_or_email'], $input['otp_code']);

            if (!$otp) {
                throw new InvalidOtpException();
            }

            // Delete OTP after use
            $this->otpRepository->deleteOtp($otp);

            return response()->json([
                'message' => __('messages.otp_validated'),
            ], 200);
        } catch (\InvalidArgumentException $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 422);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_type' => $e->getErrorType(),
            ], $e->getCode());
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage(),);
            return response()->json($message->getMessage(), $e->getCode() ?: 400);
        }
    }

    protected function createAndSaveOtp(string $phoneOrEmail): string
    {
        // Create OTP code
        $otpCode = rand(100000, 999999);

        // Define time expiration
        $expiresAt = now()->addMinutes(10);

        // Save in database
        $this->otpRepository->updateOrCreateOtp($phoneOrEmail, $otpCode, $expiresAt);

        return $otpCode;
    }

    protected function sendOtp(string $phoneOrEmail, string $otpCode): bool
    {
        try {
            if (!filter_var($phoneOrEmail, FILTER_VALIDATE_EMAIL)) {
                throw new FailedToSendOtpException();
            }
    
            $subject = __('messages.otp_subject');
            $body = __('messages.otp_body', ['otpCode' => $otpCode]);
    
            Mail::raw($body, function ($message) use ($phoneOrEmail, $subject) {
                $message->to($phoneOrEmail)->subject($subject);
            });
    
            return true; 
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    protected function validateInput(array $input, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                throw new \InvalidArgumentException(__('messages.required_field', ['attribute' => $field]));
            }
        }
    }

    protected function validateEmailBasedOnType(string $phoneOrEmail, ?string $typeGenerate): void
    {
        $emailExists = $this->userService->verifyEmail($phoneOrEmail);

        match (strtoupper($typeGenerate)) {
            OtpTypeGenerate::CHANGE_PASSWORD => !$emailExists ? throw new EmailNotFoundException() : null,
            OtpTypeGenerate::SIGNUP => $emailExists ? throw new EmailAlreadyExistsException() : null,
            default => null
        };
    }

}
