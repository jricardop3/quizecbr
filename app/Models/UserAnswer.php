<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer', // True para "Verdadeiro", False para "Falso"
        'is_correct',
    ];

    /**
     * Relacionamento: a resposta pertence a um usuário.
     *
     * Este relacionamento permite acessar o usuário que deu uma resposta específica.
     * Cada resposta está associada a um único usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: a resposta pertence a uma pergunta.
     *
     * Este relacionamento permite acessar a pergunta à qual a resposta se refere.
     * Cada resposta está vinculada a uma única pergunta dentro de um quiz.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
