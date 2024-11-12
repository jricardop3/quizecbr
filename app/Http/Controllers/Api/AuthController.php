<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/login/admin",
 *     summary="Login do Administrador",
 *     tags={"Autenticação"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", example="admin@example.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login bem-sucedido",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string",
 *              example="Bem-vindo, administrador!"),
 *             @OA\Property(property="token", type="string",
 *              example="7|LifStxIv7mEDSFK0KnnGeH4hJSlwHV1k1KMXawVZ50163912"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin User"),
 *                 @OA\Property(property="email", type="string", example="admin@example.com"),
 *                 @OA\Property(property="role", type="string", example="admin")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Acesso negado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Acesso negado.
 *              Este email não pertence a um administrador.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Senha incorreta",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Senha incorreta. Verifique e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Formato de email inválido",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *             example="O campo email deve ser um endereço de email válido.")
 *         )
 *     )
 * )
 */

    public function adminLogin(Request $request)
    {
        // Validação inicial do formato dos campos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Busca o usuário com base no email
        $user = User::where('email', $request->email)->first();

        // Verifica se o email pertence a um administrador
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Acesso negado. Este email não pertence a um administrador.'], 403);
        }

        // Verifica se a senha está correta
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Senha incorreta. Verifique e tente novamente.'], 401);
        }

        // Gera o token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retorna uma resposta de sucesso com o token e os dados do usuário
        return response()->json([
            'message' => 'Bem-vindo, administrador!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

/**
 * @OA\Post(
 *     path="/api/login/user",
 *     summary="Login de Usuário",
 *     tags={"Autenticação"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email"},
 *             @OA\Property(property="name", type="string", example="Nome do Usuário"),
 *             @OA\Property(property="email", type="string", example="usuario@example.com")
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login bem-sucedido",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Bem-vindo, usuário!"),
 *             @OA\Property(property="token", type="string",
 *                  example="7|LifStxIv7mEDSFK0KnnGeH4hJSlwHV1k1KMXawVZ50163912"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Nome do Usuário"),
 *                 @OA\Property(property="email", type="string", example="usuario@example.com"),
 *                 @OA\Property(property="role", type="string", example="user")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Usuário não encontrado. Verifique o email informado.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Nome do usuário incorreto",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Nome de usuário incorreto. Verifique os dados e tente novamente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Acesso negado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *          example="Acesso negado. Este email não pertence a um usuário regular.")
 *         )
 *     )
 * )
 */

    public function userLogin(Request $request)
    {
        // Validação inicial do formato dos campos
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email'
        ]);

        // Busca o usuário com base no email
        $user = User::where('email', $request->email)->first();

        // Verifica se o usuário existe
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado. Verifique o email informado.'], 404);
        }

        // Verifica se o nome do usuário corresponde
        if ($user->name !== $request->name) {
            return response()->json([
                'error' => 'Nome de usuário incorreto. Verifique os dados e tente novamente.'], 401);
        }


        // Verifica se o usuário possui o papel de 'user'
        if ($user->role !== 'user') {
            return response()->json(['error' => 'Acesso negado. Este email não pertence a um usuário regular.'], 403);
        }

        // Gera o token de autenticação
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retorna uma resposta de sucesso com o token e os dados do usuário
        return response()->json([
            'message' => 'Bem-vindo, usuário!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout do usuário",
 *     tags={"Autenticação"},
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Usuário não autenticado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Usuário não autenticado. Não há sessão ativa.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao realizar o logout",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string",
 *              example="Erro ao realizar o logout."),
 *             @OA\Property(property="message", type="string",
 *              example="Ocorreu um erro inesperado ao tentar encerrar a sessão. Por favor, tente novamente.")
 *         )
 *     )
 * )
 */

    public function logout(Request $request)
    {
        try {
            // Verifica se o usuário está autenticado e possui um token ativo
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
                return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
            }

            // Retorna um erro caso o usuário não esteja autenticado
            return response()->json(['error' => 'Usuário não autenticado. Não há sessão ativa.'], 401);
        } catch (\Exception $e) {
            // Caso ocorra algum erro inesperado no logout
            return response()->json([
                'error' => 'Erro ao realizar o logout.',
                'message' => 'Ocorreu um erro inesperado ao tentar encerrar a sessão. Por favor, tente novamente.'
            ], 500);
        }
    }
}
