<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use App\Actions\Teams\GetTeams;
use App\Actions\Teams\StoreTeam;
use App\Actions\Groups\GetGroups;
use App\Http\Requests\StoreTeamRequest;
use App\Actions\Teams\DestroyTeamCascade;

class TeamController extends Controller
{
    public function index(GetTeams $action) : object
    {
        return Inertia::render('MyTeams', [
            'myteams' => $action->handle(auth()->id())
            ]
        );
    }

    public function store(StoreTeamRequest $request, StoreTeam $action, GetGroups $getGroups) : object
    {
        $team = $action->storeTeam($request);
        
        $action->storeMembers($request, $team->id);

        return Inertia::render('MyGroups', [
            'mygroups' => $getGroups->handle(auth()->id())
        ]);
    }

    public function update(StoreTeamRequest $request)
    {

    }

    public function destroy(Team $team, DestroyTeamCascade $destroyTeamCasc, GetTeams $action) : object
    {
        $destroyTeamCasc->handle($team->id);

        return Inertia::render('MyTeams', [
            'myteams' => $action->handle(auth()->id())
            ]
        );
    }
}
