<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    public function product()
    {
        return $this->belongsTo('App\Product')->withDefault();
    }

    public function color()
    {
        return $this->belongsTo('App\Color')->withDefault();
    }

    public function locations()
    {
        return $this->hasMany('App\LocationVariation');
    }
}
