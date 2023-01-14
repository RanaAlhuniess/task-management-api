<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\Task;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'name' => 'Rana'
        ]);
        $categories = Category::factory(5)->create();
        
        Task::factory(35)
        ->create([
            'created_by' => $user->id
        ]);
        // Populate the pivot table
        Task::all()->each(function ($task) use ($categories) { 
            $task->categories()->attach(
                $categories->random(rand(1, 5))->pluck('id')->toArray()
            ); 
        });
    }
}
