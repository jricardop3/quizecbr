<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
