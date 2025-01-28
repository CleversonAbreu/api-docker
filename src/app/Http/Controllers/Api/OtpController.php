<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\OtpService;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function generate(Request $request): mixed
    {
        return $this->otpService->generateOtp($request->all());
    }

    public function validateOtp(Request $request): mixed
    {
        return $this->otpService->validateOtp($request->all());
    }
}