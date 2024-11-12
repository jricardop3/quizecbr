<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'sanctum');
        return $this;
    }

    public function test_index_quizzes()
    {
        Quiz::factory()->count(1)->create();

        $response = $this->actingAsAdmin()->getJson('/api/quizzes');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'title', 'description', 'image', 'created_at', 'updated_at'],
                 ]);
    }

    public function test_index_quizzes_empty()
    {
        $response = $this->actingAsAdmin()->getJson('/api/quizzes');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Nenhum quiz encontrado no momento.']);
    }

    public function test_store_quiz_successfully()
    {
        Storage::fake('public');
        $data = [
            'title' => 'Novo Quiz',
            'description' => 'Descrição do quiz',
            'image' => UploadedFile::fake()->image('quiz.jpg'),
        ];

        $response = $this->actingAsAdmin()->postJson('/api/quizzes', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'quiz' => ['id', 'title', 'description', 'image', 'created_at', 'updated_at'],
                 ]);

        $path = $response->json('quiz.image');
        $this->assertTrue(Storage::disk('public')->exists($path), 'O arquivo não existe no diretório esperado.');
    }

    public function test_store_quiz_validation_error()
    {
        $data = ['description' => ''];

        $response = $this->actingAsAdmin()->postJson('/api/quizzes', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure(['error', 'messages']);
    }

    public function test_show_quiz()
    {
        $quiz = Quiz::factory()->create();

        $response = $this->actingAsAdmin()->getJson("/api/quizzes/{$quiz->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $quiz->id,
                     'title' => $quiz->title,
                     'description' => $quiz->description,
                 ]);
    }

    public function test_show_quiz_not_found()
    {
        $response = $this->actingAsAdmin()->getJson('/api/quizzes/999');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.']);
    }

    public function test_update_quiz()
    {
        $quiz = Quiz::factory()->create();
        $data = [
            'title' => 'Título Atualizado',
            'description' => 'Descrição Atualizada',
        ];

        $response = $this->actingAsAdmin()->patchJson("/api/quizzes/{$quiz->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Quiz atualizado com sucesso!',
                     'quiz' => [
                         'title' => 'Título Atualizado',
                         'description' => 'Descrição Atualizada',
                     ],
                 ]);
    }

    public function test_update_quiz_validation_error()
    {
        $quiz = Quiz::factory()->create();
        $data = ['title' => str_repeat('a', 300)];

        $response = $this->actingAsAdmin()->patchJson("/api/quizzes/{$quiz->id}", $data);

        $response->assertStatus(422)
                 ->assertJsonStructure(['error', 'messages']);
    }

    public function test_destroy_quiz()
    {
        $quiz = Quiz::factory()->create();
        $response = $this->actingAsAdmin()->deleteJson("/api/quizzes/{$quiz->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }

    public function test_destroy_quiz_not_found()
    {
        $response = $this->actingAsAdmin()->deleteJson('/api/quizzes/999');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.']);
    }
}
