<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $fillable = [
        'name',
        'active',
        'order',
        'depends_id',
        'promotion_id',
        'keywords',
        'description',
        'meta_product_description'
    ];

    public static $rules = [
        'name'   => 'required',
        'active' => 'required|boolean',
    ];

    public static $updateRules = [
        'name'   => 'required',
        'active' => 'required|boolean',
    ];

    public function children()
    {
        return $this->hasMany(Category::class, 'depends_id', 'id')->where('active', 1)->orderBy('depends_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->hasOne(Category::class, 'id', 'depends_id');
    }

}