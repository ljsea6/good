<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Tercero;

class TerceroTransformer extends TransformerAbstract
{
    public function transform(Tercero $user)
    {
        return [
            'email' =>$user->email
        ];
    }
}