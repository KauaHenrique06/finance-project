<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Permission\PermissionResource;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'profile_pic' => $this->profile_pic,
            'address' => new AddressResource($this->whenLoaded('address')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
