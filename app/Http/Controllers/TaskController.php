<?php

namespace App\Http\Controllers;

use App\Actions\Tasks\StoreTask;
use App\Actions\Groups\GetGroups;
use App\Actions\Notes\StoreNote;
use App\Actions\Tasks\UpdateTask;
use App\Actions\Projects\GetProjects;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    private $storeTask;
    private $getGroups;
    private $getProjects;
    private $updateTask;
    private $storeNote;

    public function __construct(GetGroups $getGroups, GetProjects $getProjects, StoreTask $storeTask, UpdateTask $updateTask, StoreNote $storeNote)
    {
        $this->getGroups = $getGroups;
        $this->getProjects = $getProjects;
        $this->storeTask = $storeTask;
        $this->updateTask = $updateTask;
        $this->storeNote = $storeNote;
    }

    public function store(StoreTaskRequest $request)
    {
        $this->storeTask->handle($request);

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Task created']]);
    }

    public function update(UpdateTaskRequest $request) : object
    {
        $this->updateTask->handle($request);

        $request->note['body']
        ? $this->storeNote->handle($request->note)
        : null;

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Task updated']]);
    }
}
