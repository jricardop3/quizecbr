<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\User;

class QuestionControllerTest extends TestCase
{
    //use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
    }

    protected function actingAsUser()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
    }

    public function test_index_questions_for_existing_quiz()
    {
        $this->actingAsUser();
        
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(1)->create(['quiz_id' => $quiz->id]);

        $response = $this->getJson("/api/quizzes/{$quiz->id}/questions");

        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonStructure([
                     '*' => ['id', 'quiz_id', 'text', 'correct_answer', 'image']
                 ]);
    }

    public function test_index_questions_for_non_existent_quiz()
    {
        $this->actingAsUser();
        
        $response = $this->getJson("/api/quizzes/999/questions");

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'
                 ]);
    }

    public function test_store_question_successfully()
    {
        $this->actingAsAdmin();
        Storage::fake('public');
        
        $quiz = Quiz::factory()->create();
        $data = [
            'text' => 'Esta é uma pergunta de teste',
            'correct_answer' => true,
            'image' => UploadedFile::fake()->image('question.jpg')
        ];

        $response = $this->postJson("/api/quizzes/{$quiz->id}/questions", $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'question' => ['id', 'quiz_id', 'text', 'correct_answer', 'image']
                 ]);

        $imagePath = $response->json('question.image');
        $this->assertTrue(Storage::disk('public')->exists($imagePath));
    }

    public function test_store_question_for_non_existent_quiz()
    {
        $this->actingAsAdmin();
        
        $data = [
            'text' => 'Esta é uma pergunta de teste',
            'correct_answer' => true,
        ];

        $response = $this->postJson("/api/quizzes/999/questions", $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'
                 ]);
    }

    public function test_show_question_successfully()
    {
        $this->actingAsUser();
        
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $response = $this->getJson("/api/quizzes/{$quiz->id}/questions/{$question->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'quiz_id', 'text', 'correct_answer', 'image']);
    }

    public function test_show_non_existent_question()
    {
        $this->actingAsUser();
        
        $quiz = Quiz::factory()->create();

        $response = $this->getJson("/api/quizzes/{$quiz->id}/questions/999");

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'
                 ]);
    }

    public function test_update_question_successfully()
    {
        $this->actingAsAdmin();
        Storage::fake('public');
    
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
    
        $data = [
            'text' => 'Pergunta atualizada',
            'correct_answer' => false,
            'image' => UploadedFile::fake()->image('updated_question.jpg')
        ];
    
        $response = $this->putJson("/api/questions/{$question->id}", $data);
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'question' => ['id', 'quiz_id', 'text', 'correct_answer', 'image']
                 ]);
    
        // Verificação alternativa para confirmar a existência do arquivo
        $imagePath = $response->json('question.image');
        $this->assertTrue(Storage::disk('public')->exists($imagePath));
    }
    

    public function test_update_non_existent_question()
    {
        $this->actingAsAdmin();
        
        $data = [
            'text' => 'Pergunta inexistente',
            'correct_answer' => false,
        ];

        $response = $this->putJson("/api/questions/999", $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'
                 ]);
    }

    public function test_destroy_question_successfully()
    {
        $this->actingAsAdmin();
        Storage::fake('public');
    
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id, 'image' => 'images/question_image.jpg']);
    
        Storage::disk('public')->put('images/question_image.jpg', 'fake content');
    
        $response = $this->deleteJson("/api/questions/{$question->id}");
    
        $response->assertStatus(204);
    
        // Verificação alternativa se o arquivo não existe no sistema de arquivos
        $this->assertFalse(Storage::disk('public')->exists('images/question_image.jpg'));
    }
    

    public function test_destroy_non_existent_question()
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson("/api/questions/999");

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'
                 ]);
    }
}
