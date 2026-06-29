<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquirySKUPO extends Model
{
    use HasFactory;

    protected $table = "inquiry_po_sku";

    static function getPOSKU($request){
        // $whereConditions=[
        //     ['inquiry_po.id','=',$request->po_id],
        // ];
        // $skus = InquiryPO::where($whereConditions)
        // ->join('inquiry_sku','inquiry_sku.inquiry_id','inquiry_po.inquiry_id')
        // ->leftjoin('color','inquiry_sku.color_id','color.id')
        // ->leftjoin('size','inquiry_sku.size_id','size.id')
        // ->select('inquiry_po.id as po_id','inquiry_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size')
        // ->get()->toArray();
       $whereConditions=[
            ['inquiry_po_sku.po_id','=',$request->po_id],
        ];
        $skus = InquirySKUPO::where($whereConditions)
        //->join('inquiry_po_sku','inquiry_po_sku.po_id','inquiry_po.id')
        ->leftjoin('color','inquiry_po_sku.color_id','color.id')
        ->leftjoin('size','inquiry_po_sku.size_id','size.id')
        ->select('inquiry_po_sku.id as po_id','inquiry_po_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size','size.category as category')
        ->get()->toArray();

        return $skus;
    }

    static function getPOSKUMultiple($request){
        // $whereConditions=[
        //     ['inquiry_po.id','=',$request->po_id],
        // ];
        // $skus = InquiryPO::where($whereConditions)
        // ->join('inquiry_sku','inquiry_sku.inquiry_id','inquiry_po.inquiry_id')
        // ->leftjoin('color','inquiry_sku.color_id','color.id')
        // ->leftjoin('size','inquiry_sku.size_id','size.id')
        // ->select('inquiry_po.id as po_id','inquiry_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size')
        // ->get()->toArray();
       $whereConditions=[
            ['inquiry_po_sku.parent_po_id','=',$request->po_parent_id],
        ];
        $skus = InquirySKUPO::where($whereConditions)
        //->join('inquiry_po_sku','inquiry_po_sku.po_id','inquiry_po.id')
        ->leftjoin('color','inquiry_po_sku.color_id','color.id')
        ->leftjoin('size','inquiry_po_sku.size_id','size.id')
        ->select('inquiry_po_sku.id as po_id','inquiry_po_sku.quantity','color.id as color_id','color.name as color','size.id as size_id','size.name as size','size.category as category','parent_po_id','po_id')
        ->get()->toArray();

        return $skus;
    }
}
