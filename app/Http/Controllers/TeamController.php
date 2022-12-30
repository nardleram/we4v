<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use App\Actions\Teams\StoreTeam;
use App\Actions\Groups\GetGroups;
use App\Actions\Teams\UpdateTeam;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Actions\Teams\DestroyTeamCascade;
use App\Actions\Memberships\StoreMemberships;
use App\Actions\Memberships\UpdateMemberships;

class TeamController extends Controller
{
    public function __construct(
        private StoreTeam $storeTeam,
        private GetGroups $getGroups,
        private StoreMemberships $storeMemberships,
        private UpdateTeam $updateTeam,
        private UpdateMemberships $updateMemberships
    ) {}

    public function store(StoreTeamRequest $request) : object
    {
        $team = $this->storeTeam->handle($request);
        
        $this->storeMemberships->handle($request, $team->id);

        return redirect()->back()->with([
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Team saved, invites sent.']
        ]);
    }

    public function update(UpdateTeamRequest $request) : object
    {
        $this->updateTeam->handle($request);

        $this->updateMemberships->handle($request);

        return redirect()->back()->with([
            'mygroups' => $this->getGroups->handle(auth()->id()),
            'flash' => ['message' => 'Team updated']
        ]);
    }

    public function destroy(Team $team, DestroyTeamCascade $destroyTeamCasc) : object
    {
        $destroyTeamCasc->handle($team->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
