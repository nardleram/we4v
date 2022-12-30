<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Group;
use App\Actions\Groups\GetGroups;
use App\Actions\Groups\StoreGroup;
use App\Actions\Groups\UpdateGroup;
use App\Actions\Groups\SearchGroups;
use App\Actions\Teams\GetAdminTeams;
use App\Actions\Groups\GetAdminGroups;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\SearchGroupRequest;
use App\Actions\Groups\DestroyGroupCascade;
use App\Actions\Groups\GetNetworkGroupMember;
use App\Actions\Memberships\StoreMemberships;
use App\Actions\Groups\TransferGroupOwnership;
use App\Actions\Memberships\UpdateMemberships;
use App\Http\Requests\TransferGroupOwnershipRequest;

class GroupController extends Controller
{
    public function __construct(
        private GetGroups $getGroups,
        private GetNetworkGroupMember $getNetworkGroupMember,
        private GetAdminGroups $getAdminGroups,
        private GetAdminTeams $getAdminTeams,
        private StoreGroup $storeGroup,
        private StoreMemberships $storeMemberships,
        private UpdateGroup $updateGroup,
        private UpdateMemberships $updateMemberships,
        private DestroyGroupCascade $destroyGroupCasc,
        private SearchGroups $searchGroups,
        private TransferGroupOwnership $transferGroupOwnership
    ) {}

    public function index() : object
    {
        return Inertia::render('MyGroups', [
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id())
        ]);
    }

    public function show($id) : array
    {
        return $this->getNetworkGroupMember->handle($id);
    }

    public function search(SearchGroupRequest $string)
    {
        session(['searchData' => null]);

        return redirect()->back()->with([
            'searchResults' => [session(['searchData' => $this->searchGroups->handle($string->searchString)])]
        ]);
    }

    public function store(StoreGroupRequest $request) : object
    {
        $group = $this->storeGroup->handle($request);

        $this->storeMemberships->handle($request, $group->id);

        count($request->members) > 0
        ? $flashMessage = 'Group added, invitations sent to selected associates'
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
            'flash' => ['message' => 'Group updated']
        ]);
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

    public function transferOwnership (TransferGroupOwnershipRequest $request)
    {
        $this->transferGroupOwnership->handle($request);

        return redirect()->back()->with([
            'myGroups' => $this->getGroups->handle(auth()->id()),
            'myAdminGroups' => $this->getAdminGroups->handle(auth()->id()),
            'myAdminTeams' => $this->getAdminTeams->handle(auth()->id()),
            'flash' => ['message' => 'Group ownership transfered']
        ]);
    }
}
