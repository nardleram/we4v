<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Http\Requests\StoreGroupRequest;
use App\Actions\Memberships\StoreMemberships;

class GroupController extends Controller
{
    public function __construct(StoreGroup $storeGroup, GetGroups $getGroups, StoreMemberships $storeMembers)
    {
        $this->storeGroup = $storeGroup;
        $this->getGroups = $getGroups;
        $this->storeMembers = $storeMembers;
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

        $this->storeMembers->storeMembers($request, $group->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
