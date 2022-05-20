<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = ['новый', 'в работе', 'на тестировании', 'завершен'];

        foreach ($statuses as $status) {
            TaskStatus::factory()->create([
                'name' => $status,
            ]);
        }
    }
}
