<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function token(): string
    {
        $data = [
            "email" => "crehbatera2@gmail.com",
            "password" => "password"
        ];

        $response = $this->json('POST', '/api/v1/login', $data);
        $content = json_decode($response->getContent());

        if (!isset($content->data->token)) {
            throw new RuntimeException('Token missing in response');
        }

        return $content->data->token;
    }

    public function test_can_create_user()
    {
        $data = [
            "name" => "Joao23",
            "email" => "joao23@gmail.com",
            "password" => bcrypt("123456")
        ];

        $resp = [
            "message" => "user added successfully",
            "data" => [
                "name" => "Joao23",
                "email" => "joao23@gmail.com",
                "id" => 1
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('POST', '/api/v1/users', $data);

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('users', $data);
    }

    public function test_user_required_name()
    {


        $data = [
            "name" => null,
            "email" => "abdhin@gmail.com",
            "password" => "assword"
        ];

        $resp = [
            "message" => "The name field is required.",
            "errors" => [
                "name" => [
                    "The name field is required."
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            //   'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('POST', '/api/v1/users', $data);

        $response->assertStatus(422)
            ->assertJson($resp);

    }


    public function test_show_without_token_response()
    {
        $pass = bcrypt("123456");

        User::create([
            "name" => "Joao",
            "email" => "joao2@gmail.com",
            "password" => $pass
        ]);

        $data = [
            "name" => "Joao",
            "email" => "joao2@gmail.com",
            "password" => $pass
        ];

        $resp = [
            "data" => [
                "name" => "Joao",
                "email" => "joao2@gmail.com",
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('GET', '/api/v1/users/1');

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('users', $data);
    }

    public function test_can_update_user()
    {
        $pass = bcrypt("123456");

        User::create([
            "name" => "Hakin kohen",
            "email" => "kohen@gmail.com",
            "password" => $pass
        ]);

        $data = [
            "name" => "Hakin kohen",
            "email" => "kohen@gmail.com",
            "password" => $pass
        ];

        $resp = [
            "message" => "user updated successfully",
            "data" => 0
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('PUT', '/api/v1/users/3', $data);

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('users', $data);
    }

    public function test_can_delete_user()
    {

        $resp = [
            "message" => [
                "message" => "user deleted successfully"
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            //'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('DELETE', '/api/v1/users/2');

        $response->assertStatus(200)
            ->assertJson($resp);
    }
}
