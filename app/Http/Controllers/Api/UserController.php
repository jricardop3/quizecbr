<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Exibir uma lista de usuários.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

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
