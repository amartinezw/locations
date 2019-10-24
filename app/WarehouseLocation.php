<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WarehouseLocation
 * @package App
 * @property int $warehouse_id
 * @property int $rack
 * @property int $block
 * @property int $level
 * @property boolean $active
 */

class WarehouseLocation extends Model
{
    protected $fillable = ['warehouse_id', 'rack', 'block', 'level','active'];

    public static $rules = [
        'warehouse_id' 	=> 'required',
        'blocks'    	=> 'required|integer|between:1,50',
        'levels'    	=> 'required|integer|between:1,50',
        'sides'    		=> 'required|integer|between:1,2',
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }

    public function scopeRack($query, $rack)
    {
        return $query->where('rack', '=', $rack);
    }

    public function scopeWarehouse($query, $warehouse)
    {
        return $query->where('warehouse_id', '=', $warehouse);
    }

    public function items()
    {
        return $this->hasMany('App\LocationVariation', 'warehouselocation_id');
    }
}
