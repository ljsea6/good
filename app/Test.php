<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'tests';

    protected $guarded = [];

    public function tests()
    {
        return $this->hasMany(Test::class, 'test_id', 'id');
    }

}
