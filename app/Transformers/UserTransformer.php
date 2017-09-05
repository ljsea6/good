<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'name' => $user->name,
            'email' =>$user->email,
            'created_at' =>$user->created_at,
            'updated_at' =>$user->updated_at
        ];
    }
}