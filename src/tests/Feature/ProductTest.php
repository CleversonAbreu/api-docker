<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
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

    public function test_can_create_product()
    {

        $data = [
            "name" => "Product 11",
            "price" => "19.99",
            "description" => "new product 11"
        ];

        $resp = [
            "message" => "product added successfully",
            "data" => [
                "name" => "Product 11",
                "price" => "19.99",
                "description" => "new product 11",
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('POST', '/api/v1/products', $data);

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('products', $data);
    }

    public function test_product_required_name()
    {


        $data = [
            "name" => null,
            "price" => "19.99",
            "description" => "edited"
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
        ])->json('POST', '/api/v1/products', $data);

        $response->assertStatus(422)
            ->assertJson($resp);

    }


    public function test_show_without_token_response()
    {


        Product::create([
            "id" => 5,
            "name" => "Conhaque",
            "price" => 323,
            "description" => "quente",

        ]);

        $data = [
            "id" => 5,
            "name" => "Conhaque",
            "price" => 323,
            "description" => "quente",
        ];

        $resp = [
            "data" => [
                "id" => 5,
                "name" => "Conhaque",
                "price" => 323,
                "description" => "quente",

            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('GET', '/api/v1/products/5');

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('products', $data);
    }



    public function test_can_update_product()
    {

        Product::create([
            "name" => "Product 1 updated",
            "price" => 19.99,
            "description" => "updated"
        ]);

        $data = [
            "name" => "Product 1 updated",
            "price" => 19.99,
            "description" => "updated"
        ];

        $resp = [
            "message" => "product updated successfully",
            "data" => 0
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('PUT', '/api/v1/products/3', $data);

        $response->assertStatus(200)
            ->assertJson($resp);
        $this->assertDatabaseHas('products', $data);
    }

    public function test_can_delete_product()
    {

        $resp = [
            "message" => [
                "message" => "product deleted successfully"
            ]
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            //'Authorization' => 'Bearer ' .  $this->token(),
        ])->json('DELETE', '/api/v1/products/1');

        $response->assertStatus(200)
            ->assertJson($resp);
    }
}
