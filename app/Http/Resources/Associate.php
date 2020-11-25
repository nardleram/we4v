<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Associate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'associate-request',
                'associate_request_id' => $this->id,
                'attributes' => [
                    'confirmed_at' => optional($this->confirmed_at)->diffForHumans(),
                    'status' => $this->status,
                    'associate_id' => $this->associate_id,
                    'user_id' => $this->user_id,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$this->associate_id),
            ]
        ];
    }
}
