<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    private $user;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index() : JsonResponse
    {
        return $this->userService->getUsers(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request) : JsonResponse
    {
        return $this->userService->insertUser($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) : JsonResponse
    {
        return $this->userService->getUserById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id,UserRequest $request) : JsonResponse
    {
        return $this->userService->updateUser($id,$request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) :  JsonResponse
    {
        return $this->userService->deleteUser($id);
    }
}
