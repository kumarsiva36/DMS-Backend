<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Size extends Model
{
    use HasFactory;
    protected $table = 'size';
    protected $fillable = [
        'name','company_id','workspace_id','user_id','staff_id','is_default','status','created_by','category'
    ];

    public static function getSizes($request){
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $status = '3';
        if(isset($request->category)){
            $whereCondition=[
                ['company_id','=',$companyId],
                ['workspace_id','=',$workspaceId],
                ['user_id','=',$userId],
                ['status','!=',$status],
                ['category','=',$request->category]
            ];
        }else{
            $whereCondition=[
                ['company_id','=',$companyId],
                ['workspace_id','=',$workspaceId],
                ['user_id','=',$userId],
                ['status','!=',$status]
            ];
        }

        $getSize = Size::where($whereCondition)->orWhere('is_default', '=', '0')->get();
        return $getSize;
    }

    // public static function getSizesIfExists($whereCondition){
    //     $existsSize = Size::where($whereCondition)->first();
    //     return $existsSize;
    // }

    public static function getSizesIfExists($whereCondition,$orwhereCondition=[],$name='',$category=''){
        //$existsSize = Size::where($whereCondition)->orwhere($orwhereCondition)->first();
        $existsSize = Size::where($whereCondition)
        ->orWhere(function ($query) use($name,$category) {
            $query->where("name",$name)->where('category',$category)
                ->where('is_default','0');
        })->first();
        return $existsSize;
    }

    public static function deleteSize($id){
        $existsSize = Size::select('name')->where('id',$id)->first();
        return $existsSize;
    }

    /*Get Size Name Using ID*/
    public static function getSizeNameUsingId($id){
        $whereCondition=[
            ['id','=',$id],
                 ];
        $size = Size::where($whereCondition)->first();

       return $size;
    }

    /*Get Size categories*/
    public static function getSizeCategories($request){
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $status = '3';
        $whereCondition=[
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['status','!=',$status]
        ];
        $getSize = Size::where($whereCondition)->orWhere('is_default', '=', '0')
        ->select(DB::RAW('DISTINCT(category) as category'))
        ->orderByRaw('FIELD(category, "Infant","Toddler","Children","Youth","Men","Women")ASC')
        ->get();
        return $getSize;
    }

    public static function getCategoryIfExists($request){
        $category = ucfirst(trim($request->category));
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $whereCondition=[
            ['category','=',$category],
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId]
        ];
        // $or_whereCondition=[
        //     ['category','=',$category],
        //     ['is_default','=','0']
        // ];
        //$existsSize = Size::where($whereCondition)->orWhere($or_whereCondition)->first();
        $existsSize = Size::where($whereCondition)->orWhere(function($query) use ($category) {
            $query->where('category', $category)
                  ->where('is_default', 0);
        })->first();
        return $existsSize;
    }
    /*Add Sizes & category*/
    public static function addSizeandCategory($request){
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $status = '3';
        $whereCondition=[
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['status','!=',$status]
        ];
        $getSize = Size::where($whereCondition)->orWhere('is_default', '=', '0')
        ->select(DB::RAW('DISTINCT(category) as category'))
        ->orderByRaw('FIELD(category, "Infant","Toddler","Children","Youth","Men","Women")ASC')
        ->get();
        return $getSize;
    }
}
