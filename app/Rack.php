<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    protected $fillable = ['id', 'name'];

    public static $rules = [
        'id' => 'required',
        'name' => 'required',
        'warehouse_id' => 'required',
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse')->withDefault();
    }

    public function warehouselocations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }

    public function items()
    {
        return $this->hasManyThrough(
            LocationVariation::class,
            WarehouseLocation::class,
            'rack_id',
            'warehouselocation_id'
        );
    }

    public function products()
    {
        return $this->items()->groupBy('product_id');
    }
}
