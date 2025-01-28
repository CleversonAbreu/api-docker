<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Services\OtpService;

class OtpControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGenerateOtpApi(): void
    {
        $input = ['phone_or_email' => 'test@example.com'];

    
        Mail::fake();

        
        $response = $this->postJson('/api/otp/generate', $input);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'otp']);
        Mail::assertSent(function ($mail) use ($input) {
            return $mail->hasTo($input['phone_or_email']);
        });
    }

    public function testValidateOtpApi(): void
    {
        $input = ['phone_or_email' => 'test@example.com', 'otp_code' => '123456'];

     
        $response = $this->postJson('/api/otp/validate', $input);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => __('messages.otp_validated'),
        ]);
    }

    public function testGenerateOtpFailsMissingEmail(): void
    {
        $response = $this->postJson('/api/otp/generate', []);

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }
}
