<?php

namespace App\Http\Controllers\WebSite\Inquiry;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Models\Inquiries;
use App\Models\InquiryContact;
use App\Models\InquiryFactoryFeedback;
use App\Models\InquiryFactoryResponse;
use App\Models\InquiryLabelCreate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DownloadInquiries extends Controller
{
    /* To Download All the Buyer Inquiries */
    public static function download_buyer_inquirys(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $whereConditions=[
            ['inquiry.company_id','=',$request->company_id],
            ['inquiry.workspace_id','=',$request->workspace_id]
        ];

        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        $advFilter = [];
        if(isset($request->article_id) && $request->article_id!=''){
            $whereConditions[]=['inquiry.article_id','=',$request->article_id];
            $advFilter['article'] = CommonApp::get_inquiry_article_name($request->article_id);
        }
        if(isset($request->factory_id) && $request->factory_id!=''){
            $whereConditions[]=['inquiry.factory_ids','like','%||'.$request->factory_id.'||%'];
            $advFilter['factory'] =CommonApp::get_inquiry_factory_name($request->factory_id);
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereConditions[]=['inquiry.created_at','>=',$from];
            $whereConditions[]=['inquiry.created_at','<=',$to];
            $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
            $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
        }
        App::setlocale($dateFormatAndLanguage['language']);

        $inquiries = Inquiries::download_buyer_inquiries($whereConditions);
        $data['inquiries']=$inquiries;
        $data['dateFormat'] = $dateFormatAndLanguage['dateFormat'];
        $data['advFilter'] = $advFilter;
        if(count($inquiries)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('BuyerInquiryPDF');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }

    /* To Download Factory Inquirys */
    public static function download_factory_inquirys(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'factory_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $contact_id = InquiryContact::where('factory_id',$request->factory_id)->pluck('id')->first();
        $advFilter = [];
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        if((int)$contact_id>0){
            $whereConditions=[
                ['inquiry.factory_ids','like','%||'.$contact_id.'||%']
            ];

            $whereConditions2=[
                ['inquiry.factory_ids','like','%||'.$contact_id.'||%'],
                ['inquiry_factory_response.factory_id','=',$request->factory_id]
            ];

            if(isset($request->article_id) && $request->article_id!=''){
                $whereConditions[]=['inquiry.article_id','=',$request->article_id];
                $advFilter['article'] = CommonApp::get_inquiry_article_name($request->article_id);
            }
            if(isset($request->buyer_id) && $request->buyer_id!=''){
                $whereConditions[]=['inquiry.user_id','=',$request->buyer_id];
                $advFilter['buyer'] = CommonApp::get_inquiry_buyer_name($request->buyer_id);
            }
            if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
                $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
                $to = date('Y-m-d 23:59:59');
                $whereConditions[]=['inquiry.created_at','>=',$from];
                $whereConditions[]=['inquiry.created_at','<=',$to];
                $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
                $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
            }
            if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
                $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
                $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
                $whereConditions[]=['inquiry.created_at','>=',$from];
                $whereConditions[]=['inquiry.created_at','<=',$to];
                $advFilter['startDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($from));
                $advFilter['endDate'] = date($dateFormatAndLanguage['dateFormat'],strtotime($to));
            }
            App::setlocale($dateFormatAndLanguage['language']);
            $inquiries = Inquiries::download_factory_inquiries($whereConditions,$whereConditions2);
            $response = InquiryFactoryResponse::where('factory_id',$request->factory_id)->pluck('inquiry_id');
            $data['inquiries']=$inquiries;
            $data['response']=$response;
            $data['dateFormat'] = $dateFormatAndLanguage['dateFormat'];
            $data['advFilter'] = $advFilter;
            if(count($inquiries)>0){
                view()->share("data",$data);
                $pdf = Pdf::loadView('FactoryInquiryPDF');
                $pdf->setPaper('A4', 'portrait');
                $pdf->getOptions()->setIsFontSubsettingEnabled(true);
                $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
                return $pdf->download();
            }
        }
    }

    public static function download_inquiry_factory_response(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'inquiry_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);
        $whereConditions=[
            ['inquiry_factory_response.inquiry_id','=',$request->inquiry_id]
        ];

        $responses= InquiryFactoryResponse::download_factory_response($whereConditions);
        $data['responses']=$responses;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['inquiry_id']=$request->inquiry_id;
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('BuyerFactoryResponsePDF');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }
    public static function download_factory_feedback(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);

        $responses= InquiryFactoryFeedback::get_factory_feedback($request);
        $data['responses']=$responses;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('FactoryFeedbackPDF');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
        }
    }
    public static function get_inquiry_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $responses= Inquiries::get_inquiry_label($request);
        $serverURL = config('filesystems.disks.s3.url');
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$responses,"serverURL"=>$serverURL],200);
        return CommonApp::webEncrypt($res);
    }
    public static function download_get_inquiry_label(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'inquiry_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required',
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);

        /*Inquriy Label creation fetch and create */
        $user_info = InquiryLabelCreate::get_label_create_info($request);

        $responses= Inquiries::get_inquiry_label($request);
        $data['responses']=$responses;
        $data['user_info']=$user_info;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = config('filesystems.disks.s3.url');
        //dd($data);
        if(count($responses)>0){
            view()->share("data",$data);
            $pdf = Pdf::loadView('InquiryLabelPDF');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            return $pdf->download();
            $path = public_path() . '/Inquiry/label.pdf';
            $pdf->save($path);
        }
    }
}
