<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence, // Gera um título aleatório
            'description' => $this->faker->paragraph, // Gera uma descrição aleatória
            'image' => 'images/' . $this->faker->image('public/storage/images', 640, 480, null, false), // Gera um caminho para imagem fake
        ];
    }
}
