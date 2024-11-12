<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_successful()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login/admin', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'role',
                     ],
                 ]);
    }

    public function test_admin_login_incorrect_password()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login/admin', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Senha incorreta. Verifique e tente novamente.']);
    }

    public function test_user_login_successful()
    {
        $user = User::factory()->create([
            'name' => 'User Name',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $response = $this->postJson('/api/login/user', [
            'name' => 'User Name',
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'token',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'role',
                     ],
                 ]);
    }

    public function test_user_login_name_mismatch()
    {
        $user = User::factory()->create([
            'name' => 'Correct Name',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $response = $this->postJson('/api/login/user', [
            'name' => 'Wrong Name',
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Nome de usuário incorreto. Verifique os dados e tente novamente.']);
    }

    public function test_logout()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso.']);
    }
}
