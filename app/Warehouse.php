<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'store_id'];

    public static $rules = [
        'name' => 'required',
        'store_id' => 'required',        
    ];

    public function store()
    {
        return $this->belongsTo('App\Store')->withDefault();
    }

    public function warehouselocations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}
