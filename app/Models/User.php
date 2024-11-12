<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Adicionando a coluna role para diferenciar entre admin e user
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relacionamento: um usuário pode ter várias participações em quizzes.
     *
     * Este relacionamento permite acessar todas as participações associadas a um usuário específico,
     * incluindo detalhes sobre os quizzes em que ele participou e as pontuações.
     */
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    /**
     * Relacionamento: um usuário possui muitas pontuações em quizzes.
     *
     * Cada usuário acumula pontuações em diferentes quizzes, que são registradas em UserScore.
     * Este relacionamento permite acessar as pontuações de um usuário.
     */
    public function scores()
    {
        return $this->hasMany(UserScore::class);
    }

    /**
     * Relacionamento: um usuário possui várias respostas a perguntas.
     *
     * Este relacionamento permite acessar todas as respostas (verdadeiras ou falsas)
     * que o usuário deu para perguntas em diferentes quizzes.
     */
    public function answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
