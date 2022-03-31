<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Exceptions\GroupNotFoundException;
use App\Http\Requests\AffiliateRequestRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AffiliateController extends Controller
{
    public function store(AffiliateRequestRequest $request)
    {
        try {
            Group::findOrFail($request->requested_of)
                ->affiliations()
                ->attach($request->requested_by, [
                    'created_at' => now(),
                    'updated_at' => now(),
                    'network_id' => $request->network_id
                ]);
        } catch (ModelNotFoundException $e) {
            throw new GroupNotFoundException();
        }

        return redirect()->back();
    }
}
