<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Http\Requests\StoreGroupRequest;

class GroupController extends Controller
{
    public function __construct(StoreGroup $storeGroup, GetGroups $getGroups)
    {
        $this->storeGroup = $storeGroup;
        $this->getGroups = $getGroups;
    }

    public function index(GetGroups $action) : object
    {
        return Inertia::render('MyGroups', [
            'mygroups' => $action->handle(auth()->id())
        ]);
    }

    public function store(StoreGroupRequest $request) : object
    {
        $group = $this->storeGroup->storeGroup($request);

        $this->storeGroup->storeAssocMembers($request, $group->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
