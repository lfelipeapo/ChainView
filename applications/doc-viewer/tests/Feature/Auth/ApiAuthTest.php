<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJsonPath('user.email', 'test@example.com');
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'message',
                    'errors'
                ]);
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function login_requires_valid_email_format()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function authenticated_user_can_get_their_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'timestamp'
                ])
                ->assertJsonPath('data.user.id', $user->id)
                ->assertJsonPath('data.user.email', $user->email);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message'
                ])
                ->assertJsonPath('message', 'Logout realizado com sucesso!');
    }

    /** @test */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_access_protected_routes_with_valid_token()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/areas');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_protected_routes_without_token()
    {
        $response = $this->postJson('/api/areas', [
            'name' => 'Test Area'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_access_public_routes_without_token()
    {
        $response = $this->getJson('/api/areas');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_access_health_check_without_token()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'status',
                        'timestamp',
                        'database',
                        'system',
                        'identification'
                    ]
                ]);
    }

    /** @test */
    public function user_can_access_api_overview_without_token()
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'version',
                    'description',
                    'endpoints'
                ]);
    }
}
