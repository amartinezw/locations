<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    protected $table = 'category_product';

    public $fillable = [
        'category_id',
        'product_id',
        'order',
    ];

    public static $rules = [
        'category_id' => 'required|numeric',
        'product_id' => 'required|numeric',
        'order' => 'required|numeric',
    ];

    public static $updateRules = [
        'category_id' => 'required|numeric',
        'product_id' => 'required|numeric',
        'order' => 'required|numeric',
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}