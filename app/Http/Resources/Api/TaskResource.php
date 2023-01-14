<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'created_at'  => $this->created_at->format('d-m-Y'),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'sub_tasks' => SubTaskResource::collection($this->whenLoaded('subTasks')),
        ];
    }
}
