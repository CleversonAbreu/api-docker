<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\OtpService;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * @OA\Post(
     *     path="/api/otp/generate",
     *     summary="Gerar um código OTP",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone_or_email", "type_generate"},
     *             @OA\Property(property="phone_or_email", type="string", example="cre1@gmail.com"),
     *             @OA\Property(property="type_generate", type="string", example="CHANGE_PASSWORD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código OTP gerado com sucesso"
     *     )
     * )
     */
    public function generate(Request $request): mixed
    {
        return $this->otpService->generateOtp($request->all());
    }
    /**
     * @OA\Post(
     *     path="/api/otp/validate",
     *     summary="Validar um código OTP",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone_or_email", "otp_code"},
     *             @OA\Property(property="phone_or_email", type="string", example="cre1@gmail.com"),
     *             @OA\Property(property="otp_code", type="string", example="959120")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código OTP validado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Código OTP inválido ou expirado"
     *     )
     * )
     */
    public function validateOtp(Request $request): mixed
    {
        return $this->otpService->validateOtp($request->all());
    }
}
