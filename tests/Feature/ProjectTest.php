<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\getJson;

it('can see projects', function () {
    $user = User::factory()->create();
    $token = $user->createToken('mobile-login')->plainTextToken;

    $user->projects()->attach(Project::factory(3)->create(), ['role' => 'owner']);

    $response = getJson('/api/projects', [
        'Authorization' => 'Bearer ' . $token,
    ]);


    // dd($response->json());
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
    //
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