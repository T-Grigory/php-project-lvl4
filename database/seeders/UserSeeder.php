<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
  / \App\Models\User::factory(10)->create();   * @return void
     */
    public function run()
    {
        User::factory(10)->create();
    }
}
