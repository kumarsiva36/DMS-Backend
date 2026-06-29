<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class InquiryLabelVendor extends Model
{
    use HasFactory;

    protected $table = 'inquiry_label_vendor_list';

    public static function get_vendors_list($request){
        return InquiryLabelVendor::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                   // ->whereRaw('FIND_IN_SET('.$request->category_id.', category_ids)')
                                    ->select('id','vendor_name', 'office_address')
                                    ->orderBy('vendor_name', 'ASC')->get();
    }

    public static function get_all_vendors_list($request){
        return InquiryLabelVendor::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                    ->select('id','vendor_name', 'office_address','category_ids')
                                    ->orderBy('vendor_name', 'ASC')->get();
    }
    public static function get_all_vendors_list_array($request){
        return InquiryLabelVendor::where('company_id', $request->company_id)->where('workspace_id', $request->workspace_id)
                                    ->select('id','vendor_name', 'office_address','category_ids')
                                    ->orderBy('vendor_name', 'ASC')->get()->toArray();
    }



}
