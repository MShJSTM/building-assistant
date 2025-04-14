<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

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
        'postal_plate' => '12345',
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
    dd($response->json());
    $response->assertCreated()
             ->assertJson([
                 'project' => [
                     'name' => 'New Project',
                 ],
             ]);
});

it('can update a project', function () {
    //
});

it('can delete a project', function () {
    //
});

it('can see a project', function () {
    //
});

it('can assign a user to a project', function () {
    //
});