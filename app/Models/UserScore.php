<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
    ];

    /**
     * Relacionamento: a pontuação pertence a um usuário.
     *
     * Este relacionamento permite acessar o usuário a quem a pontuação pertence.
     * Cada pontuação está associada a um único usuário, refletindo o desempenho dele em um quiz específico.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: a pontuação pertence a um quiz.
     *
     * Este relacionamento permite acessar o quiz associado à pontuação.
     * Cada pontuação está vinculada a um único quiz, permitindo monitorar o desempenho do usuário naquele quiz.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
