<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Notes\StoreNote;
use App\Actions\Groups\GetGroups;
use App\Actions\Tasks\GetAdminTasks;
use App\Actions\Projects\GetProjects;
use App\Actions\Groups\GetAdminGroups;
use App\Actions\Projects\StoreProject;
use App\Actions\Projects\UpdateProject;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    private $getProjects;
    private $getAdminTasks;
    private $getGroups;
    private $storeProject;
    private $updateProject;
    private $storeNote;

    public function __construct(
        GetProjects $getProjects, 
        GetAdminTasks $getAdminTasks,
        GetGroups $getGroups, 
        GetAdminGroups $getAdminGroups,
        StoreProject $storeProject, 
        UpdateProject $updateProject, 
        StoreNote $storeNote)
    {
        $this->getProjects = $getProjects;
        $this->getAdminTasks = $getAdminTasks;
        $this->getGroups = $getGroups;
        $this->getAdminGroups = $getAdminGroups;
        $this->storeProject = $storeProject;
        $this->updateProject = $updateProject;
        $this->storeNote = $storeNote;
    }
    
    public function index() : object
    {
        return Inertia::render('MyProjects', [
            'myProjects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTasks' => $this->getAdminTasks->handle(auth()->id()),
        ]);
    }

    public function store(StoreProjectRequest $request) : object
    {
        $this->storeProject->handle($request);

        return redirect()->back()->with([
            'myProjects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminTasks' => $this->getAdminTasks->handle(auth()->id()),
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
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminTasks' => $this->getAdminTasks->handle(auth()->id()),
            'flash' => ['message' => 'Project updated']]);
    }

    private function cmp($a, $b)
    {
        return strcmp($a["group_name"], $b["group_name"]);
    }
}
