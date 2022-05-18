<?php

namespace App\Actions\Notes;

use App\Models\Note;
use App\Models\Team;
use App\Jobs\ThrottleMail;
use App\Models\Membership;
use App\Mail\TaskNoteLogged;
use App\Mail\ProjectNoteLogged;
use Illuminate\Support\Collection;

class StoreNote
{
    public function handle($note) : void
    {
        $type = 'team';
        $teamMemberships = new Collection();
        $uniqueSet = [];

        $note = Note::create([
            'body' => $note['body'],
            'noteable_id' => $note['noteable_id'],
            'noteable_type' => $note['noteable_type'],
            'user_id' => auth()->id()
        ]);

        /*
        * Email notifications
        */

        if ($note->noteable_type === 'App\\Models\\Project') {
            $initialMemberships = $note->noteable->projectable->memberships;

            if ($note->noteable->projectable_type === 'App\\Models\\Group') {
                $type = 'group';
                $teams = Team::where('group_id', $note->noteable->projectable->id)->get();
    
                foreach ($teams as $team) {
                    $teamMemberships = $teamMemberships->merge($team->memberships);
                }
    
                $memberships = $teamMemberships->merge($initialMemberships);
            } else {
                $memberships = $initialMemberships;
            }

            foreach ($memberships as $membership) {
                if (!in_array($membership->member->email, $uniqueSet)) {
                    array_push($uniqueSet, $membership->member->email);

                    if ($membership->member->user_id !== auth()->id()) {
                        ThrottleMail::dispatch(new ProjectNoteLogged($membership, $note->user, $note, $type), $membership->member);
                    }
                }
            }
        } 
        
        if ($note->noteable_type === 'App\\Models\\Task') {
            $note->noteable->taskable_type === 'App\\Models\\Group'
            ? $type = 'group'
            : null;

            // Get task memberships
            $memberships = Membership::where('membershipable_id', $note->noteable->id)->get();

            foreach($memberships as $membership) {
                if (!$membership->user_id) { // Assigned to whole team
                    // Get all team memberships
                    $members = Membership::where('membershipable_id', $note->noteable->taskable_id)
                        ->where('user_id', '!=', auth()->id()) // Note author needs no notification
                        ->get();

                    // Dispatch emails
                    foreach ($members as $member) {
                        ThrottleMail::dispatch(new TaskNoteLogged($member, $note->user, $note, $type, false), $member->member);
                    }

                    if ($note->noteable->owner !== auth()->id()) {
                        // Email team owner as implicit member of team.
                        ThrottleMail::dispatch(new TaskNoteLogged(Membership::where('user_id', $note->noteable->owner)->first(), $note->user, $note, $type), $note->noteable->user);
                    }
                } 
                
                if ($membership->user_id) { // Assigned to individual user(s), whether in group or team
                    foreach ($note->noteable->taskable->memberships as $adminMembership) {
                        $adminMembership->is_admin ?
                        $adminId = $adminMembership->user_id
                        : null;
                    }
                    // Dispatch emails to members, tho not to note author
                    if ($membership->user_id !== auth()->id()) {
                        ThrottleMail::dispatch(new TaskNoteLogged($membership, $note->user, $note, $type, false), $membership->member);
                    }

                    // Email owner as implicit member of team or group, if not note author and not admin of group or team
                    if ($note->noteable->owner !== auth()->id() && $note->noteable->owner !== $adminId) {
                        ThrottleMail::dispatch(new TaskNoteLogged(Membership::where('user_id', $note->noteable->owner)->first(), $note->user, $note, $type), $note->noteable->user);
                    }
                }
            }
        }
    }
}