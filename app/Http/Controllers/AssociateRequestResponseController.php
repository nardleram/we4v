<?php

namespace App\Http\Controllers;

use App\Actions\Associations\UpdateAssociation;
use App\Http\Requests\AssociateRequestResponseRequest;

class AssociateRequestResponseController extends Controller
{
    public function __construct(private UpdateAssociation $updateAssociation)
    {}
    
    public function update(AssociateRequestResponseRequest $request)
    {
        if ($request->status) {
            $this->updateAssociation->accept($request);
            $message = 'Association accepted';
        } else {
            $this->updateAssociation->reject($request);
            $message = 'Association rejected';
        }

        return redirect()->back()->with([
            'flash' => ['message' => $message]]);

    }
}
