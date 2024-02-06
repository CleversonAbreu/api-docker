<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use App\Api\ApiMessages;


class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(int $qtd): JsonResponse
    {
        return response()->json($this->userRepository->findAll($qtd), 200);
    }

    public function getUserById(int $id): JsonResponse
    {
        try {
            return response()->json(['data' => $this->userRepository->findById($id)], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function insertUser(array $user): JsonResponse
    {
        try {
            $user = $this->userRepository->insert($user);
            return response()->json([
                'message' => 'user added successfully',
                'data' => $user
                ,
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function updateUser($id, array $user): JsonResponse
    {
        try {
            $userUpdated = $this->userRepository->update($id, $user);
            return response()->json([
                'message' => 'user updated successfully',
                'data' => $userUpdated
                ,
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function deleteUser(int $id): JsonResponse
    {
        try {
            $this->userRepository->delete($id);
            return response()->json([
                'message' => ['message' => 'user deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
