<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginJwtService;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints para gerenciamento da authenticação"
 * )
 */
class LoginJwtController extends Controller
{
    private $loginJwtService;

    public function __construct(LoginJwtService $loginJwtService)
    {
        $this->loginJwtService = $loginJwtService;
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar usuário e gerar token JWT",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="cre1@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Pass*a1b37")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas")
     * )
     */
    public function login(LoginRequest $request)
    {
        return $this->loginJwtService->login($request->all());
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="Encerrar sessão do usuário",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout bem-sucedido")
     * )
     */
    public function logout()
    {
        return $this->loginJwtService->logout();
    }

    /**
     * @OA\Get(
     *     path="/api/refresh",
     *     summary="Atualizar token JWT",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token inválido ou expirado")
     * )
     */
    public function refresh()
    {
        return $this->loginJwtService->refresh();
    }
}