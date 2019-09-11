<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    public function product()
    {
        return $this->belongsTo('App\Product')->withDefault();
    }
}
