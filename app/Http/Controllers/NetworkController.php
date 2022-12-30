<?php

namespace App\Http\Controllers;

use App\Actions\Networks\TransferNetworkOwnership;
use App\Http\Requests\NetworkRequest;
use App\Actions\Networks\StoreNetwork;
use App\Actions\Networks\UpdateNetwork;
use App\Actions\Memberships\UpdateNetworkMemberships;
use App\Http\Requests\TransferNetworkOwnershipRequest;

class NetworkController extends Controller
{
    public function __construct(
        private StoreNetwork $storeNetwork,
        private UpdateNetwork $updateNetwork,
        private UpdateNetworkMemberships $updateNetworkMemberships,
        private TransferNetworkOwnership $transferNetworkOwnership
    ) {}

    public function store(NetworkRequest $request)
    {
        $this->storeNetwork->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Network created']
        ]);
    }

    public function update(NetworkRequest $request) 
    {
        $this->updateNetwork->handle($request);

        $this->updateNetworkMemberships->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Network updated']
        ]);
    }

    public function transferOwnership (TransferNetworkOwnershipRequest $request)
    {
        $this->transferNetworkOwnership->handle($request);

        return redirect()->back()->with([
            'flash' => ['message' => 'Network ownership transfered']
        ]);
    }
}
