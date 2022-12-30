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
    public function __construct(
        private GetGroups $getGroups,
        private GetAdminGroups $getAdminGroups,
        private GetAdminTeams $getAdminTeams,
        private GetProjects $getProjects,
        private StoreTask $storeTask,
        private UpdateTask $updateTask,
        private StoreNote $storeNote,
        private StoreMemberships $storeMemberships,
        private UpdateTaskMemberships $updateTaskMemberships
    ) {}

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

        if ($request->projectNote) {
            $request->projectNote['body']
            ? $this->storeNote->handle($request->projectNote)
            : null;
        }

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
