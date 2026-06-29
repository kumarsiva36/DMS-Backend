<?php
namespace App\Http\Controllers\Mobile\Fabric;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use App\Models\CompanySettings;
use App\Models\FabricContact;
use App\Models\FabricInquiry;
use App\Models\FabricInquiryLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\FabricMaster;
use App\Models\FabricSupplierResponse;
use App\Models\Inquiries;
use App\Models\User;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class Fabric extends Controller
{
    public static function get_fabric_master(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
           // 'type' => 'required',
            'referenceId'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }

        if(isset($request->type) && $request->type!=''){
            $whereConditions=[
                ['type','=',$request->type]
            ];

            $data = FabricMaster::where($whereConditions)
                        ->where(function ($query) use($request) {
                            $query->where('reference_id', '=', 0)
                                ->orWhere('reference_id', '=', $request->referenceId);
                        })
                        ->get();
        }else{
            $data = FabricMaster::where(function ($query) use($request) {
                            $query->where('reference_id', '=', 0)
                                ->orWhere('reference_id', '=', $request->referenceId);
                        })
                        ->get();
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function add_fabric_master(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'type' => 'required',
            'content'=>'required',
            'referenceId'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $filedata['type']=$request->type;
        $filedata['content']=$request->content;
        $filedata['reference_id']=$request->referenceId;
        $filedata['created_at']=date('Y-m-d H:i:s');

        FabricMaster::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Data Added Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function get_fabric_inquiry_ids(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_type' => 'required',
            'user_id'=>'required',
            'company_id'=>'required',
            'workspace_id'=>'required',
            'login_type' => 'required',
        ]);
        $inquires =Inquiries::get_fabric_inquiry_ids($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquires],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function save_fabric_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'yarn_count' => 'required',
            'yarn_quantity' => 'required',
            'yarn_quality'=>'required',
            'meterial'=>'required',
            'composition' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        DB::beginTransaction();
        try{
            FabricInquiry::insert_fabric_inquiry($request);
            $inquiryID = DB::getPdo()->lastInsertId();
            /* Inquiry Create Log starts */
            try{

                FabricInquiryLog::create_inquiry_log($inquiryID,$request);
            }catch(Exception $e){

            }
            /* Inquiry Create Log end */
            Fabric::inquiry_pdf_generate($inquiryID,$request);
        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Fabric Inquiry Added Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function inquiry_pdf_generate($inquiryID,$request){
        $request->inquiry_id = $inquiryID; //$inquiryID;
        $user = CommonApp::getUserDetailsById($request->user_id);
        $company = CommonApp::getCompanyDetailsbyID($request->company_id);
        $datas['inquiryID'] = $inquiryID;
        $datas['request'] = $request;
        $datas['user'] = $user;
        $datas['logo'] = $company->logo;
        view()->share("datas",$datas);
        //return view('InquiryPDF');
        $pdf = Pdf::loadView('FabricPDF');
        $pdf->setPaper('A4', 'portrait');
        //$pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
        $pdf->setOption("enable_php", true);
        $path = public_path() . '/Fabric/' .$inquiryID.'.pdf';
        $pdf->save($path);
    }
    public static function get_fabric_inquiry_list(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;
        $inquiries = FabricInquiry::get_inquirys($request);
        $pdfpath  = config('app.public_url').'Fabric/';
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiries,"pdfpath"=>$pdfpath],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function fabric_inquiry_details(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $inquiry = FabricInquiry::inquiry_details($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$inquiry],200);
        return CommonApp::apiEncrypt($res);
    }
    /* To Update The Inquiry */
    public static function update_fabric_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id'=>'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
            'yarn_count' => 'required',
            'yarn_quantity' => 'required',
            'yarn_quality'=>'required',
            'meterial'=>'required',
            'composition' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        DB::beginTransaction();
        try{
            $update = FabricInquiry::update_fabric_inquiry($request);
            $inquiryID = $request->inquiry_id;
            /* Inquiry Update Log starts */
            try{
                FabricInquiryLog::edit_inquiry_log($inquiryID,$request);
            }catch(Exception $e){
            }
            /* Inquiry Update Log end */
            Fabric::inquiry_pdf_generate($inquiryID,$request);

        }catch(Exception $e){
            DB::rollBack();
            $res = json_encode(["status_code"=>201,'status'=>"failure","message"=>$e->getMessage()],201);
            return CommonApp::apiEncrypt($res);
        }
        DB::commit();
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Updated Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function send_fabric_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'supplier_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $facts = implode('||',$request->supplier_id);
        $facts = $facts.'||';
        $update = DB::table('fabric_inquiry')
        ->where('id', $request->inquiry_id)
        ->limit(1)
        ->update(array('supplier_ids' => DB::raw("concat(ifnull(supplier_ids,'||'), '".(string)$facts."')")));
        $factories = FabricContact::select('id','supplier','contact_person','contact_email')->whereIn('id',$request->supplier_id)->get();
        $userDetails = User::where('id',$request->user_id)->pluck('name')->first();
        $companyDetail = CompanySettings::where('id',$request->company_id)->pluck('company_name')->first();
        foreach ($factories as $fact){
            $details['created_by'] = $fact->contact_person;
            $details["email"] = $fact->contact_email;
            $details["title"] = 'Fabric Inquiry Request - '.$companyDetail.' - '.$userDetails;
            $file = public_path() . '/' .$request->inquiry_id.'.pdf';
            if(!file_exists($file))
             $file = public_path() . '/Fabric/' .$request->inquiry_id.'.pdf';

            if($fact->contact_email){
                Mail::send('FabricInquiryMail', ['details'=>$details], function($message)use($details, $file) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                    if(file_exists($file))
                        $message->attach($file);
                });
            }
        }
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry sent Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function add_fabric_contact(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'supplier' => 'required|max:60',
            'contact_person' => 'required|max:60',
            'contact_number' => 'required|unique:fabric_contact',
            'contact_email' => 'required|email:dns,rfc|unique:fabric_contact',
            'address' => 'required',
            'city' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $filedata['supplier']=$request->supplier ?? "";
        $filedata['contact_person']=$request->contact_person ?? "";
        $filedata['contact_number']=$request->contact_number ?? "";
        $filedata['contact_email']=$request->contact_email ?? '';
        $filedata['address']=$request->address ?? '';
        $filedata['city']=$request->city ?? '';
        $filedata['created_at']=date('Y-m-d H:i:s');
        FabricContact::insert($filedata);
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Supplier Added Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function get_fabric_contact(){
        $suppliers = FabricContact::select('id','supplier','contact_person','contact_number','contact_email')->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$suppliers],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function fabric_supplier_list(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $suppliers = FabricInquiry::getSuppliers($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$suppliers],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function inquiry_supplier_response(Request $request ){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $factories = FabricSupplierResponse::FactoryResponse($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function inquiry_supplier_list(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $factories = FabricInquiry::getFactories($request);
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$factories],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function delete_fabric_inquiry(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        FabricInquiry::deleteInquiry($request);
        /* Inquiry delete Log starts */
        try{
            FabricInquiryLog::delete_inquiry_log($request);
        }catch(Exception $e){
        }
        /* Inquiry delete Log end */
        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Fabric Inquiry Deleted Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }
    /* To get the supplier list who are all not using the DMS */
    public static function get_supplier_list_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $res = FabricSupplierResponse::get_factory_list_response($request);
        $currency = FabricInquiry::where('id',$request->inquiry_id)->pluck('currency')->first();
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$res,'currency'=>$currency],200);
        return CommonApp::apiEncrypt($res);
    }
    public static function save_fabric_inquiry_supplier_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'supplier_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'user_type' => 'required',
            'price'=>'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $filedata['inquiry_id']=$request->inquiry_id ?? 0;
        $filedata['supplier_id']=$request->supplier_id ?? 0;
        $filedata['price']=$request->price ?? 0;
        $filedata['comments']=$request->comments ?? '';
        $filedata['updated_by_type']=$request->user_type ?? 'user';
        $filedata['updated_by']=$request->user_id ?? 0;

        FabricSupplierResponse::insert($filedata);

        $buyer = FabricInquiry::get_buyer_email($request->inquiry_id);
        if(!empty($buyer)){
            $factory = FabricContact::where('id',$request->supplier_id)->select('supplier','contact_person')->first()->toArray();
            $details['name'] = $buyer['name'];
            $details['factory_name'] = $factory['supplier'];
            $details['contact_name'] = $factory['contact_person'];
            $details["email"] = $buyer['email'];
            $details['price']=$request->price ?? 0;
            $details['inquiry_id']=$request->inquiry_id ?? 0;
            $details['comments']=$request->comments ?? '';
            $details["title"] = "Fabric Inquiry Response from ". $factory['supplier']."(FBIN-".$request->inquiry_id.")";
            if($buyer['email']){
                Mail::send('InquiryResponseMail', ['details'=>$details], function($message)use($details) {
                    $message->to($details["email"])
                            ->subject($details["title"]);
                });
            }
        }

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Inquiry Response Added Successfully"],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function get_reference_inquiry_currency(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $res = Inquiries::where('id',$request->inquiry_id)->pluck('currency')->first();
        $res = json_encode(["status_code"=>200,'status'=>"success",'data'=>$res],200);
        return CommonApp::apiEncrypt($res);
    }

    public static function fabric_inquiry_pdf_download(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $whereConditions=[
            ['fabric_inquiry.company_id','=',$request->company_id],
            ['fabric_inquiry.workspace_id','=',$request->workspace_id]
        ];

        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        $advFilter = [];

        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['fabric_inquiry.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['fabric_inquiry.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }
        App::setlocale($dateFormatAndLanguage['language']);

        $inquiries = FabricInquiry::download_fabric_inquiries($whereConditions);
        $data['inquiries']=$inquiries;
        $data['dateFormat'] = $dateFormatAndLanguage['dateFormat'];
        $data['advFilter'] = $advFilter;
        if(count($inquiries)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('FabricInquiryPDF');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }

    public static function download_inquiry_supplier_response(Request $request){
        $request= CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'inquiry_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"errors"=>$validator->errors()]);
            return CommonApp::apiEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);
        $whereConditions=[
            ['inquiry_id','=',$request->inquiry_id]
        ];

        $responses= FabricSupplierResponse::download_supplier_response($whereConditions);
        $data['responses']=$responses;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['inquiry_id']=$request->inquiry_id;
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('SupplierResponsePDF');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }


}
