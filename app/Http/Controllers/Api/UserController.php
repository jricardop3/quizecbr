<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Listar todos os usuários",
 *     tags={"Usuários"},
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Lista de usuários",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao@example.com"),
 *                 @OA\Property(property="role", type="string", example="user")
 *             )
 *         )
 *     )
 * )
 */

    /**
     * Exibir uma lista de usuários.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }
/**
 * @OA\Post(
 *     path="/api/register/user",
 *     summary="Criar um novo usuário",
 *     tags={"Usuários"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email"},
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", example="joao@example.com"),
 *             @OA\Property(property="role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuário criado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Usuário criado com sucesso!"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao@example.com"),
 *                 @OA\Property(property="role", type="string", example="user")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="E-mail já registrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="E-mail já registrado."),
 *             @OA\Property(property="message", type="string",
 *              example="Este e-mail já está associado a uma conta existente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro de validação."),
 *             @OA\Property(property="messages", type="object",
 *                 @OA\Property(property="name", type="array",
 *                 @OA\Items(type="string", example="O campo nome é obrigatório.")),
 *                 @OA\Property(property="email", type="array",
 *                 @OA\Items(type="string", example="O campo email é obrigatório e deve ser único."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao salvar o usuário no banco de dados",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro ao salvar o usuário no banco de dados."),
 *             @OA\Property(property="message", type="string", example="Verifique os dados e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro inesperado ao criar o usuário",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro inesperado ao criar o usuário."),
 *             @OA\Property(property="message", type="string", example="Por favor, tente novamente mais tarde.")
 *         )
 *     )
 * )
 */



    /**
     * Criar um novo usuário.
     */
    public function store(Request $request)
    {
        // Valida os campos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        // Verifica se o e-mail já está registrado
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'error' => 'E-mail já registrado.',
                'message' => 'Este e-mail já está associado a uma conta existente.',
            ], 409); // 409 Conflict
        }

        try {
            // Criação do usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role ?? 'user', // Define o papel padrão como 'user'
            ]);

            // Retorno de sucesso
            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => $user,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorno de erro específico de validação
            return response()->json([
                'error' => 'Erro de validação.',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Retorno de erro específico de banco de dados
            return response()->json([
                'error' => 'Erro ao salvar o usuário no banco de dados.',
                'message' => 'Verifique os dados e tente novamente.',
            ], 500);
        } catch (\Exception $e) {
            // Retorno de erro genérico para falhas inesperadas
            return response()->json([
                'error' => 'Erro inesperado ao criar o usuário.',
                'message' => 'Por favor, tente novamente mais tarde.',
            ], 500);
        }
    }

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Exibir um usuário específico",
 *     tags={"Usuários"},
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do usuário",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", example="joao@example.com"),
 *             @OA\Property(property="role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Usuário não encontrado")
 *         )
 *     )
 * )
 */
    /**
     * Exibir um usuário específico.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        return response()->json($user, 200);
    }
/**
 * @OA\Patch(
 *     path="/api/users/{id}",
 *     summary="Atualizar um usuário",
 *     tags={"Usuários"},
 *     description="Atualiza as informações de um usuário existente.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do usuário a ser atualizado",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", example="joao.silva@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário atualizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso!"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao.silva@example.com"),
 *                 @OA\Property(property="role", type="string", example="user")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Usuário não encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro de validação."),
 *             @OA\Property(property="messages", type="object",
 *                 @OA\Property(property="name", type="array",
 *                 @OA\Items(type="string", example="O campo nome é obrigatório.")),
 *                 @OA\Property(property="email", type="array",
 *                 @OA\Items(type="string", example="O campo email é obrigatório e deve ser único."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao atualizar o usuário",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro ao atualizar o usuário no banco de dados."),
 *             @OA\Property(property="message", type="string", example="Verifique os dados e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro inesperado ao atualizar o usuário",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro inesperado ao atualizar o usuário."),
 *             @OA\Property(property="message", type="string", example="Por favor, tente novamente mais tarde.")
 *         )
 *     )
 * )
 */


    /**
     * Atualizar um usuário existente.
     */
    public function update(Request $request, $id)
    {
        // Busca o usuário pelo ID fornecido
        $user = User::find($id);

        // Retorna um erro 404 caso o usuário não seja encontrado
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        try {
            // Valida os campos de entrada
            $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            ]);

            // Atualiza apenas os campos nome e email, caso estejam presentes no request
            $user->update($request->only(['name', 'email']));

            // Retorno de sucesso
            return response()->json([
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorno de erro específico de validação
            return response()->json([
                'error' => 'Erro de validação.',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Retorno de erro específico do banco de dados
            return response()->json([
                'error' => 'Erro ao atualizar o usuário no banco de dados.',
                'message' => 'Verifique os dados e tente novamente.',
            ], 500);
        } catch (\Exception $e) {
            // Retorno de erro genérico para falhas inesperadas
            return response()->json([
                'error' => 'Erro inesperado ao atualizar o usuário.',
                'message' => 'Por favor, tente novamente mais tarde.',
            ], 500);
        }
    }
/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Excluir um usuário",
 *     tags={"Usuários"},
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID do usuário",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Usuário removido com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Usuário removido com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Usuário não encontrado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao remover o usuário",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Erro ao remover o usuário"),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar remover o usuário.")
 *         )
 *     )
 * )
 */

    /**
     * Remover um usuário específico.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário removido com sucesso!'], 204);
    }
}
