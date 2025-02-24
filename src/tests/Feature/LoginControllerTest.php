<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        // Creating a user for testing login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // User password
        ]);

        // Login credentials
        $loginRequest = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Sending a login request to the API
        $response = $this->postJson('/api/login', $loginRequest);

        // Asserting the response status and JSON structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                 ]);
    }

    public function testLoginFailure()
    {
        // Incorrect login credentials
        $loginRequest = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Sending a login request to the API
        $response = $this->postJson('/api/login', $loginRequest);

        // Asserting that the response returns unauthorized (401)
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthorized',
                 ]);
    }

    public function testLogoutSuccess()
    {
        // Creating a user for testing login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    
        // Performing login and retrieving the token
        $loginRequest = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];
    
        $loginResponse = $this->postJson('/api/login', $loginRequest);
        $token = $loginResponse->json('token');
    
        // Sending a logout request with the token
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/logout');  // Ensure this matches the route method (GET or POST)
    
        // Asserting the response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'logout successfully',
                 ]);
    }
    

    public function testRefreshSuccess()
    {
        // Creating a user for testing login
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Performing login and retrieving the token
        $loginRequest = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $loginResponse = $this->postJson('/api/login', $loginRequest);
        $token = $loginResponse->json('token');

        // Sending a refresh token request
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/refresh');

        // Asserting the response
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                 ]);
    }
}
