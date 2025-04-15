<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(10)->create()->each(function ($project) {
            $project->users()->attach(User::factory(3)->create(), ['role' => 'member']);
        });

        User::all()->each(function ($user){
            $project = Project::factory()->create();
            $project->users()->attach($user->id, ['role' => 'owner']);
        });
    }
}
