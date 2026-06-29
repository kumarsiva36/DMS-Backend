<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class POTesting extends Model
{
    use HasFactory;

    protected $table = "inquiry_new_po_testing";

    public static function getPOTesting($request){
        $whereConditions=[
            ['inquiry_new_po_testing.po_id','=',$request->po_id],
        ];
        $skus = POTesting::where($whereConditions)
        ->leftjoin('color','inquiry_new_po_testing.color_id','color.id')
        ->leftjoin('size','inquiry_new_po_testing.size_id','size.id')
        ->select('inquiry_new_po_testing.po_id as po_id','inquiry_new_po_testing.length_qty','color.id as color_id','color.name as color','size.id as size_id','size.name as size','inquiry_new_po_testing.type as type')
        ->get()->toArray();

        return $skus;
    }
    public static function getPOTestingSKU($request){
        $whereConditions=[
            ['inquiry_new_po_testing.po_id','=',$request->po_id],
        ];
        $skus = POTesting::where($whereConditions)
        ->leftjoin('color','inquiry_new_po_testing.color_id','color.id')
        ->leftjoin('size','inquiry_new_po_testing.size_id','size.id')
        ->select('inquiry_new_po_testing.po_id as po_id','inquiry_new_po_testing.type as type',DB::raw('GROUP_CONCAT( DISTINCT color.name ) as colors'),
        DB::raw('GROUP_CONCAT( DISTINCT color.id ) as color_id'),DB::raw('GROUP_CONCAT( DISTINCT size.name ) as sizes'),DB::raw('GROUP_CONCAT( DISTINCT size.id ) as size_id'))
        ->groupBy('inquiry_new_po_testing.type')
        ->get()->toArray();

        return $skus;
    }
    public static function getPOTestingMulti($request){
        $whereConditions=[
            ['inquiry_new_po_testing.po_parent_id','=',$request->po_parent_id],
        ];
        $skus = POTesting::where($whereConditions)
        ->leftjoin('color','inquiry_new_po_testing.color_id','color.id')
        ->leftjoin('size','inquiry_new_po_testing.size_id','size.id')
        ->select('inquiry_new_po_testing.po_id as po_id','inquiry_new_po_testing.length_qty','color.id as color_id','color.name as color','size.id as size_id','size.name as size','inquiry_new_po_testing.type as type')
        ->get()->toArray();

        return $skus;
    }
    public static function getPOTestingSKUMulti($request){

        $po_ids = InquiryPOrder::where('parent_id','=',$request->po_parent_id)->select('id')->get();
        $skus=[];
        foreach($po_ids as $p){
            $whereConditions=[
                ['inquiry_new_po_testing.po_id','=',$p->id],
            ];
            $skus[$p->id] = POTesting::where($whereConditions)
            ->leftjoin('color','inquiry_new_po_testing.color_id','color.id')
            ->leftjoin('size','inquiry_new_po_testing.size_id','size.id')
            ->select('inquiry_new_po_testing.po_id as po_id','inquiry_new_po_testing.type as type',DB::raw('GROUP_CONCAT( DISTINCT color.name ) as colors'),
            DB::raw('GROUP_CONCAT( DISTINCT color.id ) as color_id'),DB::raw('GROUP_CONCAT( DISTINCT size.name ) as sizes'),DB::raw('GROUP_CONCAT( DISTINCT size.id ) as size_id'))
            ->groupBy('inquiry_new_po_testing.type')
            //->groupBy('inquiry_new_po_testing.po_id')
            ->get()->toArray();
        }
        return $skus;
    }
}
