<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can see projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $user->projects()->attach(Project::factory(3)->create(), ['role' => 'owner']);

    $response = getJson('/api/projects', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJsonStructure([
                 'projects' => [
                     '*' => [
                         'id',
                         'name',
                     ],
                 ],
             ]);
});

it('can create a project', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;

    $response = postJson('/api/projects', [
        'name' => 'New Project',
        'slug' => 'new-project',
        'project_type' => 'personal',
        'address' => '123 Main St',
        'postal_code' => '12345',
        'land_area' => 1000,
        'building_area' => 500,
        'structure_type' => 'residential',
        'start_date' => '2023-01-01',
        'end_date' => '2023-12-31',
        'permit_start_date' => '2023-01-01',
        'permit_end_date' => '2023-12-31',
        'images' => [
            // Assuming you have a test image in the storage
            'image1' => UploadedFile::fake()->image('image1.jpg'),
            'image2' => UploadedFile::fake()->image('image2.jpg'),
        ],
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);
    
    $response->assertCreated()
             ->assertJson([
                 'project' => [
                     'name' => 'New Project',
                 ],
             ]);
});

it('can update a project', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;
    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);

    $response = putJson('/api/projects/' . $project->slug, [
        'name' => 'Updated Project',
        'project_type' => 'commercial',
        'address' => '456 Main St',
        'postal_code' => '67890',
        'land_area' => 2000,
        'building_area' => 1000,
        'structure_type' => 'commercial',
        'start_date' => '2023-02-01',
        'end_date' => '2024-12-31',
        'permit_start_date' => '2023-02-01',
        'permit_end_date' => '2024-12-31',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'project' => [
                     'name' => 'Updated Project',
                 ],
             ]);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project',
        'project_type' => 'commercial',
        'address' => '456 Main St',
        'postal_code' => '67890',
        'land_area' => 2000,
        'building_area' => 1000,
        'structure_type' => 'commercial',
        'start_date' => '2023-02-01',
        'end_date' => '2024-12-31',
        'permit_start_date' => '2023-02-01',
        'permit_end_date' => '2024-12-31',
    ]);
    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
});

it('can delete a project', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;
    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);

    $response = deleteJson('/api/projects/' . $project->slug, [], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertOk()
             ->assertJson([
                 'message' => __('Project deleted successfully'),
             ]);
    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
        'name' => $project->name,
    ]);
});

it('can see a project', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth-token')->plainTextToken;
    $project = Project::factory()->create();
    $user->projects()->attach($project, ['role' => 'owner']);
    $response = getJson('/api/projects/' . $project->slug, [
        'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertOk()
             ->assertJson([
                 'project' => [
                     'id' => $project->id,
                     'name' => $project->name,
                 ],
             ]);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => $project->name,
    ]);
    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $user->id,
        'role' => 'owner',
    ]);
});

it('can assign a user to a project', function () {
    //
});