<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API de Usuários",
 *      description="Documentação da API de Usuários",
 *      @OA\Contact(
 *          email="suporte@exemplo.com"
 *      ),
 * )
 *      @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     )
 *
 * @OA\Tag(
 *     name="Usuários",
 *     description="Endpoints para gerenciamento de usuários"
 * )
 */

class UserController extends Controller
{
    private $user;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Listar todos os usuários",
     *     security={{"bearerAuth":{
     * }}}, 
     *     tags={"Usuários"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */

    public function index() : JsonResponse
    {
        return $this->userService->getUsers(10);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Criar um novo usuário",
     *     tags={"Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com")
     *         )
     *     )
     * )
     */


    public function store(UserRequest $request) : JsonResponse
    {
        return $this->userService->insertUser($request->all());
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obter um usuário pelo ID",
     *     security={{"bearerAuth":{}}},
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado"
     *     )
     * )
     */

    public function show(int $id) : JsonResponse
    {
        return $this->userService->getUserById($id);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Atualizar um usuário",
     *     security={{"bearerAuth":{}}},
     *     tags={"Usuários"},
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="janedoe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso"
     *     )
     * )
     */
    
    public function update(int $id,UserRequest $request) : JsonResponse
    {
        return $this->userService->updateUser($id,$request->all());
    }

    /**
     * @OA\Post(
     *     path="/api/users/change-password",
     *     summary="Alterar senha do usuário",
     *     tags={"Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "newPassword"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="newPassword", type="string", format="password", example="Pass*a1b37")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha alterada com sucesso"
     *     )
     * )
     */


    public function changePassword(Request $request) : JsonResponse
    {
        return $this->userService->changePassword($request->all());
    }
    
    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Deletar um usuário",
     *     tags={"Usuários"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso"
     *     )
     * )
     */
    public function destroy(int $id) :  JsonResponse
    {
        return $this->userService->deleteUser($id);
    }
}
