<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorMetaData extends Model
{
    protected $fillable = [
	    'reviewed_at',
	    'reviewed_by',
	    'color_id',
	    'language',
	    'name'
    ];
}
