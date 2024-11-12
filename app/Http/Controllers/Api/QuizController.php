<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/quizzes",
 *     summary="Listar todos os quizzes",
 *     description="Retorna uma lista com todos os quizzes disponíveis.",
 *     operationId="listQuizzes",
 *     tags={"Quizzes"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de quizzes retornada com sucesso",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer",
 *                  description="ID do quiz"),
 *                 @OA\Property(property="title", type="string",
 *                  description="Título do quiz"),
 *                 @OA\Property(property="description", type="string",
 *                  description="Descrição do quiz"),
 *                 @OA\Property(property="image", type="string",
 *                  description="Caminho da imagem do quiz"),
 *                 @OA\Property(property="created_at", type="string", format="date-time",
 *                  description="Data de criação do quiz"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time",
 *                  description="Data de atualização do quiz")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Nenhum quiz encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Nenhum quiz encontrado no momento.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao buscar os quizzes",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao buscar os quizzes"),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar buscar os quizzes. Por favor, tente novamente.")
 *         )
 *     )
 * )
 */
    // Listar todos os quizzes
    public function index()
    {
        try {
            $quizzes = Quiz::all();

            if ($quizzes->isEmpty()) {
                return response()->json(['message' => 'Nenhum quiz encontrado no momento.'], 404);
            }

            return response()->json($quizzes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao buscar os quizzes',
                'message' => 'Ocorreu um erro inesperado ao tentar buscar os quizzes. Por favor, tente novamente.'
            ], 500);
        }
    }
 /**
 * @OA\Post(
 *     path="/api/quizzes",
 *     summary="Criação de um novo quiz",
 *     description="Cria um novo quiz com título, descrição e imagem opcional.",
 *     tags={"Quizzes"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"title", "description"},
 *                 @OA\Property(property="title", type="string", maxLength=255, example="Meu Novo Quiz"),
 *                 @OA\Property(property="description", type="string", example="Descrição do quiz"),
 *                 @OA\Property(property="image", type="file", description="Imagem opcional do quiz")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Quiz criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Quiz criado com sucesso!"),
 *             @OA\Property(property="quiz", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Meu Novo Quiz"),
 *                 @OA\Property(property="description", type="string", example="Descrição do quiz"),
 *                 @OA\Property(property="image", type="string",
 *                  example="images/nome-da-imagem.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="date-time",
 *                  example="2024-11-10T01:12:39.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time",
 *                  example="2024-11-10T01:12:39.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro de validação"),
 *             @OA\Property(property="messages", type="object",
 *                 @OA\Property(property="title", type="array", @OA\Items(type="string",
 *                  example="O campo título é obrigatório.")),
 *                 @OA\Property(property="description", type="array", @OA\Items(type="string",
 *                  example="O campo descrição é obrigatório."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao criar o quiz",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro ao criar o quiz"),
 *             @OA\Property(property="message", type="string",
 *              example="Houve um problema ao salvar o quiz no banco de dados. Verifique os campos e tente novamente.")
 *         )
 *     )
 * )
 */

    // Criar um novo quiz
    public function store(Request $request)
    {
        try {
            // Validação dos campos
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validações para imagem
            ]);

            // Instância do quiz
            $quiz = new Quiz();
            $quiz->title = $validatedData['title'];
            $quiz->description = $validatedData['description'];

            // Tratamento de upload de imagem, caso presente
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images', 'public');
                $quiz->image = $path;
            }

            // Salvar o quiz no banco
            DB::enableQueryLog();
            $quiz->save();
            Log::info(DB::getQueryLog());

            return response()->json([
                'message' => 'Quiz criado com sucesso!',
                'quiz' => $quiz,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorno de erro de validação específico
            return response()->json([
                'error' => 'Erro de validação',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Erro de consulta ao banco de dados
            return response()->json([
                'error' => 'Erro ao criar o quiz',
                'message' => 'Houve um problema ao salvar o quiz no banco de dados. 
                Verifique os campos e tente novamente.',
            ], 500);
        } catch (\Exception $e) {
            // Erro genérico
            return response()->json([
                'error' => 'Erro ao criar o quiz',
                'message' => 'Ocorreu um erro inesperado. Por favor, tente novamente.',
            ], 500);
        }
    }
/**
 * @OA\Get(
 *     path="/api/quizzes/{id}",
 *     summary="Exibir um quiz específico",
 *     description="Retorna os detalhes de um quiz pelo seu ID.",
 *     tags={"Quizzes"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do quiz",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalhes do quiz",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string",
 *              example="Meu Novo Quiz"),
 *             @OA\Property(property="description", type="string",
 *              example="Descrição do quiz"),
 *             @OA\Property(property="image", type="string",
 *              example="images/nome-da-imagem.jpg"),
 *             @OA\Property(property="created_at", type="string", format="date-time",
 *              example="2024-11-10T01:12:39.000000Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time",
 *              example="2024-11-10T01:12:39.000000Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Quiz não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Quiz não encontrado. Verifique o ID e tente novamente.")
 *         )
 *     )
 * )
 */
    // Exibir um quiz específico
    public function show($id)
    {
        // Busca o quiz pelo ID
        $quiz = Quiz::find($id);

        // Retorna erro se o quiz não for encontrado
        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'], 404);
        }

        // Retorna o quiz encontrado
        return response()->json($quiz, 200);
    }
/**
 * @OA\Patch(
 *     path="/api/quizzes/{id}",
 *     summary="Atualizar um quiz existente",
 *     tags={"Quizzes"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do quiz a ser atualizado",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string",
 *              description="Título do quiz", example="Título Atualizado"),
 *             @OA\Property(property="description", type="string",
 *              description="Descrição do quiz", example="Descrição atualizada do quiz."),
 *             @OA\Property(property="image", type="file",
 *              description="Imagem do quiz (opcional)", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz atualizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Quiz atualizado com sucesso!"),
 *             @OA\Property(
 *                 property="quiz",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string",
 *                  example="Título Atualizado"),
 *                 @OA\Property(property="description", type="string",
 *                  example="Descrição atualizada do quiz."),
 *                 @OA\Property(property="image", type="string",
 *                  example="images/atualizado.jpg"),
 *                 @OA\Property(property="created_at", type="string",
 *                  format="date-time", example="2024-11-10T01:12:39.000000Z"),
 *                 @OA\Property(property="updated_at", type="string",
 *                  format="date-time", example="2024-11-10T02:15:47.000000Z")
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
 *             @OA\Property(property="error", type="string", example="Erro de validação."),
 *             @OA\Property(
 *                 property="messages",
 *                 type="object",
 *                 @OA\Property(
 *                     property="title",
 *                     type="array",
 *                     @OA\Items(type="string", example="O campo título deve ser uma string.")
 *                 ),
 *                 @OA\Property(
 *                     property="image",
 *                     type="array",
 *                     @OA\Items(type="string",
 *                      example="O campo imagem deve ser um arquivo do tipo: jpeg, png, jpg, gif, svg.")
 *                 )
 *             )
 *         )
 *     ),
 * )
 */

    // Atualizar um quiz
    public function update(Request $request, $id)
    {
        // Encontrar o quiz pelo ID fornecido
        $quiz = Quiz::find($id);

        // Verifica se o quiz existe
        if (!$quiz) {
            return response()->json(['error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'], 404);
        }

        try {
            // Validação dos campos recebidos
            $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorno específico para erro de validação
            return response()->json([
                'error' => 'Erro de validação.',
                'messages' => $e->errors(),
            ], 422);
        }

        try {
            // Atualiza os campos de título e descrição, caso sejam enviados
            if ($request->has('title')) {
                $quiz->title = $request->title;
            }
            if ($request->has('description')) {
                $quiz->description = $request->description;
            }

            // Tratamento para upload de imagem
            if ($request->hasFile('image')) {
                // Remove a imagem anterior, se existir
                if ($quiz->image) {
                    Storage::disk('public')->delete($quiz->image);
                }

                // Armazena a nova imagem e define o caminho na coluna 'image' do banco de dados
                $path = $request->file('image')->store('images', 'public');
                $quiz->image = $path;
            }

            // Salva as atualizações no banco de dados
            $quiz->save();

            // Retorna uma resposta de sucesso
            return response()->json([
                'message' => 'Quiz atualizado com sucesso!',
                'quiz' => $quiz->fresh() // Retorna o quiz atualizado para garantir os dados corretos
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Retorno de erro específico de banco de dados
            return response()->json([
                'error' => 'Erro ao atualizar o quiz no banco de dados.',
                'message' => 'Verifique os dados e tente novamente.',
            ], 500);
        } catch (\Exception $e) {
            // Retorno de erro genérico para falhas inesperadas
            return response()->json([
                'error' => 'Erro inesperado ao atualizar o quiz.',
                'message' => 'Por favor, tente novamente mais tarde.',
            ], 500);
        }
    }
/**
 * @OA\Delete(
 *     path="/api/quizzes/{id}",
 *     summary="Excluir um quiz existente",
 *     tags={"Quizzes"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do quiz a ser excluído",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Quiz e imagem removidos com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Quiz e imagem removidos com sucesso!")
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
 *         response=500,
 *         description="Erro ao remover o quiz",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao remover o quiz"),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar remover o quiz. Por favor, tente novamente.")
 *         )
 *     )
 * )
 */

    // Excluir um quiz
    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        // Verifica se o quiz existe
        if (!$quiz) {
            return response()->json([
                'error' => 'Quiz não encontrado. Verifique o ID e tente novamente.'
            ], 404);
        }

        try {
            // Verifica se há uma imagem associada e tenta excluí-la
            if ($quiz->image) {
                $imagePath = $quiz->image;
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                    Log::info("Imagem removida com sucesso: {$imagePath}");
                } else {
                    Log::warning("Imagem não encontrada no sistema de arquivos: {$imagePath}");
                }
            }

            // Remove o registro do quiz
            $quiz->delete();
            Log::info("Quiz com ID {$id} removido com sucesso.");

            // Responde com um código 204 sem conteúdo
            return response()->json(['message' => 'Quiz e imagem removidos com sucesso!'], 204);
        } catch (\Exception $e) {
            Log::error("Erro ao tentar remover o quiz com ID {$id}: " . $e->getMessage());

            // Retorna erro genérico
            return response()->json([
                'error' => 'Erro ao remover o quiz',
                'message' => 'Ocorreu um erro inesperado ao tentar remover o quiz. Por favor, tente novamente.'
            ], 500);
        }
    }
}
