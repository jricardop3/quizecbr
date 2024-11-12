<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/quizzes/{quizId}/questions",
 *     summary="Lista todas as perguntas de um quiz",
 *     description="Retorna uma lista de todas as perguntas associadas a um quiz específico.",
 *     operationId="listQuestions",
 *     tags={"Perguntas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="quizId",
 *         in="path",
 *         description="ID do quiz ao qual as perguntas pertencem",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de perguntas do quiz",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", description="ID da pergunta"),
 *                 @OA\Property(property="quiz_id", type="integer", description="ID do quiz"),
 *                 @OA\Property(property="text", type="string", description="Texto da pergunta"),
 *                 @OA\Property(property="correct_answer", type="boolean", description="Resposta correta"),
 *                 @OA\Property(property="image", type="string", description="Caminho da imagem da pergunta")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Quiz não encontrado ou sem perguntas",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Quiz não encontrado. Verifique o ID e tente novamente."),
 *             @OA\Property(property="message", type="string",
 *              example="Nenhuma pergunta encontrada para este quiz.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno ao listar as perguntas",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao listar perguntas. Por favor, tente novamente.")
 *         )
 *     )
 * )
 */
    // Listar todas as perguntas de um quiz
    public function index($quizId)
    {
        $quiz = Quiz::find($quizId);

        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'], 404);
        }

        if ($quiz->questions->isEmpty()) {
            return response()->json(['message' => 'Nenhuma pergunta encontrada para este quiz.'], 404);
        }

        return response()->json($quiz->questions, 200);
    }
/**
 * @OA\Post(
 *     path="/api/quizzes/{quizId}/questions",
 *     summary="Cria uma nova pergunta para um quiz",
 *     description="Cria uma nova pergunta associada a um quiz específico.",
 *     operationId="createQuestion",
 *     tags={"Perguntas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="quizId",
 *         in="path",
 *         description="ID do quiz ao qual a pergunta pertence",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"text", "correct_answer"},
 *             @OA\Property(property="text", type="string",
 *              description="Texto da pergunta"),
 *             @OA\Property(property="correct_answer", type="boolean",
 *              description="Resposta correta (true para verdadeiro, false para falso)"),
 *             @OA\Property(property="image", type="string", format="binary",
 *              description="Imagem opcional da pergunta (arquivo)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Pergunta criada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Pergunta criada com sucesso!"),
 *             @OA\Property(property="question", type="object",
 *                 @OA\Property(property="id", type="integer", description="ID da pergunta"),
 *                 @OA\Property(property="text", type="string", description="Texto da pergunta"),
 *                 @OA\Property(property="correct_answer", type="boolean", description="Resposta correta"),
 *                 @OA\Property(property="image", type="string", description="Caminho da imagem da pergunta")
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
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Os dados fornecidos são inválidos."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="text", type="array", @OA\Items(type="string",
 *                  example="O campo texto é obrigatório.")),
 *                 @OA\Property(property="correct_answer", type="array", @OA\Items(type="string",
 *                  example="O campo resposta correta é obrigatório.")),
 *                 @OA\Property(property="image", type="array", @OA\Items(type="string",
 *                  example="O campo imagem deve ser um arquivo do tipo: jpeg, png, jpg, gif."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno ao criar a pergunta",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao criar a pergunta."),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar salvar a pergunta. Por favor, tente novamente.")
 *         )
 *     )
 * )
 */

    // Criar uma nova pergunta para um quiz
    public function store(Request $request, $quizId)
    {
        $quiz = Quiz::find($quizId);

        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'], 404);
        }

        $validatedData = $request->validate([
            'text' => 'required|string',
            'correct_answer' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Valida o arquivo de imagem
        ]);

        try {
            // Armazena a imagem, caso tenha sido enviada
            $imagePath = null;
            if ($request->hasFile('image')) {
                // Armazena em 'storage/app/public/uploads/questions'
                $imagePath = $request->file('image')->store('uploads/questions', 'public');
            }

            // Cria a pergunta associada ao quiz
            $question = $quiz->questions()->create([
                'text' => $validatedData['text'],
                'correct_answer' => $validatedData['correct_answer'],
                'image' => $imagePath, // Salva o caminho da imagem
            ]);

            return response()->json([
                'message' => 'Pergunta criada com sucesso!',
                'question' => $question,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar a pergunta.',
                'message' => 'Ocorreu um erro inesperado ao tentar salvar a pergunta. Por favor, tente novamente.'
            ], 500);
        }
    }
