<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;

it('can assign project to another user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();

    $response = postJson('/api/projects/' . $project->id . '/assign', [
        'user_id' => User::factory()->create()->id,
        'role' => 'member',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertCreated()
             ->assertJson([
                 'message' => 'User assigned to project successfully.',
             ]);
});

it('only owner can assign his project to other users', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();

    $response = postJson('/api/projects/' . $project->id . '/assign', [
        'user_id' => User::factory()->create()->id,
        'role' => 'member',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertForbidden()
             ->assertJson([
                 'message' => 'You are not authorized to assign this project.',
             ]);
});

it('can detach users from his own projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);

    $response = deleteJson('/api/projects/' . $project->id . '/detach', [
        'user_id' => User::factory()->create()->id,
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => 'User detached from project successfully.',
             ]);
});