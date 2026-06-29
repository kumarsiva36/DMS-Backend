<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ArticleName;
use App\Models\FabricType;
use App\Models\IncomeTerms;

class InquiryLog extends Model
{
    use HasFactory;

    protected $table = 'inquiry_log';

    public static function create_inquiry_log($inquiry_id,$request){
        $data = [];
        // $data['Article']= $request->article_id ? InquiryLog::get_article_name($request->article_id) : '-';
        // $data['Style_No']= $request->style_no ?? '-';
        // $data['Fabric_Composition']= $request->fabric_type_id ? InquiryLog::get_fabric_name($request->fabric_type_id) : '-';
        // $data['Fabric_GSM']= $request->fabric_GSM ?? '-';
        // $data['Fabric_Type']= $request->fabric_type ?? '-';
        // $data['Yarn_Count']= $request->yarn_count ?? '-';
        // $data['Inquiry_Due_Date']= $request->due_date ?? '-';
        // $data['Incoterms']= (isset($request->incoterms) && $request->incoterms!=0 ) ? InquiryLog::get_incoterms_name($request->incoterms) : '-';
        // $data['Currency']= $request->currency ?? '-';
        // $data['Target Price']= $request->target_price ?? 0;
        // $data['Payment Terms']= $request->payment_terms ?? '-';
        // $data['Payment Instructions']= $request->payment_instructions ?? '-';
        // $data['Style/Article Description']= $request->style_article_description ?? '-';
        // $data['Special Finishers -If any']= $request->special_finish ?? '-';
        // $data['Total Quantity']= $request->total_qty ?? 0;
        // $data['Patterns']= $request->patterns ?? '-';
        // $data['Place of Jurisdiction']= $request->jurisdiction ?? '-';
        // $data['Customs Declaration Document']= $request->customs_declaraion_document ?? '-';
        // $data['Penalty']= $request->penality ?? '-';
        // $data['Print Size']= $request->print_size ?? '-';
        // $data['Print Type']= $request->print_type ?? '-';
        // $data['No of Colors']= $request->print_no_of_colors  ?? '-';
        // $data['Main Label']= $request->main_lable_info  ?? '-';
        // $data['Wash Care Label']= $request->washcare_lable_info ?? '-';
        // $data['Hangtag']= $request->hangtag_lable_info ?? '-';
        // $data['Barcode Stickers']= $request->barcode_lable_info ?? '-';
        // $data['Trims Notifications- Specify If any']= $request->trims_nominations ?? '-';
        // $data['Polybag Size & Thickness']= $request->poly_bag_size ?? '-';
        // $data['Polybag Material']= $request->poly_bag_material ?? '-';
        // $data['Print Details on Polybag']= $request->poly_bag_price ?? '-';
        // $data['Carton Box Dimensions']= $request->carton_bag_dimensions ?? '-';
        // $data['Carton Color']= $request->carton_color ?? '-';
        // $data['No of Ply']= $request->carton_material ?? '-';
        // $data['Carton Edge Finish']= $request->carton_edge_finish ?? '-';
        // $data['Carton Mark Details']= $request->carton_mark ?? '-';
        // $data['Make-Up']= $request->make_up ?? '-';
        // $data['Films-CD']= $request->films_cd ?? '-';
        // $data['Picture-Card']= $request->picture_card ?? '-';
        // $data['Inner Cardboard']= $request->inner_cardboard ?? '-';
        // $data['Shipping Size']= $request->shipping_size ?? '-';
        // $data['Air Freight']= $request->air_frieght ?? '-';
        // $data['Estimated Delivery Date']= $request->estimate_delivery_date ?? '-';
        // $data['Forbidden Substances Information']= $request->forbidden_substance_info ?? '-';
        // $data['Testing Requirement']= $request->testing_requirements ?? '-';
        // $data['Sample Requirements']= $request->sample_requirements ?? '-';
        // $data['Special Request -If any']= $request->special_requests ?? '-';

        $logArry = array();
        $logArry['inquiry_id'] =$inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Create";
        //$data['sku']= $request->sku_details;
        //$logArry['after_values'] = json_encode($data);
        InquiryLog::insert($logArry);
    }
    public static function edit_inquiry_log($inquiry_id,$request){
        $data = [];$before = [];

        if(isset($request->article_id) && isset($request->inquiryDetails->article_id) && ($request->article_id!=$request->inquiryDetails->article_id) ){
            $data['Article']= $request->article_id ? InquiryLog::get_article_name($request->article_id) : '-';
            $before['Article']= $request->inquiryDetails->article_id ? InquiryLog::get_article_name($request->inquiryDetails->article_id) : '-';
        }
        if(isset($request->style_no) && isset($request->inquiryDetails->style_no) && ($request->style_no!=$request->inquiryDetails->style_no) ){
            $data['Style_No']= $request->style_no;
            $before['Style_No']= $request->inquiryDetails->style_no;
        }
        if(isset($request->fabric_type_id) && isset($request->inquiryDetails->fabric_type_id) && ($request->fabric_type_id!=$request->inquiryDetails->fabric_type_id) ){
            $data['Fabric_Composition']= $request->fabric_type_id ? InquiryLog::get_fabric_name($request->fabric_type_id) : '-';
            $before['Fabric_Composition']= $request->inquiryDetails->fabric_type_id ? InquiryLog::get_fabric_name($request->inquiryDetails->fabric_type_id) : '-';
        }
        if(isset($request->fabric_GSM) && isset($request->inquiryDetails->fabric_GSM) && ($request->fabric_GSM!=$request->inquiryDetails->fabric_GSM) ){
            $data['Fabric_GSM']= $request->fabric_GSM ?? '-';
            $before['Fabric_GSM']= $request->inquiryDetails->fabric_GSM ?? '-';
        }
        if(isset($request->fabric_type) && isset($request->inquiryDetails->fabric_type) && ($request->fabric_type!=$request->inquiryDetails->fabric_type) ){
            $data['Fabric_Type']= $request->fabric_type ?? '-';
            $before['Fabric_Type']= $request->inquiryDetails->fabric_type ?? '-';
        }
        if(isset($request->yarn_count) && isset($request->inquiryDetails->yarn_count) && ($request->yarn_count!=$request->inquiryDetails->yarn_count) ){
            $data['Yarn_Count']= $request->yarn_count ?? '-';
            $before['Yarn_Count']= $request->inquiryDetails->yarn_count ?? '-';
        }
        if(isset($request->due_date) && isset($request->inquiryDetails->due_date) && ($request->due_date!=$request->inquiryDetails->due_date) ){
            $data['Inquiry_Due_Date']= $request->due_date ?? '-';
            $before['Inquiry_Due_Date']= $request->inquiryDetails->due_date ?? '-';
        }
        if(isset($request->incoterms) && isset($request->inquiryDetails->incoterms) && ($request->incoterms!=$request->inquiryDetails->incoterms) ){
            $data['Incoterms']= (isset($request->incoterms) && $request->incoterms!=0 ) ? InquiryLog::get_incoterms_name($request->incoterms) : '-';
            $before['Incoterms']= (isset($request->inquiryDetails->incoterms) && $request->inquiryDetails->incoterms!=0 ) ? InquiryLog::get_incoterms_name($request->inquiryDetails->incoterms) : '-';
        }
        if(isset($request->currency) && isset($request->inquiryDetails->currency) && ($request->currency!=$request->inquiryDetails->currency) ){
            $data['Currency']= $request->currency ?? '-';
            $before['Currency']= $request->inquiryDetails->currency ?? '-';
        }
        if(isset($request->target_price) && isset($request->inquiryDetails->target_price) && ($request->target_price!=$request->inquiryDetails->target_price) ){
            $data['Target Price']= $request->target_price ?? '-';
            $before['Target Price']= $request->inquiryDetails->target_price ?? '-';
        }
        if(isset($request->payment_terms) && isset($request->inquiryDetails->payment_terms) && ($request->payment_terms!=$request->inquiryDetails->payment_terms) ){
            $data['Payment Terms']= $request->payment_terms ?? '-';
            $before['Payment Terms']= $request->inquiryDetails->payment_terms ?? '-';
        }
        if(isset($request->payment_instructions) && isset($request->inquiryDetails->payment_instructions) && ($request->payment_instructions!=$request->inquiryDetails->payment_instructions) ){
            $data['Payment Instructions']= $request->payment_instructions ?? '-';
            $before['Payment Instructions']= $request->inquiryDetails->payment_instructions ?? '-';
        }
        if(isset($request->style_article_description) && isset($request->inquiryDetails->style_article_description) && ($request->style_article_description!=$request->inquiryDetails->style_article_description) ){
            $data['Style/Article Description']= $request->style_article_description ?? '-';
            $before['Style/Article Description']= $request->inquiryDetails->style_article_description ?? '-';
        }
        if(isset($request->special_finish) && isset($request->inquiryDetails->special_finish) && ($request->special_finish!=$request->inquiryDetails->special_finish) ){
            $data['Special Finishers -If any']= $request->special_finish ?? '-';
            $before['Special Finishers -If any']= $request->inquiryDetails->special_finish ?? '-';
        }
        if(isset($request->total_qty) && isset($request->inquiryDetails->total_qty) && ($request->total_qty!=$request->inquiryDetails->total_qty) ){
            $data['Total Quantity']= $request->total_qty ?? '0';
            $before['Total Quantity']= $request->inquiryDetails->total_qty ?? '0';
        }
        if(isset($request->patterns) && isset($request->inquiryDetails->patterns) && ($request->patterns!=$request->inquiryDetails->patterns) ){
            $data['Patterns']= $request->patterns ?? '-';
            $before['Patterns']= $request->inquiryDetails->patterns ?? '-';
        }
        if(isset($request->jurisdiction) && isset($request->inquiryDetails->jurisdiction) && ($request->jurisdiction!=$request->inquiryDetails->jurisdiction) ){
            $data['Place of Jurisdiction']= $request->jurisdiction ?? '-';
            $before['Place of Jurisdiction']= $request->inquiryDetails->jurisdiction ?? '-';
        }
        if(isset($request->customs_declaraion_document) && isset($request->inquiryDetails->customs_declaraion_document) && ($request->customs_declaraion_document!=$request->inquiryDetails->customs_declaraion_document) ){
            $data['Customs Declaration Document']= $request->customs_declaraion_document ?? '-';
            $before['Customs Declaration Document']= $request->inquiryDetails->customs_declaraion_document ?? '-';
        }
        if(isset($request->penality) && isset($request->inquiryDetails->penality) && ($request->penality!=$request->inquiryDetails->penality) ){
            $data['Penalty']= $request->penality ?? '-';
            $before['Penalty']= $request->inquiryDetails->penality ?? '-';
        }
        if(isset($request->print_size) && isset($request->inquiryDetails->print_size) && ($request->print_size!=$request->inquiryDetails->print_size) ){
            $data['Print Size']= $request->print_size ?? '-';
            $before['Print Size']= $request->inquiryDetails->print_size ?? '-';
        }
        if(isset($request->print_type) && isset($request->inquiryDetails->print_type) && ($request->print_type!=$request->inquiryDetails->print_type) ){
            $data['Print Type']= $request->print_type ?? '-';
            $before['Print Type']= $request->inquiryDetails->print_type ?? '-';
        }
        if(isset($request->print_no_of_colors) && isset($request->inquiryDetails->print_no_of_colors) && ($request->print_no_of_colors!=$request->inquiryDetails->print_no_of_colors) ){
            $data['No of Colors']= $request->print_no_of_colors ?? '-';
            $before['No of Colors']= $request->inquiryDetails->print_no_of_colors ?? '-';
        }
        if(isset($request->main_lable_info) && isset($request->inquiryDetails->main_lable_info) && ($request->main_lable_info!=$request->inquiryDetails->main_lable_info) ){
            $data['Main Label']= $request->main_lable_info ?? '-';
            $before['Main Label']= $request->inquiryDetails->main_lable_info ?? '-';
        }
        if(isset($request->washcare_lable_info) && isset($request->inquiryDetails->washcare_lable_info) && ($request->washcare_lable_info!=$request->inquiryDetails->washcare_lable_info) ){
            $data['Wash Care Label']= $request->washcare_lable_info ?? '-';
            $before['Wash Care Label']= $request->inquiryDetails->washcare_lable_info ?? '-';
        }
        if(isset($request->hangtag_lable_info) && isset($request->inquiryDetails->hangtag_lable_info) && ($request->hangtag_lable_info!=$request->inquiryDetails->hangtag_lable_info) ){
            $data['Hangtag']= $request->hangtag_lable_info ?? '-';
            $before['Hangtag']= $request->inquiryDetails->hangtag_lable_info ?? '-';
        }
        if(isset($request->barcode_lable_info) && isset($request->inquiryDetails->barcode_lable_info) && ($request->barcode_lable_info!=$request->inquiryDetails->barcode_lable_info) ){
            $data['Barcode Stickers']= $request->barcode_lable_info ?? '-';
            $before['Barcode Stickers']= $request->inquiryDetails->barcode_lable_info ?? '-';
        }
        if(isset($request->trims_nominations) && isset($request->inquiryDetails->trims_nominations) && ($request->trims_nominations!=$request->inquiryDetails->trims_nominations) ){
            $data['Trims Notifications- Specify If any']= $request->trims_nominations ?? '-';
            $before['Trims Notifications- Specify If any']= $request->inquiryDetails->trims_nominations ?? '-';
        }
        if(isset($request->poly_bag_size) && isset($request->inquiryDetails->poly_bag_size) && ($request->poly_bag_size!=$request->inquiryDetails->poly_bag_size) ){
            $data['Polybag Size & Thickness']= $request->poly_bag_size ?? '-';
            $before['Polybag Size & Thickness']= $request->inquiryDetails->poly_bag_size ?? '-';
        }
        if(isset($request->poly_bag_material) && isset($request->inquiryDetails->poly_bag_material) && ($request->poly_bag_material!=$request->inquiryDetails->poly_bag_material) ){
            $data['Polybag Material']= $request->poly_bag_material ?? '-';
            $before['Polybag Material']= $request->inquiryDetails->poly_bag_material ?? '-';
        }
        if(isset($request->poly_bag_price) && isset($request->inquiryDetails->poly_bag_price) && ($request->poly_bag_price!=$request->inquiryDetails->poly_bag_price) ){
            $data['Print Details on Polybag']= $request->poly_bag_price ?? '-';
            $before['Print Details on Polybag']= $request->inquiryDetails->poly_bag_price ?? '-';
        }
        if(isset($request->carton_bag_dimensions) && isset($request->inquiryDetails->carton_bag_dimensions) && ($request->carton_bag_dimensions!=$request->inquiryDetails->carton_bag_dimensions) ){
            $data['Carton Box Dimensions']= $request->carton_bag_dimensions ?? '-';
            $before['Carton Box Dimensions']= $request->inquiryDetails->carton_bag_dimensions ?? '-';
        }
        if(isset($request->carton_color) && isset($request->inquiryDetails->carton_color) && ($request->carton_color!=$request->inquiryDetails->carton_color) ){
            $data['Carton Color']= $request->carton_color ?? '-';
            $before['Carton Color']= $request->inquiryDetails->carton_color ?? '-';
        }
        if(isset($request->carton_material) && isset($request->inquiryDetails->carton_material) && ($request->carton_material!=$request->inquiryDetails->carton_material) ){
            $data['No of Ply']= $request->carton_material ?? '-';
            $before['No of Ply']= $request->inquiryDetails->carton_material ?? '-';
        }
        if(isset($request->carton_edge_finish) && isset($request->inquiryDetails->carton_edge_finish) && ($request->carton_edge_finish!=$request->inquiryDetails->carton_edge_finish) ){
            $data['Carton Edge Finish']= $request->carton_edge_finish ?? '-';
            $before['Carton Edge Finish']= $request->inquiryDetails->carton_edge_finish ?? '-';
        }
        if(isset($request->carton_mark) && isset($request->inquiryDetails->carton_mark) && ($request->carton_mark!=$request->inquiryDetails->carton_mark) ){
            $data['Carton Mark Details']= $request->carton_mark ?? '-';
            $before['Carton Mark Details']= $request->inquiryDetails->carton_mark ?? '-';
        }
        if(isset($request->make_up) && isset($request->inquiryDetails->make_up) && ($request->make_up!=$request->inquiryDetails->make_up) ){
            $data['Make-Up']= $request->make_up ?? '-';
            $before['Make-Up']= $request->inquiryDetails->make_up ?? '-';
        }
        if(isset($request->films_cd) && isset($request->inquiryDetails->films_cd) && ($request->films_cd!=$request->inquiryDetails->films_cd) ){
            $data['Films-CD']= $request->films_cd ?? '-';
            $before['Films-CD']= $request->inquiryDetails->films_cd ?? '-';
        }
        if(isset($request->picture_card) && isset($request->inquiryDetails->picture_card) && ($request->picture_card!=$request->inquiryDetails->picture_card) ){
            $data['Picture-Card']= $request->picture_card ?? '-';
            $before['Picture-Card']= $request->inquiryDetails->picture_card ?? '-';
        }
        if(isset($request->inner_cardboard) && isset($request->inquiryDetails->inner_cardboard) && ($request->inner_cardboard!=$request->inquiryDetails->inner_cardboard) ){
            $data['Inner Cardboard']= $request->inner_cardboard ?? '-';
            $before['Inner Cardboard']= $request->inquiryDetails->inner_cardboard ?? '-';
        }
        if(isset($request->shipping_size) && isset($request->inquiryDetails->shipping_size) && ($request->shipping_size!=$request->inquiryDetails->shipping_size) ){
            $data['Shipping Size']= $request->shipping_size ?? '-';
            $before['Shipping Size']= $request->inquiryDetails->shipping_size ?? '-';
        }
        if(isset($request->air_frieght) && isset($request->inquiryDetails->air_frieght) && ($request->air_frieght!=$request->inquiryDetails->air_frieght) ){
            $data['Air Freight']= $request->air_frieght ?? '-';
            $before['Air Freight']= $request->inquiryDetails->air_frieght ?? '-';
        }
        if(isset($request->estimate_delivery_date) && isset($request->inquiryDetails->estimate_delivery_date) && ($request->estimate_delivery_date!=$request->inquiryDetails->estimate_delivery_date) ){
            $data['Estimated Delivery Date']= $request->estimate_delivery_date ?? '-';
            $before['Estimated Delivery Date']= $request->inquiryDetails->estimate_delivery_date ?? '-';
        }
        if(isset($request->forbidden_substance_info) && isset($request->inquiryDetails->forbidden_substance_info) && ($request->forbidden_substance_info!=$request->inquiryDetails->forbidden_substance_info) ){
            $data['Forbidden Substances Information']= $request->forbidden_substance_info ?? '-';
            $before['Forbidden Substances Information']= $request->inquiryDetails->forbidden_substance_info ?? '-';
        }
        if(isset($request->testing_requirements) && isset($request->inquiryDetails->testing_requirements) && ($request->testing_requirements!=$request->inquiryDetails->testing_requirements) ){
            $data['Testing Requirement']= $request->testing_requirements ?? '-';
            $before['Testing Requirement']= $request->inquiryDetails->testing_requirements ?? '-';
        }
        if(isset($request->sample_requirements) && isset($request->inquiryDetails->sample_requirements) && ($request->sample_requirements!=$request->inquiryDetails->sample_requirements) ){
            $data['Sample Requirements']= $request->sample_requirements ?? '-';
            $before['Sample Requirements']= $request->inquiryDetails->sample_requirements ?? '-';
        }
        if(isset($request->special_requests) && isset($request->inquiryDetails->special_requests) && ($request->special_requests!=$request->inquiryDetails->special_requests) ){
            $data['Special Request -If any']= $request->special_requests ?? '-';
            $before['Special Request -If any']= $request->inquiryDetails->special_requests ?? '-';
        }

        $logArry = array();
        $logArry['inquiry_id'] =$inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Edit";
        //$data['sku']= $request->sku_details;
        $logArry['after_values'] = !empty($data)?json_encode($data):'';
        $logArry['before_values'] = !empty($before)?json_encode($before):'';
        InquiryLog::insert($logArry);
    }

