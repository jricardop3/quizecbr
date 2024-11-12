<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Executa o seeder para criar um usuário administrador padrão.
     *
     * Este método cria um usuário administrador com credenciais padrão,
     * útil para acessar o sistema com permissões administrativas logo
     * após a instalação, sem a necessidade de criar uma conta manualmente.
     */
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com', // E-mail padrão para o admin
            'password' => Hash::make('password'), // Senha padrão (deve ser alterada em produção)
            'role' => 'admin', // Define a função do usuário como administrador
        ]);
    }
}
