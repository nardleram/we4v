<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function store(Request $request)
    {
        $approval = Approval::where('approvalable_id', $request->id)
            ->where('user_id', auth()->id())
            ->first();
        
        isset($approval) 
            ? $approval->delete()
            : Approval::create([
                'approvalable_id' => $request->id,
                'approvalable_type' => $request->model,
                'user_id' => auth()->id()
            ]);

        return redirect()->back();
    }
}
