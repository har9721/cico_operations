<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $this->withoutExceptionHandling();

        User::factory()->create([
            "email" => "harshal@yopmail.com",
            "password" => Hash::make('12345678')
        ]);

        $response = $this->postJson('/api/auth/login',[
            'email' => 'harshal@yopmail.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_incorrect_credentials() : void 
    {
        $this->withoutExceptionHandling();

        User::factory()->create([
            "email" => "harshal21@yopmail.com",
            "password" => Hash::make('12345678')
        ]);

        $response = $this->postJson('/api/auth/login',[
            'email' => 'harshal2@yopmail.com',
            'password' => '12345678'
        ]);

        $response->assertStatus(401);
    }
}
