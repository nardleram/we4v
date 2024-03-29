<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Inertia\Inertia;
use App\Models\Project;
use App\Actions\Notes\StoreNote;
use App\Actions\Groups\GetGroups;
use App\Actions\Tasks\GetAdminTasks;
use App\Actions\Teams\GetAdminTeams;
use App\Actions\Projects\GetProjects;
use App\Actions\Groups\GetAdminGroups;
use App\Actions\Projects\StoreProject;
use App\Actions\Projects\UpdateProject;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function __construct(
        private GetProjects $getProjects, 
        private GetAdminTasks $getAdminTasks,
        private GetAdminTeams $getAdminTeams,
        private GetGroups $getGroups, 
        private GetAdminGroups $getAdminGroups,
        private StoreProject $storeProject,
        private UpdateProject $updateProject, 
        private StoreNote $storeNote
    ) {}
    
    public function index() : object
    {
        return Inertia::render('MyProjects', [
            'myProjects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id())
        ]);
    }

    public function store(StoreProjectRequest $request) : object
    {
        $this->storeProject->handle($request);

        return redirect()->back()->with([
            'myProjects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Project created']]);
    }

    public function update(UpdateProjectRequest $request)
    {
        $this->updateProject->handle($request);

        $request->note['body']
        ? $this->storeNote->handle($request->note)
        : null;

        return redirect()->back()->with([
            'myProjects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Project updated']]);
    }

    public function destroy (Project $project)
    {
        Note::where('noteable_id', $project->id)->forceDelete();

        Project::find($project->id)->forceDelete();

        return redirect()->back()->with([
            'flash' => ['message' => 'Project deleted']]);
    }
}
