<?php

namespace app\Http\Resources;

class UserResource
{
    public static function make($resource): array
    {
        return [
            'id' => $resource->id,
            'email' => $resource->email,
            'username' => $resource->username,
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
        ];
    }
}
