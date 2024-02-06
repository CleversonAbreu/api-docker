<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginJwtService;

class LoginJwtController extends Controller
{
    private $loginJwtService;

    public function __construct(LoginJwtService $loginJwtService)
    {
        $this->loginJwtService = $loginJwtService;
    }

    public function login(LoginRequest $request)
    {
        return $this->loginJwtService->login($request->all());
    }

    public function logout()
    { 
        return $this->loginJwtService->logout();
    }

    public function refresh()
    {
        return $this->loginJwtService->refresh();
    }
}