    public static function edit_inquiry_media_log($refId,$request,$filedata){

        $data=[];
        $data['file_type']=$request->type;
        $data['filepath']=config('filesystems.disks.s3.url').$filedata['filepath'];
        $data['orginalfilename']=$filedata['orginalfilename'];

        $logArry = array();
        $logArry['inquiry_id'] =InquiryLog::get_inquiry_name($refId);
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="File Added";
        $logArry['after_values'] = json_encode($data);
        InquiryLog::insert($logArry);
    }
    public static function delete_inquiry_media_log($request){

        $data=[];
        $data['file_type']=InquiryLog::get_media_file_type($request->media_id);

        $logArry = array();
        $logArry['inquiry_id'] =InquiryLog::get_media_inquiry_id($request->media_id);
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="File Deleted";
        $logArry['after_values'] = json_encode($data);
        InquiryLog::insert($logArry);
    }
    public static function delete_inquiry_log($request){
        $logArry = array();
        $logArry['inquiry_id'] =$request->inquiry_id;
        $logArry['company_id'] = $request->company_id??0;
        $logArry['workspace_id'] = $request->workspace_id??0;
        $logArry['staff_id'] =$request->staff_id ?? 0;
        $logArry['user_id'] = $request->user_id ?? 0;
        $logArry['action'] ="Delete";
        InquiryLog::insert($logArry);
    }

    public static function get_article_name($id){
        return ArticleName::where('id',$id)->pluck('name')->first();
    }
    public static function get_fabric_name($id){
        return FabricType::where('id',$id)->pluck('name')->first();
    }
    public static function get_incoterms_name($id){
        return IncomeTerms::where('id',$id)->pluck('name')->first();
    }
    public static function get_inquiry_name($id){
        return Inquiries::where('media_reference_id',$id)->pluck('id')->first();
    }
    public static function get_media_inquiry_id($id){
        return InquiryMedia::where('id',$id)->pluck('inquiry_id')->first();
    }
    public static function get_media_file_type($id){
        return InquiryMedia::where('id',$id)->pluck('media_type')->first();
    }
}
