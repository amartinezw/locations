<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationVariation extends Model
{
    protected $fillable = [
	    'warehouselocation_id',
	    'variation_id',
        'product_id',
        'user_id'	    
    ];
    public function warehouselocation()
    {
        return $this->belongsTo('App\WarehouseLocation')->withDefault();
    }

    public function variation()
    {
        return $this->belongsTo('App\Variation')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo('App\Product')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }


    
}
