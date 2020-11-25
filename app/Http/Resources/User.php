<?php

namespace App\Http\Resources;

use App\Http\Resources\UserImage as UserImageResource;
use App\Associate;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Associate as AssociateResource;

class User extends JsonResource
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
                'type' => 'users',
                'user_id' => $this->id,
                'attributes' => [
                    'fname' => $this->fname,
                    'sname' => $this->sname,
                    'uname' => $this->uname,
                    'association' => new AssociateResource(Associate::association($this->id)),
                    'cover_image' => new UserImageResource($this->coverImage),
                    'profile_image' => new UserImageResource($this->profileImage),
                ]
            ],
            'links' => [
                'self' => url('/users/'.$this->id),
            ]
        ];
    }
}
