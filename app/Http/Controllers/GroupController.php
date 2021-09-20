<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Http\Requests\StoreGroupRequest;

class GroupController extends Controller
{
    public function index(GetGroups $action) : object
    {
        return Inertia::render('MyGroups', [
            'mygroups' => $action->handle(auth()->id())
        ]);
    }

    public function store(StoreGroupRequest $request, StoreGroup $action, GetGroups $getGroups) : object
    {
        $group = $action->storeGroup($request);

        $action->storeAssocMembers($request, $group->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $getGroups->handle(auth()->id())
        ]);
    }
}
