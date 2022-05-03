<?php

namespace App\Actions\Associations;

use App\Models\User;
use App\Jobs\ThrottleMail;
use App\Models\Association;
use Illuminate\Http\Request;
use App\Mail\AssociationRequestAcceptedNotification;
use App\Mail\AssociationRequestRejectedNotification;

class UpdateAssociation
{
    protected $association;

    public function __construct(Request $request)
    {
        $this->association = Association::where('id', $request->id)->first();
    }

    public function accept(Request $request) : void
    {
        Association::where('id', $request->id)
            ->update([
                'confirmed_at' => now(),
                'status' => 1
            ]);
        
        $requestee = User::where('id', $this->association->requested_of)->first();
        $requester = User::where('id', $this->association->requested_by)->first();
        
        ThrottleMail::dispatch(new AssociationRequestAcceptedNotification($requestee, $requester), $requester);
    }

    public function reject(Request $request) : void
    {
        Association::where('id', $request->id)->forceDelete();

        $requestee = User::where('id', $this->association->requested_of)->first();
        $requester = User::where('id', $this->association->requested_by)->first();
        
        ThrottleMail::dispatch(new AssociationRequestRejectedNotification($requestee, $requester), $requester);
    }
}