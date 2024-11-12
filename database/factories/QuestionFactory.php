<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'quiz_id' => Quiz::factory(),  // Cria um Quiz relacionado, se não especificado
            'text' => $this->faker->sentence,
            'correct_answer' => $this->faker->boolean,  // Verdadeiro ou falso
            'image' => 'images/' . $this->faker->image('public/storage/images', 640, 480, null, false), // Gera um caminho para imagem fake
        ];
    }
}
