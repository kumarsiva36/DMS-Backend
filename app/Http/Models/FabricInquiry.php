<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Models\FabricContact;

class FabricInquiry extends Model
{
    use HasFactory;

    protected $table = 'fabric_inquiry';

    public static function insert_fabric_inquiry($request){
        $data = [];

        $data['reference_id']= $request->reference_id ?? '';
        $data['company_id']= $request->company_id ?? 0;
        $data['workspace_id']= $request->workspace_id ?? 0;
        $data['user_id']= $request->user_id ?? 0;
        $data['staff_id']= $request->staff_id ?? 0;
        $data['yarn_count']= $request->yarn_count ?? '';
        $data['yarn_quantity']= $request->yarn_quantity ?? '';
        $data['yarn_quality']= $request->yarn_quality ?? '';
        $data['meterial']= $request->meterial ?? '';
        $data['composition']= $request->composition ?? '';
        $data['reference_inquiry']= $request->reference_inquiry ?? 0;
        $data['currency']= $request->currency ?? '$';
        $data['delivery_date']= $request->delivery_date ?? '';
        $data['inhouse_date']= $request->inhouse_date ?? '';
        $data['created_at']= date('Y-m-d H:i:s');
        return FabricInquiry::insert($data);
    }

    public static function get_inquirys($request){
        $whereConditions=[
            ['fabric_inquiry.company_id','=',$request->company_id],
            ['fabric_inquiry.workspace_id','=',$request->workspace_id]
        ];

        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['fabric_inquiry.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry.created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['fabric_inquiry.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry.created_at','<=',$to];
        }

        $inquiries = FabricInquiry::where($whereConditions)
        ->select('fabric_inquiry.id','fabric_inquiry.yarn_count','fabric_inquiry.yarn_quantity','fabric_inquiry.meterial',
        'fabric_inquiry.composition','fabric_inquiry.delivery_date','fabric_inquiry.supplier_ids',
        DB::raw('DATE_FORMAT(fabric_inquiry.created_at,"%Y-%m-%d") as created_date'))
        ->orderBy('fabric_inquiry.created_at','desc')
        ->paginate(20, ['*'], 'page', $request->page);
        //->get();

        return $inquiries;
    }
    public static function inquiry_details($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id],
        ];
        $inquiries = FabricInquiry::where($whereConditions)
        ->select('fabric_inquiry.*',DB::raw('DATE_FORMAT(fabric_inquiry.created_at,"%Y-%m-%d") as created_date'))
        ->get();

        return $inquiries;
    }

    public static function update_fabric_inquiry($request){
        $data = [];
        $data['yarn_count']= $request->yarn_count ?? '';
        $data['yarn_quantity']= $request->yarn_quantity ?? '';
        $data['yarn_quality']= $request->yarn_quality ?? '';
        $data['meterial']= $request->meterial ?? '';
        $data['composition']= $request->composition ?? '';
        $data['reference_inquiry']= $request->reference_inquiry ?? 0;
        $data['delivery_date']= $request->delivery_date ?? '';
        $data['inhouse_date']= $request->inhouse_date ?? '';
        $data['updated_user_id']= $request->user_id ?? 0;
        $data['updated_staff_id']= $request->staff_id ?? 0;
        $data['currency']= $request->currency ?? '$';
        return FabricInquiry::where('id',$request->inquiry_id)->update($data);
    }

    public static function getSuppliers($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id],

        ];
        $inquiries = FabricInquiry::where($whereConditions)->select('supplier_ids')->get();

        if(!empty($inquiries)){
            $ids = array_unique(explode('||',$inquiries[0]['supplier_ids']));
            $suppliers =FabricContact::whereIn('id',$ids)->select('id','supplier','contact_person','contact_number','contact_email')->get();
            return $suppliers;
        }
        return array();
    }
    public static function getFactories($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id],

        ];
        $inquiries = FabricInquiry::where($whereConditions)->select('supplier_ids')->get();

        if(!empty($inquiries)){
            $ids = array_unique(explode('||',$inquiries[0]['supplier_ids']));
            $factories =FabricContact::whereIn('id',$ids)->select('id','supplier','contact_person','contact_number','contact_email')->get();
            return $factories;
        }
        return array();
    }
    public static function deleteInquiry($request){
        $whereConditions=[
            ['id','=',$request->inquiry_id]
        ];
        FabricInquiry::where($whereConditions)->delete();

        $whereConditions1=[
            ['inquiry_id','=',$request->inquiry_id]
        ];
        FabricSupplierResponse::where($whereConditions1)->delete();
        $path = public_path() . '/Fabric/' .$request->inquiry_id.'.pdf';
        if(file_exists($path))
            unlink($path);
        return true;
    }
    public static function get_buyer_email($id){
        $email = FabricInquiry::where('fabric_inquiry.id', $id)
        ->join('users','users.id','fabric_inquiry.user_id')
        ->select('email','name')
        ->first()->toArray();

        return $email;
    }
    /* Download Fabric Inquiries */
    public static function download_fabric_inquiries($whereConditions){
        $inquiries = FabricInquiry::where($whereConditions)
            ->select()
            ->orderBy('created_at','desc')
            ->get();

        return $inquiries;
    }
}
