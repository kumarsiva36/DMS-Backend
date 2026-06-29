<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class FabricSupplierResponse extends Model
{
    use HasFactory;

    protected $table = 'fabric_supplier_response';

    public static function FactoryResponse($request){
        $whereConditions=[
            ['fabric_supplier_response.inquiry_id','=',$request->inquiry_id]
        ];
       $res = FabricSupplierResponse::where($whereConditions)
              ->join('fabric_contact','fabric_contact.id','fabric_supplier_response.supplier_id')
              ->select('fabric_supplier_response.price','fabric_supplier_response.comments','supplier','contact_person','contact_number','contact_email','fabric_supplier_response.inquiry_id')
              ->orderBy('fabric_supplier_response.created_at','desc')
              ->get();
        return $res;
    }

    public static function get_factory_list_response($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id]
        ];

        $res = FabricInquiry::where($whereConditions)->pluck('supplier_ids')->first();

        $fact_ids = array_unique(explode('||',$res));

        $factories = FabricContact::whereIn('fabric_contact.id',$fact_ids)
                    ->whereNotIn('fabric_contact.id',function($query) use($request)
                    {
                        $query->select('fabric_supplier_response.supplier_id')
                            ->from('fabric_supplier_response')
                            ->whereRaw('inquiry_id = '.$request->inquiry_id);
                    })
                    ->select('fabric_contact.id','supplier')->get();
        return $factories;
    }

    public static function download_supplier_response($whereConditions){
        $res = FabricSupplierResponse::where($whereConditions)
              ->join('fabric_contact','fabric_contact.id','fabric_supplier_response.supplier_id')
              ->join('fabric_inquiry','fabric_inquiry.id','fabric_supplier_response.inquiry_id')
              ->select('fabric_supplier_response.price','fabric_supplier_response.comments',
              'supplier','contact_person','contact_number','contact_email','fabric_supplier_response.inquiry_id','fabric_inquiry.currency')
              ->orderBy('fabric_supplier_response.created_at','desc')
              ->get();
        return $res;
    }

}
