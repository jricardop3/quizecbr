<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'text',
        'image',
        'correct_answer', // True para "Verdadeiro", False para "Falso"
    ];

    /**
     * Relacionamento: uma pergunta pertence a um quiz.
     *
     * Cada pergunta está associada a um único quiz.
     * Este relacionamento permite acessar o quiz ao qual a pergunta pertence.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relacionamento: uma pergunta possui muitas respostas de usuário.
     *
     * Uma pergunta pode ter várias respostas de diferentes usuários.
     * Este relacionamento permite acessar todas as respostas dos usuários para uma pergunta específica.
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
