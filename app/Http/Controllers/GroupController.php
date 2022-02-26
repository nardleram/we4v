<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Group;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Actions\Groups\UpdateGroup;
use App\Actions\Teams\GetAdminTeams;
use App\Actions\Groups\GetAdminGroups;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\SearchGroupRequest;
use App\Actions\Groups\DestroyGroupCascade;
use App\Actions\Groups\SearchGroups;
use App\Actions\Memberships\StoreMemberships;
use App\Actions\Memberships\UpdateMemberships;

class GroupController extends Controller
{
    private $getGroups;
    private $getAdminGroups;
    private $getAdminTeams;
    private $storeGroup;
    private $storeMemberships;
    private $updateGroup;
    private $updateMemberships;
    private $destroyGroupCasc;
    private $searchGroups;

    public function __construct(
        GetGroups $getGroups,
        GetAdminGroups $getAdminGroups,
        GetAdminTeams $getAdminTeams,
        StoreGroup $storeGroup,
        StoreMemberships $storeMemberships,
        UpdateGroup $updateGroup,
        UpdateMemberships $updateMemberships,
        DestroyGroupCascade $destroyGroupCasc,
        SearchGroups $searchGroups
    )
    {
        $this->getGroups = $getGroups;
        $this->getAdminGroups = $getAdminGroups;
        $this->getAdminTeams = $getAdminTeams;
        $this->storeGroup = $storeGroup;
        $this->storeMemberships = $storeMemberships;
        $this->updateGroup = $updateGroup;
        $this->updateMemberships = $updateMemberships;
        $this->destroyGroupCasc = $destroyGroupCasc;
        $this->searchGroups = $searchGroups;
    }

    public function index() : object
    {
        return Inertia::render('MyGroups', [
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id())
        ]);
    }

    public function store(StoreGroupRequest $request) : object
    {
        $group = $this->storeGroup->handle($request);

        $this->storeMemberships->handle($request, $group->id);

        count($request->members) > 0
        ? $flashMessage = 'Group added, invitations sent'
        : $flashMessage = 'Group added';

        return redirect()->back()->with([
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => $flashMessage]
        ]);
    }

    public function update(StoreGroupRequest $request) : object
    {
        $this->updateGroup->handle($request);

        $this->updateMemberships->handle($request);

        return redirect()->back()->with([
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Group updated']]);
    }

    public function destroy(Group $group) : object
    {
        $this->destroyGroupCasc->handle($group->id);

        return Inertia::render('MyGroups', [
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Group deleted'] 
        ]);
    }

    public function search(SearchGroupRequest $string)
    {
        session(['searchData' => null]);

        return redirect()->back()->with([
            'searchResults' => [session(['searchData' => $this->searchGroups->handle($string->searchString)])]
        ]);
    }
}
