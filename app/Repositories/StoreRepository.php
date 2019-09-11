<?php

namespace App\Repositories;

use App\Store;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoreRepository extends BaseRepository
{
    protected $model = 'App\Store';

    public function getAll(Request $request)
    {
        $query = Store::orderBy($request->column, $request->order);
        $stores = $query->paginate($request->per_page ?? 10);
        return $stores;
    }
    
}