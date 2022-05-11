<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Actions\Notes\StoreNote;
use App\Actions\Tasks\StoreTask;
use App\Actions\Groups\GetGroups;
use App\Actions\Tasks\UpdateTask;
use App\Actions\Teams\GetAdminTeams;
use App\Actions\Projects\GetProjects;
use App\Actions\Groups\GetAdminGroups;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Actions\Memberships\StoreMemberships;
use App\Actions\Memberships\UpdateTaskMemberships;
use App\Models\Membership;

class TaskController extends Controller
{
    private $getGroups;
    private $getAdminGroups;
    private $getAdminTeams;
    private $getProjects;
    private $storeTask;
    private $storeNote;
    private $storeMemberships;
    private $updateTaskMemberships;
    private $updateTask;

    public function __construct(
        GetGroups $getGroups,
        GetAdminGroups $getAdminGroups,
        GetAdminTeams $getAdminTeams,
        GetProjects $getProjects,
        StoreTask $storeTask,
        UpdateTask $updateTask,
        StoreNote $storeNote,
        StoreMemberships $storeMemberships,
        UpdateTaskMemberships $updateTaskMemberships)
    {
        $this->getGroups = $getGroups;
        $this->getAdminGroups = $getAdminGroups;
        $this->getAdminTeams = $getAdminTeams;
        $this->getProjects = $getProjects;
        $this->storeTask = $storeTask;
        $this->updateTask = $updateTask;
        $this->storeNote = $storeNote;
        $this->storeMemberships = $storeMemberships;
        $this->updateTaskMemberships = $updateTaskMemberships;
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->storeTask->handle($request);

        if (count($request->members)) {
            $this->storeMemberships->handle($request, $task->id);
        }

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Task created']]);
    }

    public function update(UpdateTaskRequest $request) : object
    {
        $this->updateTask->handle($request);

        count($request->members) > 0
        ? $this->updateTaskMemberships->handle($request, $request->id)
        : null;

        $request->taskNote['body']
        ? $this->storeNote->handle($request->taskNote)
        : null;

        $request->projectNote['body']
        ? $this->storeNote->handle($request->projectNote)
        : null;

        return redirect()->back()->with([
            'myprojects' => $this->getProjects->handle(auth()->id()),
            'myGroups' => array_merge(
                $this->getGroups->handle(auth()->id()), 
                $this->getAdminGroups->handle(auth()->id())
            ),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Task updated']
        ]);
    }

    public function destroy(Task $task) 
    {
        Membership::where('membershipable_id', $task->id)->forceDelete();

        Task::find($task->id)->forceDelete();

        return redirect()->back()->with([
            'flash' => ['message' => 'Task deleted']
        ]);
    }
}
