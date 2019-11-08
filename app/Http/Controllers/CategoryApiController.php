<?php

namespace App\Http\Controllers;
use App\Category;
use Dompdf\Exception;

class CategoryApiController extends Controller
{
    protected $mCategory;

    public function __construct()
    {
        $this->mCategory = new Category();
    }

    public function getParentCategories()
    {
        try {

            $categories = $this->mCategory->where('depends_id',0)->where('active',1)->get();
            return response()->json($categories)->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getChildCategories()
    {
        try {

            $categories = $this->mCategory->where('depends_id','>',0)->where('active',1)->get();
            return response()->json($categories)->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getCategoriesOfParent($parent_id)
    {
        try {

            $categories = $this->mCategory->where('depends_id','=',$parent_id)->where('active',1)->get();
            return response()->json($categories)->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}