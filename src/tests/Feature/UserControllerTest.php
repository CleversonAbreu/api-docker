<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\UserController;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    private $userServiceMock;
    private $userController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userServiceMock = Mockery::mock(UserService::class);
        $this->userController = new UserController($this->userServiceMock);
    }

    public function testIndexReturnsJsonResponse()
    {
        $users = collect([
            (object)['id' => 1, 'name' => 'Joao Silva', 'email' => 'joao@gmail.com'],
            (object)['id' => 2, 'name' => 'Maria Silva', 'email' => 'maris@gmail.com'],
        ]);

        $this->userServiceMock
            ->shouldReceive('getUsers')
            ->once()
            ->with(10)
            ->andReturn(new JsonResponse($users));

        $response = $this->userController->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testStoreCreatesUser()
    {
        $requestData = [
            'name' => 'Joao Silva',
            'email' => 'joao@gmail.com',
            'password' => '12345678',
        ];

        $userRequest = Mockery::mock(UserRequest::class);
        $userRequest->shouldReceive('all')->andReturn($requestData);

        $this->userServiceMock
            ->shouldReceive('insertUser')
            ->once()
            ->with($requestData)
            ->andReturn(new JsonResponse(['id' => 1, 'name' => 'Joao Silva', 'email' => 'joao@gmail.com'], 201));

        $response = $this->userController->store($userRequest);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testShowReturnsUser()
    {
        $userId = 1;
        $user = (object)['id' => 1, 'name' => 'Joao Silva', 'email' => 'joao@gmail.com'];

        $this->userServiceMock
            ->shouldReceive('getUserById')
            ->once()
            ->with($userId)
            ->andReturn(new JsonResponse($user));

        $response = $this->userController->show($userId);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testUpdateUser()
    {
        $userId = 1;
        $updateData = ['name' => 'Maria Silva', 'email' => 'maris@gmail.com'];
        $userRequest = Mockery::mock(UserRequest::class);
        $userRequest->shouldReceive('all')->andReturn($updateData);

        $this->userServiceMock
            ->shouldReceive('updateUser')
            ->once()
            ->with($userId, $updateData)
            ->andReturn(new JsonResponse(['id' => 1, 'name' => 'Maria Silva', 'email' => 'maris@gmail.com']));

        $response = $this->userController->update($userId, $userRequest);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testChangePassword()
    {
        $requestData = [
            'email' => 'joao@gmail.com',
            'newPassword' => 'newpassword123',
        ];

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn($requestData);

        $this->userServiceMock
            ->shouldReceive('changePassword')
            ->once()
            ->with($requestData)
            ->andReturn(new JsonResponse(['message' => 'Senha alterada com sucesso']));

        $response = $this->userController->changePassword($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testDestroyUser()
    {
        $userId = 1;

        $this->userServiceMock
            ->shouldReceive('deleteUser')
            ->once()
            ->with($userId)
            ->andReturn(new JsonResponse(['message' => 'Usuário removido com sucesso']));

        $response = $this->userController->destroy($userId);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
