<?php

namespace Database\Factories;

use App\Models\Participation;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipationFactory extends Factory
{
    protected $model = Participation::class;

    /**
     * Define o estado padrão do model de Participation.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'quiz_id' => Quiz::factory(),
            'score' => $this->faker->numberBetween(10, 100), // Gera um score aleatório
            'completed_at' => $this->faker->dateTimeBetween('-1 week', 'now'), // Data aleatória de uma semana até o momento
        ];
    }
}
