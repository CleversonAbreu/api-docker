<?php

namespace App\Services;

use App\Exceptions\OtpException\EmailNotFoundException;
use App\Repositories\UserRepository;
use App\Exceptions\CustomException;
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
                'data' => $user
                ,
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function changePassword(array $user): JsonResponse
    {
        try {

            // Validate 'email'
            if (empty($user['email'])) {
                throw new \InvalidArgumentException(__('messages.required_field', ['attribute' => 'email']));
            }

            // Validate 'newPassword'
            if (empty($user['newPassword'])) {
                throw new \InvalidArgumentException(__('messages.required_field', ['attribute' => 'newPassword']));
            }

            // Verify e-mail and check if exists
            $verificationResult = $this->verifyEmail($user['email']);
          
            if (!$verificationResult) {
                throw new EmailNotFoundException();
            }

            $this->userRepository->changePassword($user);  

            return response()->json([
                'message' => 'user updated successfully'
                ,
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

    public function deleteUser(int $id): JsonResponse
    {
        try {
            $this->userRepository->delete($id);
            return response()->json([
                'message' => 'user deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function verifyEmail(string $email): bool
    {
        // verify email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Verify email exists
        return $this->userRepository->emailExists($email);
    }
}
