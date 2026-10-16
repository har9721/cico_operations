<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_a_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Harshal Test',
            'email' => 'harshal@example.com',
            'password' => '12345678',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('data.name', 'Harshal Test')
            ->assertJsonPath('data.email', 'harshal@example.com')
            ->assertJsonMissingPath('data.password')
            ->assertJsonStructure([
                'token',
                'expires_in',
            ]);

        $user = User::where('email', 'harshal@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('12345678', $user->password));
    }

    public function test_registration_requires_valid_unique_details(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'existing@example.com',
            'password' => 'short',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $this->assertDatabaseCount('users', 1);
    }
}
