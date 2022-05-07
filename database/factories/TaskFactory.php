<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\TaskStatus;
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
            'name' => $this->faker->unique()->text(),
            'description' => $this->faker->text(),
            'status_id' => TaskStatus::all()->random()->id,
            'assigned_to_id' => User::all()->random()->id,
        ];
    }
}
