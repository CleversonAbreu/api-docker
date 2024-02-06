<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use App\Api\ApiMessages;


class LoginJwtService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $loginJwtRequest) : JsonResponse 
    {
        if(!$token = auth('api')->attempt($loginJwtRequest)) {
            $message = new ApiMessages('Unauthorized');
            return response()->json($message->getMessage(),401);
        }

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout() : JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'logout successfully'],200);
    }

    public function refresh() : JsonResponse
    {
        $token = auth('api')->refresh();
        return response()->json(['token' =>  $token] ,200);
    }

}
