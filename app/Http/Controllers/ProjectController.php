<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Notes\StoreNote;
use App\Actions\Groups\GetGroups;
use App\Actions\Projects\GetProjects;
use App\Actions\Projects\StoreProject;
use App\Actions\Projects\UpdateProject;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    private $getProjects;
    private $getGroups;
    private $storeProject;
    private $updateProject;
    private $storeNote;

    public function __construct(GetProjects $getProjects, GetGroups $getGroups, StoreProject $storeProject, UpdateProject $updateProject, StoreNote $storeNote)
    {
        $this->getProjects = $getProjects;
        $this->getGroups = $getGroups;
        $this->storeProject = $storeProject;
        $this->updateProject = $updateProject;
        $this->storeNote = $storeNote;
    }
    
    public function index() : object
    {
        return Inertia::render('MyProjects', [
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
        ]);
    }

    public function store(StoreProjectRequest $request) : object
    {
        $this->storeProject->handle($request);

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Project created']]);
    }

    public function update(UpdateProjectRequest $request)
    {
        $this->updateProject->handle($request);

        $request->note['body']
        ? $this->storeNote->handle($request->note)
        : null;

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Project updated']]);
    }
}
