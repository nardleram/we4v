<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use App\Actions\Teams\StoreTeam;
use App\Actions\Groups\GetGroups;
use App\Actions\Memberships\StoreMemberships;
use App\Http\Requests\StoreTeamRequest;
use App\Actions\Teams\DestroyTeamCascade;

class TeamController extends Controller
{
    public function __construct(StoreTeam $storeTeam, GetGroups $getGroups, StoreMemberships $storeMembers)
    {
        $this->storeTeam = $storeTeam;
        $this->getGroups = $getGroups;
        $this->storeMemberships = $storeMembers;
    }

    public function store(StoreTeamRequest $request) : object
    {
        $team = $this->storeTeam->handle($request);
        
        $this->storeMemberships->handle($request, $team->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }

    public function update(StoreTeamRequest $request)
    {

    }

    public function destroy(Team $team, DestroyTeamCascade $destroyTeamCasc) : object
    {
        $destroyTeamCasc->handle($team->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $this->getGroups->handle(auth()->id())
        ]);
    }
}
