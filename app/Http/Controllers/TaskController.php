<?php

namespace App\Http\Controllers;

use App\Actions\Tasks\StoreTask;
use App\Actions\Groups\GetGroups;
use App\Actions\Projects\GetProjects;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    private $storeTask;

    public function __construct(GetGroups $getGroups, StoreTask $storeTask, GetProjects $getProjects)
    {
        $this->getGroups = $getGroups;
        $this->storeTask = $storeTask;
        $this->getProjects = $getProjects;
    }

    public function store(StoreTaskRequest $request)
    {
        $this->storeTask->handle($request);

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Task created']]);
    }
}
