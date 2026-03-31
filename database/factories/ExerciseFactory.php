<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\TrainingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'training_plan_id' => TrainingPlan::factory(),
            'name' => fake()->randomElement([
                'Bench Press',
                'Squat',
                'Deadlift',
                'Pull Ups',
                'Shoulder Press',
            ]),
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->numberBetween(6, 15),
            'weight' => fake()->numberBetween(20, 120),
        ];
    }
}
