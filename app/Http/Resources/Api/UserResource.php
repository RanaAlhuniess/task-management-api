<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $withToken = false;
    public function withToken(bool $value){
        $this->withToken = $value;
        return $this;
    }
     
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            $this->mergeWhen($this->withToken, [
                'token' => $this->createToken('accessToken')->accessToken,
            ]),
        ];
    }
}
