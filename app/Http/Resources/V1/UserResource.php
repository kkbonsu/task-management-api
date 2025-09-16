<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'email_verified_at' => $this->when($request->routeIs('users.*'), $this->email_verified_at),
                'created_at' => $this->when($request->routeIs('users.*'), $this->created_at),
                'updated_at' => $this->when($request->routeIs('users.*'), $this->updated_at),
            ],
            'includes' => TaskResource::collection($this->whenLoaded('tasks')),
            'links' => [
                'self' => route('users.show', ['user' => $this->id]),
            ]
        ];
    }
}
