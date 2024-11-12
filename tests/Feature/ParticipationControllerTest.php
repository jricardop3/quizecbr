<?php

namespace Tests\Unit;

use App\Models\Participation;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class ParticipationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }
    public function test_index_participations_successful()
    {
        $user = $this->actingAsUser();
        $quiz = Quiz::factory()->create();
        
        // Criando participações
        Participation::factory()->count(1)->for($user)->for($quiz)->create();

        $response = $this->getJson("/api/quizzes/{$quiz->id}/participations");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id', 'user_id', 'quiz_id', 'score', 'completed_at',
                         'user' => ['id', 'name', 'email'],
                         'quiz' => ['id', 'title', 'description']
                     ],
                 ]);
    }

    public function test_index_participations_empty()
    {
        $this->actingAsUser();
        $quiz = Quiz::factory()->create();

        $response = $this->getJson("/api/quizzes/{$quiz->id}/participations");

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Nenhuma participação encontrada.'
                 ]);
    }
    public function test_create_participation_successfully()
    {
        $this->actingAsUser();

        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(3)->create(['quiz_id' => $quiz->id]);

        $data = [
            'answers' => [
                ['question_id' => $questions[0]->id, 'answer' => true],
                ['question_id' => $questions[1]->id, 'answer' => false],
                ['question_id' => $questions[2]->id, 'answer' => true]
            ]
        ];

        $response = $this->postJson("/api/quizzes/{$quiz->id}/participations", $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'participation' => [
                         'id', 'user_id', 'quiz_id', 'score', 'completed_at'
                     ],
                     'message'
                 ]);
    }

    public function test_create_participation_with_missing_quiz()
    {
        $this->actingAsUser();

        $data = [
            'answers' => [
                ['question_id' => 1, 'answer' => true]
            ]
        ];

        $response = $this->postJson('/api/quizzes/999/participations', $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Quiz não encontrado.'
                 ]);
    }

    public function test_create_participation_with_invalid_answers()
    {
        $this->actingAsUser();

        $quiz = Quiz::factory()->create();
        Question::factory()->count(3)->create(['quiz_id' => $quiz->id]);

        $data = [
            'answers' => [
                ['question_id' => 999, 'answer' => true],  // ID de pergunta inexistente
            ]
        ];

        $response = $this->postJson("/api/quizzes/{$quiz->id}/participations", $data);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'answers.0.question_id'
            ]
        ]);
    }

    public function test_view_nonexistent_participation()
    {
        $this->actingAsUser();

        $quiz = Quiz::factory()->create();

        $response = $this->getJson("/api/quizzes/{$quiz->id}/participations/999");

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Participação não encontrada.'
                 ]);
    }

    public function test_user_cannot_participate_multiple_times()
    {
        $this->actingAsUser();

        $quiz = Quiz::factory()->create();
        $participation = Participation::factory()->create(['quiz_id' => $quiz->id, 'user_id' => auth()->id()]);

        $data = [
            'answers' => [
                ['question_id' => 1, 'answer' => true]
            ]
        ];

        $response = $this->postJson("/api/quizzes/{$quiz->id}/participations", $data);

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => 'Você já participou deste quiz.'
                 ]);
    }

}
