<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class InquiryPOForwarder extends Model
{
    use HasFactory;

    protected $table = 'inquiry_po_forwarder';

    public static function get_forwarder_list($request){
        return InquiryPOForwarder::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                   // ->whereRaw('FIND_IN_SET('.$request->category_id.', category_ids)')
                                    ->select('id','company_name', 'address','contact_person','contact_phone','contact_email')
                                    ->orderBy('contact_person', 'ASC')->get();
    }

    public static function get_all_vendors_list($request){
        return InquiryPOForwarder::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                    ->select('id','vendor_name', 'office_address','category_ids')
                                    ->orderBy('vendor_name', 'ASC')->get();
    }
    public static function get_all_vendors_list_array($request){
        return InquiryPOForwarder::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                    ->select('id','vendor_name', 'office_address','category_ids')
                                    ->orderBy('vendor_name', 'ASC')->get()->toArray();
    }



}