/**
 * @OA\Get(
 *     path="/api/quizzes/{quizId}/questions/{id}",
 *     summary="Exibe uma pergunta específica de um quiz",
 *     description="Retorna os detalhes de uma pergunta específica associada a um quiz.",
 *     operationId="showQuestion",
 *     tags={"Perguntas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="quizId",
 *         in="path",
 *         description="ID do quiz ao qual a pergunta pertence",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID da pergunta",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalhes da pergunta",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer",
 *              description="ID da pergunta"),
 *             @OA\Property(property="quiz_id", type="integer",
 *              description="ID do quiz"),
 *             @OA\Property(property="text", type="string",
 *              description="Texto da pergunta"),
 *             @OA\Property(property="correct_answer", type="boolean",
 *              description="Resposta correta"),
 *             @OA\Property(property="image", type="string", nullable=true,
 *              description="Caminho da imagem da pergunta")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pergunta não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Pergunta não encontrada. Verifique o ID e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro ao exibir a pergunta.")
 *         )
 *     )
 * )
 */

    // Exibir uma pergunta específica
    public function show($quizId, $id)
    {
        $question = Question::where('quiz_id', $quizId)->find($id);

        if (!$question) {
            return response()->json(['error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'], 404);
        }

        return response()->json($question, 200);
    }
/**
 * @OA\Put(
 *     path="/api/questions/{id}",
 *     summary="Atualiza uma pergunta",
 *     description="Atualiza o texto, a resposta correta e a imagem de uma pergunta existente.",
 *     operationId="updateQuestion",
 *     tags={"Perguntas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID da pergunta a ser atualizada",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"text", "correct_answer"},
 *                 @OA\Property(property="text", type="string",
 *                  description="Texto da pergunta"),
 *                 @OA\Property(property="correct_answer", type="boolean",
 *                  description="Resposta correta (true para verdadeiro, false para falso)"),
 *                 @OA\Property(property="image", type="file",
 *                  description="Imagem opcional da pergunta (arquivo)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Pergunta atualizada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Pergunta atualizada com sucesso!"),
 *             @OA\Property(property="question", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="text", type="string",
 *                  example="Texto atualizado da pergunta"),
 *                 @OA\Property(property="correct_answer", type="boolean",
 *                  example=true),
 *                 @OA\Property(property="image", type="string", nullable=true,
 *                  example="images/atualizada-imagem.jpg")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pergunta não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Pergunta não encontrada. Verifique o ID e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Erro de validação. Verifique os dados e tente novamente."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="text", type="array", @OA\Items(type="string")),
 *                 @OA\Property(property="correct_answer", type="array", @OA\Items(type="string")),
 *                 @OA\Property(property="image", type="array", @OA\Items(type="string"))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao atualizar a pergunta",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro inesperado ao atualizar a pergunta.")
 *         )
 *     )
 * )
 */

    // Atualizar uma pergunta
    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'], 404);
        }

        $validatedData = $request->validate([
            'text' => 'required|string',
            'correct_answer' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validação da imagem opcional
        ]);

        // Verifica e deleta a imagem antiga se uma nova foi enviada
        if ($request->hasFile('image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }

            // Armazena a nova imagem e atualiza o caminho
            $path = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $path;
        }

        // Atualiza os dados da pergunta com os valores validados
        $question->update($validatedData);

        return response()->json([
            'message' => 'Pergunta atualizada com sucesso!',
            'question' => $question,
        ], 200);
    }
/**
 * @OA\Delete(
 *     path="/api/questions/{id}",
 *     summary="Exclui uma pergunta",
 *     description="Exclui uma pergunta pelo seu ID e, se aplicável, remove a imagem associada.",
 *     operationId="deleteQuestion",
 *     tags={"Perguntas"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID da pergunta a ser excluída",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Pergunta e imagem removidas com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Pergunta e imagem removidas com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pergunta não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Pergunta não encontrada. Verifique o ID e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao remover a pergunta",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao remover a pergunta"),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar remover a pergunta.
 *              Por favor, tente novamente.")
 *         )
 *     )
 * )
 */

    // Excluir uma pergunta
    public function destroy($id)
    {
        $question = Question::find($id);

        // Verifica se a pergunta existe
        if (!$question) {
            return response()->json(['error' => 'Pergunta não encontrada. Verifique o ID e tente novamente.'], 404);
        }

        try {
            // Verifica se há uma imagem associada e tenta excluí-la
            if ($question->image) {
                $imagePath = $question->image;
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info("Imagem da pergunta removida com sucesso: {$imagePath}");
                } else {
                    Log::warning("Imagem da pergunta não encontrada no sistema de arquivos: {$imagePath}");
                }
            }

            // Remove o registro da pergunta
            $question->delete();
            Log::info("Pergunta com ID {$id} removida com sucesso.");

            return response()->json(['message' => 'Pergunta e imagem removidas com sucesso!'], 204);
        } catch (\Exception $e) {
            Log::error("Erro ao tentar remover a pergunta com ID {$id}: " . $e->getMessage());

            return response()->json([
                'error' => 'Erro ao remover a pergunta',
                'message' => 'Ocorreu um erro inesperado ao tentar remover a pergunta. Por favor, tente novamente.'
            ], 500);
        }
    }
}
