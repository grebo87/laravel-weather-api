<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register(): void
    {
        $password = $this->faker->password(8);
        $userData = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson(route('api.auth.register'), $userData);

        $response->assertStatus(201) // HTTP_CREATED
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'],
                'token_type',
                'access_token',
            ])
            ->assertJsonPath('user.email', $userData['email']);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        // Test without email
        $response = $this->postJson(route('api.auth.register'), [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(422) // HTTP_UNPROCESSABLE_ENTITY
            ->assertJsonValidationErrors(['email']);

        // Test with passwords that don't match
        $response = $this->postJson(route('api.auth.register'), [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'],
                'token_type',
                'access_token',
            ])
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.auth.login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Invalid credentials.');
    }

    public function test_authenticated_user_can_get_their_details(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum') // Simulates authentication with Sanctum
            ->getJson(route('api.user'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'first_name', 'last_name', 'email']])
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        // Generate a token for the user
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('api.auth.logout'));

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Successfully logged out');
    }
}
