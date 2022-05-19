<?php

namespace App\Actions\Memberships;

use App\Models\Task;
use App\Models\User;
use App\Jobs\ThrottleMail;
use App\Models\Membership;
use App\Mail\TeamTaskAssigned;
use App\Mail\UsersTaskAssigned;
use App\Mail\TeamMembershipRequested;
use App\Mail\GroupMembershipRequested;

class StoreMemberships
{
    public function handle($request, $parentId)
    {
        foreach ($request->members as $member) {
            $membership = Membership::create([
                'membershipable_id' => $parentId,
                'membershipable_type' => $request->membershipable_type,
                'user_id' => $member['user_id'],
                'role' => $member['role'],
                'is_admin' => $member['is_admin'],
                'updated_by' => auth()->id()
            ]);

            $user = User::where('id', auth()->id())->first();
        
            if ($request->membershipable_type === 'App\\Models\\Group') {
                ThrottleMail::dispatch(new GroupMembershipRequested($membership, $user), $membership->member);
            }

            if ($request->membershipable_type === 'App\\Models\\Team') {
                ThrottleMail::dispatch(new TeamMembershipRequested($membership, $user), $membership->member);
            }

            if ($request->membershipable_type === 'App\\Models\\Task') {
                $task = $membership->tasks->first();

                // Email assigned members
                if (!$membership->user_id) { // Assigned to whole team; get all team members
                    // Get team members, tho not admin
                    $members = Membership::where('membershipable_id', $task->id)
                        ->where('user_id', '!=', auth()->id()) // If admin created task, needs no notification
                        ->get();
                    
                    foreach ($members as $member) {
                        ThrottleMail::dispatch(new TeamTaskAssigned($member, $user, $task), $member->member);
                    }
                }

                if ($membership->user_id) { // Assigned to individual user(s)
                    ThrottleMail::dispatch(new UsersTaskAssigned($membership, $user, $task), $membership->member);
                }
            }
        }
    }
}