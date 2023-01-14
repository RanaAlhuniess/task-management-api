<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'due_date' => $this->faker->date()
        ];
    }
}
