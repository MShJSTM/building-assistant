<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('owner can assign users to his projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();

    $response = postJson('/api/projects/'.$project->slug.'/users', [
        'phone' => '0912345678', 
        'role' => 'member',
        'name' => 'John Doe',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertForbidden()
             ->assertJson([
                 'message' => 'You do not have permission to update this project.',
             ]);

    $user->projects()->attach($project, ['role' => 'owner']);
             
    $response = postJson('/api/projects/'.$project->slug.'/users', [
        'phone' => '0912345678', 
        'role' => 'member',
        'name' => 'John Doe',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertCreated()
             ->assertJson([
                 'message' => 'User assigned to project successfully.',
             ]);

    $this->assertDatabaseHas('project_user', [
        'user_id' => User::where('phone', '0912345678')->first()->id,
        'project_id' => $project->id,
        'role' => 'member',
    ]);
});

it('can detach users from his own projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);

    $response = deleteJson('/api/projects/'.$project->slug.'/users', [
        'user_id' => User::factory()->create()->id,
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => 'User detached from project successfully.',
             ]);
});

test('assigned user can see his projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'member']);

    $response = getJson('/api/projects', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'projects' => [
                     [
                         'id' => $project->id,
                         'name' => $project->name,
                     ],
                 ],
             ]);
});

it('can see users of his projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);

    $response = getJson('/api/projects/' . $project->slug . '/users', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'users' => [
                     [
                         'id' => $user->id,
                         'name' => $user->name,
                         'phone' => $user->phone,
                         'role' => 'owner',
                     ],
                 ],
             ]);
});