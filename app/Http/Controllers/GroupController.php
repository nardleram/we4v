<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Group;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Http\Requests\StoreGroupRequest;
use App\Actions\Groups\DestroyGroupCascade;
use App\Actions\Memberships\StoreMemberships;

class GroupController extends Controller
{
    public function __construct(StoreGroup $storeGroup, GetGroups $getGroups, StoreMemberships $storeMembers, DestroyGroupCascade $destroyGroupCasc)
    {
        $this->storeGroup = $storeGroup;
        $this->getGroups = $getGroups;
        $this->storeMembers = $storeMembers;
        $this->destroyGroupCasc = $destroyGroupCasc;
    }

    public function index() : object
    {
        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
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

    public function update(StoreGroupRequest $request)
    {

    }

    public function destroy(Group $group) : object
    {
        $this->destroyGroupCasc->handle($group->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
