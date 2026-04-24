<?php

namespace Database\Factories;

use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingPlan>
 */
class TrainingPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Chest Day',
                'Back Day',
                'Leg Day',
                'Push Day',
                'Pull Day',
            ]),
        ];
    }
}
