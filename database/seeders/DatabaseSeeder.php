<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $demo = User::query()->updateOrCreate(
            ['email' => 'demo@todopro.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password123'),
            ]
        );

        if ($demo->tasks()->count() < 20) {
            Task::factory(20)->create(['user_id' => $demo->id]);
        }

        User::factory(2)->create()->each(function (User $user) {
            Task::factory(8)->create(['user_id' => $user->id]);
        });
    }
}
