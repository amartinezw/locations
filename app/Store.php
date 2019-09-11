<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'number'];

    public static $rules = [
        'name' => 'required',        
    ];
}
