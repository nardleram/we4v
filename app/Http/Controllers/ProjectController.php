<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Groups\GetGroups;
use App\Actions\Projects\GetProjects;
use App\Actions\Projects\StoreProject;
use App\Http\Requests\StoreProjectRequest;

class ProjectController extends Controller
{
    public function __construct(GetProjects $getProjects, GetGroups $getGroups, StoreProject $storeProject)
    {
        $this->getProjects = $getProjects;
        $this->getGroups = $getGroups;
        $this->storeProject = $storeProject;
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

        return Inertia::render('MyProjects', [
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
