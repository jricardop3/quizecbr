<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    /**
     * Relacionamento: um quiz possui muitas perguntas.
     * 
     * Cada quiz pode ter múltiplas perguntas associadas.
     * Este relacionamento permite acessar todas as perguntas de um quiz específico.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relacionamento: um quiz possui muitas participações.
     * 
     * Cada quiz pode ter várias participações de diferentes usuários.
     * Este relacionamento permite acessar todas as participações relacionadas a um quiz.
     */
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    /**
     * Relacionamento: um quiz possui muitas pontuações de usuários.
     * 
     * Cada quiz registra a pontuação dos usuários que participam.
     * Este relacionamento permite acessar todas as pontuações dos usuários para um quiz específico.
     */
    public function scores()
    {
        return $this->hasMany(UserScore::class);
    }

}
