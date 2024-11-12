<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participation;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserScore;
use Illuminate\Http\Request;

class ParticipationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/quizzes/{$quiz->id}/participations",
     *     summary="Listar todas as participações",
     *     tags={"Participações"},
     *     description="Retorna todas as participações com as informações do usuário e do quiz associadas.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de participações encontrada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="ID da participação"),
     *                 @OA\Property(property="user_id", type="integer", description="ID do usuário"),
     *                 @OA\Property(property="quiz_id", type="integer", description="ID do quiz"),
     *                 @OA\Property(property="score", type="integer", description="Pontuação da participação"),
     *                 @OA\Property(property="completed_at", type="string", format="date-time",
     *                      description="Data e hora de conclusão da participação"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", description="ID do usuário"),
     *                     @OA\Property(property="name", type="string", description="Nome do usuário"),
     *                     @OA\Property(property="email", type="string", description="Email do usuário")
     *                 ),
     *                 @OA\Property(property="quiz", type="object",
     *                     @OA\Property(property="id", type="integer", description="ID do quiz"),
     *                     @OA\Property(property="title", type="string", description="Título do quiz"),
     *                     @OA\Property(property="description", type="string", description="Descrição do quiz")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma participação encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nenhuma participação encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao buscar as participações",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string",
     *              example="Erro ao buscar as participações."),
     *             @OA\Property(property="message", type="string",
     *              example="Ocorreu um problema ao tentar buscar as participações.
     *              Por favor, tente novamente mais tarde.")
     *         )
     *     )
     * )
     */

    // Listar todas as participações
    public function index()
    {
        try {
            // Busca todas as participações com relação aos usuários e quizzes
            $participations = Participation::with('user', 'quiz')->get();

            // Verifica se há participações
            if ($participations->isEmpty()) {
                return response()->json(['message' => 'Nenhuma participação encontrada.'], 404);
            }

            return response()->json($participations, 200);
        } catch (\Exception $e) {
            // Retorna uma mensagem de erro caso ocorra um problema inesperado
                return response()->json([
                'error' => 'Erro ao buscar as participações.',
                'message' => 'Ocorreu um problema ao tentar buscar as participações. Tente novamente mais tarde.'
                ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/quizzes/{quizId}/participations",
     *     summary="Registrar a participação em um quiz",
     *     description="Registra a participação de um usuário em um quiz específico,
     *     armazena as respostas e calcula a pontuação com base nas respostas corretas.",
     *     operationId="storeParticipation",
     *     tags={"Participações"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="quizId",
     *         in="path",
     *         description="ID do quiz para registrar a participação",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"answers"},
     *             @OA\Property(
     *                 property="answers",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"question_id", "answer"},
     *                     @OA\Property(property="question_id", type="integer",
     *                      description="ID da pergunta"),
     *                     @OA\Property(property="answer", type="boolean",
     *                      description="Resposta do usuário: true para verdadeiro, false para falso")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Participação registrada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="participation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="quiz_id", type="integer", example=3),
     *                 @OA\Property(property="score", type="integer", example=40),
     *                 @OA\Property(property="completed_at", type="string", example="2024-11-10T14:48:00.000000Z")
     *             ),
     *             @OA\Property(property="user_score", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="quiz_id", type="integer", example=3),
     *                 @OA\Property(property="score", type="integer", example=40)
     *             ),
     *             @OA\Property(property="message", type="string", example="Participação registrada com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Quiz não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Quiz não encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Participação já registrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Você já participou deste quiz.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="answers",
     *                     type="array",
     *                     @OA\Items(type="string", example="O campo answers é obrigatório.")
     *                 ),
     *                 @OA\Property(
     *                     property="answers.0.question_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="O campo question_id é obrigatório.")
     *                 ),
     *                 @OA\Property(
     *                     property="answers.0.answer",
     *                     type="array",
     *                     @OA\Items(type="string", example="O campo answer é obrigatório.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    // Criar uma nova participação para um quiz
    public function store(Request $request, $quizId)
    {
        // Encontrar o quiz pelo ID fornecido
        $quiz = Quiz::find($quizId);

        // Retornar um erro se o quiz não for encontrado
        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado.'], 404);
        }

        // Obter o ID do usuário autenticado
        $userId = $request->user()->id;

        // Verificar se o usuário já participou deste quiz
        $existingParticipation = Participation::where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->first();

        // Retornar erro se o usuário já tiver participação
        if ($existingParticipation) {
            return response()->json(['error' => 'Você já participou deste quiz.'], 400);
        }

        // Validar o formato das respostas enviadas
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|boolean',
        ]);

        // Inicializar a pontuação do usuário
        $score = 0;

        // Processar cada resposta
        foreach ($request->answers as $answerData) {
            $question = Question::find($answerData['question_id']);
            $isCorrect = $question && $question->correct_answer == $answerData['answer'];

            // Registrar cada resposta individualmente em UserAnswer, incluindo is_correct
            UserAnswer::create([
                'user_id' => $userId,
                'question_id' => $answerData['question_id'],
                'answer' => $answerData['answer'],
                'is_correct' => $isCorrect, // Define is_correct como true ou false
            ]);

            // Incrementar a pontuação se a resposta estiver correta
            if ($isCorrect) {
                $score += 10; // Cada resposta correta vale 10 pontos
            }
        }

        // Registrar a pontuação total para o quiz em UserScore
        $userScore = UserScore::create([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => $score,
        ]);

        // Registrar a participação do usuário no quiz
        $participation = Participation::create([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => $score,
            'completed_at' => now(),
        ]);

        // Retornar a resposta JSON com a pontuação e participação registradas
        return response()->json([
            'participation' => $participation,
            'user_score' => $userScore,
            'message' => 'Participação registrada com sucesso!',
        ], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/quizzes/{quizId}/participations/{id}",
     *     summary="Exibir uma participação específica",
     *     description="Retorna os detalhes de uma participação específica de um usuário em um quiz.",
     *     operationId="showParticipation",
     *     tags={"Participações"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="quizId",
     *         in="path",
     *         description="ID do quiz ao qual a participação está associada",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da participação a ser exibida",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da participação",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer",
     *              description="ID da participação"),
     *             @OA\Property(property="user_id", type="integer",
     *              description="ID do usuário que participou"),
     *             @OA\Property(property="quiz_id", type="integer",
     *              description="ID do quiz"),
     *             @OA\Property(property="score", type="integer",
     *              description="Pontuação obtida na participação"),
     *             @OA\Property(property="completed_at", type="string", format="date-time",
     *              description="Data e hora de conclusão da participação"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="ID do usuário"),
     *                 @OA\Property(property="name", type="string", description="Nome do usuário"),
     *                 @OA\Property(property="email", type="string", description="Email do usuário")
     *             ),
     *             @OA\Property(
     *                 property="quiz",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="ID do quiz"),
     *                 @OA\Property(property="title", type="string", description="Título do quiz"),
     *                 @OA\Property(property="description", type="string", description="Descrição do quiz")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Participação não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Participação não encontrada.")
     *         )
     *     )
     * )
     */

        // Exibir uma participação específica para ver a pontuação
    public function show($id)
    {
        // Busca a participação pelo ID fornecido, incluindo os relacionamentos com o usuário e o quiz
        $participation = Participation::with('user', 'quiz')->find($id);

        // Retorna uma mensagem de erro caso a participação não seja encontrada
        if (!$participation) {
            return response()->json(['error' => 'Participação não encontrada.'], 404);
        }

        // Retorna a participação encontrada com sucesso
        return response()->json($participation, 200);
    }
    /**
     * @OA\Get(
     *     path="/api/ranking/general",
     *     summary="Obter o ranking geral de jogadores",
     *     tags={"Ranking"},
     *     description="Retorna o ranking geral com base na soma das pontuações em todas as participações.",
     *     @OA\Response(
     *         response=200,
     *         description="Ranking geral gerado com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="ID do usuário"),
     *                 @OA\Property(property="name", type="string", description="Nome do usuário"),
     *                 @OA\Property(property="email", type="string", description="Email do usuário"),
     *                 @OA\Property(property="score", type="integer", description="Pontuação total do usuário")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nenhuma participação encontrada para gerar o ranking",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string",
     *              example="Nenhuma participação encontrada para gerar o ranking.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao gerar o ranking geral",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string",
     *              example="Erro ao gerar o ranking geral."),
     *             @OA\Property(property="message", type="string",
     *              example="Ocorreu um problema inesperado ao tentar gerar o ranking.
     *              Tente novamente mais tarde.")
     *         )
     *     )
     * )
     */

    // Exibir o ranking dos jogadores com base na pontuação
    public function generalRanking()
    {
        try {
            // Obter o ranking de usuários com base na soma das pontuações
            $ranking = User::withSum('participations', 'score')
                ->orderByDesc('participations_sum_score')
                ->get(['id', 'name', 'email']);

            // Verificar se há dados de participação para exibir no ranking
            if ($ranking->isEmpty()) {
                return response()->json(['message' => 'Nenhuma participação encontrada para gerar o ranking.'], 404);
            }

            // Estruturar o retorno do ranking
            $ranking = $ranking->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'score' => $user->participations_sum_score ?? 0
                ];
            });

            return response()->json($ranking, 200);
        } catch (\Exception $e) {
            // Retorna uma resposta de erro caso ocorra uma exceção
            return response()->json([
                'error' => 'Erro ao gerar o ranking geral.',
                'message' => 'Ocorreu um problema inesperado ao tentar gerar o ranking. Tente novamente mais tarde.'
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/ranking/quiz/{quizId}",
     *     summary="Obter o ranking dos usuários para um quiz específico",
     *     description="Retorna o ranking dos usuários com base na pontuação de um quiz específico.",
     *     operationId="quizRanking",
     *     tags={"Ranking"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="quizId",
     *         in="path",
     *         description="ID do quiz",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ranking dos usuários para o quiz",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer",
     *                  example=1, description="ID do usuário"),
     *                 @OA\Property(property="name", type="string",
     *                  example="John Doe", description="Nome do usuário"),
     *                 @OA\Property(property="email", type="string",
     *                  example="john.doe@example.com", description="Email do usuário"),
     *                 @OA\Property(property="score", type="integer",
     *                  example=50, description="Pontuação do usuário para o quiz")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Quiz não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string",
     *              example="Quiz não encontrado. Verifique o ID e tente novamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Nenhuma pontuação encontrada para este quiz",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string",
     *              example="Nenhuma pontuação encontrada para este quiz.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao gerar o ranking",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao tentar gerar o ranking.")
     *         )
     *     )
     * )
     */

    public function quizRanking($quizId)
    {
        // Verifica se o quiz existe
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'], 404);
        }

        // Soma os scores de um quiz específico para cada usuário e ordena
        $ranking = User::with(['scores' => function ($query) use ($quizId) {
                $query->where('quiz_id', $quizId);
        }])
            ->get(['id', 'name', 'email']);

        // Verifica se existe pontuação para o quiz
        if ($ranking->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma pontuação encontrada para este quiz.'
            ], 404);
        }

        // Filtra e estrutura o retorno do ranking para o quiz específico
        $ranking = $ranking->map(function ($user) use ($quizId) {
            $score = $user->scores->firstWhere('quiz_id', $quizId)->score ?? 0;
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'score' => $score
            ];
        })->sortByDesc('score')->values();

        // Retorna o ranking do quiz específico
        return response()->json($ranking, 200);
    }
}
