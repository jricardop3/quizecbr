<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        User::factory()->count(3)->create();

        $response = $this->actingAsAdmin()->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'email', 'role'],
                 ]);
    }

    public function test_store_user_successfully()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => 'user',
        ];

        $response = $this->actingAsAdmin()->postJson('/api/register/user', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Usuário criado com sucesso!',
                     'user' => [
                         'name' => 'John Doe',
                         'email' => 'john.doe@example.com',
                         'role' => 'user',
                     ],
                 ]);
    }

    public function test_show_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAsAdmin()->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email,
                     'role' => $user->role,
                 ]);
    }

    public function test_update_user()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated.email@example.com',
        ];

        $response = $this->actingAsAdmin()->putJson('/api/users/' . $user->id, $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Usuário atualizado com sucesso!',
                     'user' => [
                         'name' => 'Updated Name',
                         'email' => 'updated.email@example.com',
                     ],
                 ]);
    }

    public function test_destroy_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAsAdmin()->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(204);
    }

    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'sanctum');
        return $this;
    }
    public function test_user_access_denied_to_protected_route()
    {
        $user = User::factory()->create(['role' => 'user']); // Usuário comum
    
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users');
    
        $response->assertStatus(403)
                 ->assertJson([
                     'error' => 'Acesso não autorizado. necéssario autorização de administrador',
                 ]);
    }
    
    public function test_admin_access_granted_to_protected_route()
    {
        $admin = User::factory()->create(['role' => 'admin']); // Usuário admin

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    '*' => ['id', 'name', 'email', 'role'],
                ]);
    }


}
