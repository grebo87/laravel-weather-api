<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_register_user_creates_and_returns_user(): void
    {
        $userData = [
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->authService->registerUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['first_name'], $user->first_name);
        $this->assertEquals($userData['last_name'], $user->last_name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    public function test_login_user_returns_user_and_token_on_success(): void
    {
        $password = 'password123';
        $testUser = User::factory()->create(['password' => Hash::make($password)]);

        // Simulates a successful login attempt
        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => $testUser->email, 'password' => $password])
            ->andReturn(true);

        // Returns the mocked/real user
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($testUser);

        $credentials = ['email' => $testUser->email, 'password' => $password];
        $result = $this->authService->loginUser($credentials);

        $this->assertIsArray($result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertEquals($testUser->id, $result['user']->id);
        $this->assertNotEmpty($result['token']);
        $this->assertCount(1, $testUser->tokens); // Verify that a token was created
    }

    public function test_login_user_throws_validation_exception_on_failure(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'wrong@example.com', 'password' => 'wrongpass'])
            ->andReturn(false); // Simulates a failed login attempt

        $this->expectException(ValidationException::class);

        $this->authService->loginUser(['email' => 'wrong@example.com', 'password' => 'wrongpass']);
    }

    public function test_logout_user_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->accessToken; // Get the token object

        // If AuthService usa $user->tokens()->delete();
        $this->authService->logoutUser($user);
        $this->assertCount(0, $user->fresh()->tokens); // Retrieve the user again to see the updated state of tokens
    }
}
