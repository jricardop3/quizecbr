<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'completed_at',
    ];

    /**
     * Relacionamento: uma participação pertence a um usuário.
     *
     * Cada registro de participação está vinculado a um único usuário.
     * Este relacionamento permite acessar o usuário que participou do quiz.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: uma participação pertence a um quiz.
     *
     * Cada participação está associada a um único quiz.
     * Este relacionamento permite acessar o quiz que o usuário participou.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
