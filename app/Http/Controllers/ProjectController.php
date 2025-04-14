<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'projects' => auth()->user()->projects()->get()->each(function ($project){
                $project->image = $project->getFirstMediaUrl('images');
            }),
        ])->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        auth()->user()->projects()->attach($project->id, ['role' => 'owner']);
        collect($request->file('images'))->each(function ($image) use ($project) {
            $project->addMedia($image)->toMediaCollection('images');
        });

        return response()->json([
            'project' => $project,
            'message' => __('Project created successfully'),
        ])->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {   $project->images = $project->getMedia('images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
            ];
        });

        return response()->json([
            'project' => $project,
        ])->setStatusCode(200);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        if ($request->hasFile('images')) {
            $project->clearMediaCollection('images');
            collect($request->file('images'))->each(function ($image) use ($project) {
                $project->addMedia($image)->toMediaCollection('images');
            });
        }
        return response()->json([
            'project' => $project,
            'message' => __('Project updated successfully'),
        ])->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => __('Project deleted successfully'),
        ])->setStatusCode(200);
    }
}
