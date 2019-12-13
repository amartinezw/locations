<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function images()
    {
        return $this->hasMany('App\Image')->orderBy('order');
    }

    public function locations()
    {
        return $this->hasMany('App\LocationVariation')->orderBy('created_at');
    }

    public function variations()
    {
        return $this->hasMany('App\Variation')->orderBy('id');
    }

    public function categoryProduct()
    {
        return $this->hasMany('App\CategoryProduct');
    }

    public function firstimg()
    {
        return $this->images()->take(1);
    }
    public function parentCategory()
    {
        return $this->hasMany('App\CategoryProduct')->with('category')->with('category.parent');
    }
}
