<?php

namespace App\Http\Controllers\Mobile\v1\TechPack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use App\Models\TechPackImages;
use App\Models\TechPack;
use App\Models\TechPackDetails;
use App\Models\User;
use App\Models\Staff;
use App\Common\Uploads;
use App\Http\Controllers\WebSite\Common\MobileNotification;
use App\Models\TechPackComments;
use App\Models\TechPackEditDetails;
use App\Models\TechpackLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Imagick;
use ImagickPixel;
//use Image;
use Intervention\Image\Facades\Image;

class TeckPackDetail extends Controller
{

    /*View Tech Pack */
    public function viewTeckPack(Request $request)
    {
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //  $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
           // ['user_id', '=', $request->user_id],
           // ['staff_id', '=', $request->staff_id]
        ];
        if(isset($request->article_id) && intval($request->article_id>0)){
            $whereCondition[]=['article_id','=',$request->article_id];
        }
        if(isset($request->fabric_id) && intval($request->fabric_id>0)){
            $whereCondition[]=['fabric_id','=',$request->fabric_id];
        }
        if(isset($request->category_id) && intval($request->category_id>0)){
            $whereCondition[]=['category_id','=',$request->category_id];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date==''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59');
            $whereCondition[]=['created_at','>=',$from];
            $whereCondition[]=['created_at','<=',$to];
        }
        if(isset($request->from_date) && isset($request->to_date) && $request->from_date!='' && $request->to_date!=''){
            $from = date('Y-m-d 00:00:00',strtotime($request->from_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->to_date));
            $whereCondition[]=['created_at','>=',$from];
            $whereCondition[]=['created_at','<=',$to];
        }
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;
        $tec = TechPack::where($whereCondition)->orderBy('id', 'DESC')->paginate(20, ['*'], 'page', $request->page);
        $res = json_encode(["status_code" => 200, "status" => "Success", "data" => $tec,"filterData"=>$this->techPackFilters($request)]);
        return CommonApp::apiEncrypt($res);
    }

    /*Teckpack Filters */
    private function techPackFilters($request){
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id]
        ];
        $filterArray=[];
        $filterArrayData = TechPack::select("article_id as id", "article_name as name", DB::raw("'article' as type"))
        ->where($whereCondition)
        ->where("article_name", "!=", "")
        ->groupBy("article_name")
        ->orderBy("article_name", "asc")
        ->union(
            TechPack::select("fabric_id as id", "fabric_name as name", DB::raw("'fabric' as type"))
                ->where($whereCondition)
                ->where("fabric_name", "!=", "")
                ->groupBy("fabric_name")
                ->orderBy("fabric_name", "asc")
        )
        ->union(
            TechPack::select("category_id as id", "category_name as name", DB::raw("'category' as type"))
                ->where($whereCondition)
                ->where("category_name", "!=", "")
                ->groupBy("category_name")
                ->orderBy("category_name", "asc")
        )
        ->get();

        $filterArray['article'] = $filterArrayData->where('type', 'article')->values();
        $filterArray['fabric'] = $filterArrayData->where('type', 'fabric')->values();
        $filterArray['category'] = $filterArrayData->where('type', 'category')->values();
        return $filterArray;
    }

    /*Download Tech pack PDF */
    public function downloadTeckPackPDF(Request $request)
    {
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id', '=', $request->techpack_id]
        ];
        if(isset($request->techpack_type) && $request->techpack_type!=''){
            $whereCondition2 = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['techpack_type', '=', $request->techpack_type],
                ['is_publish', '=', 1],
            ];
        }else{
            $whereCondition2 = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['is_publish', '=', 1],
            ];
        }
        // $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        // App::setlocale($dateFormatAndLanguage['language']);
        $data = [];
        $tec = TechPack::select("id as techpack_id", 'staff_id', 'user_id', "po_no", "po_id", "style_no", "article_id", "article_name", "category_id", "category_name", "fabric_id", "fabric_name", "size_id", "size_name", "reference_id", "created_at", "updated_at", "created_by", "updated_by", "update_by_type","published_date","is_publish")->where($whereCondition)->first();
        $tecDet = TechPackDetails::select("id", "techpack_type", "techpack_details", "reference_id","seq_ord","comments","techpack_id","created_by","created_by_type","created_at","updated_by","update_by_type","updated_at","is_publish")
        ->where($whereCondition2)
        ->orderBy("seq_ord", "ASC")
        ->orderBy("created_at", "ASC")
        ->get();
        $tecDetAry = [];
        $updated_by_all= $updated_date = '';
        $i=0;
        foreach ($tecDet as $techdata) {
            $tecp = [];
            $tecp['techpackdetail_id'] = $techdata['id'];
            $tecp['techpack_type'] = $techdata['techpack_type'];
            $tecp['techpack_details'] = $techdata['techpack_details'];
            $tecp['seq_ord'] = $techdata['seq_ord'];
            $tecp['comments'] = $techdata['comments'];
            $tecp['files'] = $this->getTechPackImages($techdata['reference_id'], $techdata['techpack_type'], 'view',$techdata['id']);
            $commets_count = 1;
            // if($techdata['comments']==0)
            //     $commets_count =TechPackDetails::where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])->count();

            $tecp['comments_count'] = $commets_count;

            $created_by = $updated_by = '';
            if($techdata['created_by_type']=='Admin')
            {
                $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
            }else if($techdata['created_by_type']=='Staff')
            {
                $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                $created_by = $staff->first_name." ".$staff->last_name;
            }
            $tecp['created_by'] = $created_by;
            $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

            if($techdata['update_by_type']=='Admin')
            {
                $updated_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
            }else if($techdata['update_by_type']=='Staff')
            {
                $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                $updated_by = $staff->first_name." ".$staff->last_name;
            }
            $tecp['updated_by'] = $updated_by;
            $tecp['update_date'] =$techdata['updated_at']!='0000-00-00 00:00:00' ? date('Y-m-d H:i:s',strtotime($techdata['updated_at'])) : '';
            if($i==0){
                $updated_date = $techdata['updated_at'];
                $updated_by_all = $updated_by;
            }
            if($updated_date < $techdata['updated_at'] && $i>0){
                $updated_date = $techdata['updated_at'];
                $updated_by_all = $updated_by;
            }
            $tecDetAry[] = $tecp;
            $i++;
        }
        //dd($tecDetAry);
        $created_by = '-';
        if ($tec['staff_id'] > 0) {
            $staffDet = CommonApp::getStaffDetailsByID($tec['staff_id']);
            if (!empty($staffDet)) {
                $created_by = $staffDet['first_name'] . ' ' . $staffDet['last_name'];
            }
        } else {
            $iserDet = CommonApp::getUserDetailsById($tec['user_id']);
            if (!empty($iserDet)) {
                $created_by = $iserDet['name'];
            }
        }
        //$updated_by = '-';
        if ($tec['updated_by'] > 0 && $updated_by_all!='') {
            if (strtolower($tec['update_by_type']) == 'admin') {
                $iserDetu = CommonApp::getUserDetailsById($tec['updated_by']);
                if (!empty($iserDetu)) {
                    $updated_by_all = $iserDetu['name'];
                } else {
                    $staffDetu = CommonApp::getStaffDetailsByID($tec['updated_by']);
                    if (!empty($staffDetu)) {
                        $updated_by_all = $staffDetu['first_name'] . ' ' . $staffDetu['last_name'];
                    }
                }
            }
        }
        $data['company_id'] = $request->company_id;
        $data['workspace_id'] = $request->workspace_id;
        $data['teckpackINFO'] = $tec;
        if(isset($request->techpack_type) && $request->techpack_type=='Garmentimage')
        {
            $data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        }else if(!isset($request->techpack_type) ){
            $data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        }else{
            $data['GarmentSheet']=[];
        }
        if(isset($request->techpack_type) && $request->techpack_type=='MeasurementChart')
        {
            $data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        }else if(!isset($request->techpack_type) ){
            $data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        }else{
            $data['MeasurementChart']=[];
        }
        //$data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        //$data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        $data['teckpackINFO'] = $tec;
        $data['created_by'] = ucfirst($created_by);
        $data['updated_by'] = ucfirst($updated_by_all);
        $data['updated_date'] = $updated_date;
        $data['teckpack_details'] = $tecDetAry;
        $data['techpack_type'] = (isset($request->techpack_type) && $request->techpack_type!='')?$request->techpack_type:'';
        // $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = config('filesystems.disks.s3.url');
        // $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        // $data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        view()->share("data", $data);
        $pdf = Pdf::loadView('TeckPackPDF');
        $pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni', 'poppins']);
        $pdf->setOption("enable_php", true);
        $pdf->setOption("isHtml5ParserEnabled", true);
        // $filePath = public_path() . '/TeckPack';
        // if (!file_exists($filePath)) {
        //     File::makeDirectory($filePath, 0777, true, true);
        // }
        // $path = public_path() . '/TeckPack/' . $request->techpack_id . '.pdf';
        // $pdf->save($path);
        return $pdf->download();
    }
    // public function getTechPackImages($referenceId, $type,$download='',$techpack_details_id=0)
    // {
    //     $files = [];
    //     try {
    //         if($techpack_details_id!=0){
    //             $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
    //             ->where('reference_id', $referenceId)
    //             ->where('techpack_details_id', $techpack_details_id)
    //             ->where("techpack_type", $type)
    //             ->orderBy("convert_images", 'asc')
    //             ->orderBy("image_width", 'asc')->get();
    //         }else{
    //             $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
    //             ->where('reference_id', $referenceId)
    //             ->where("techpack_type", $type)
    //             ->orderBy("convert_images", 'asc')
    //             ->orderBy("image_width", 'asc')->get();
    //         }
    //           if (count($files) > 0) {
    //             foreach ($files as $key => $file) {
    //                 $files[$key]->org_filepath=$file->filepath;
    //                 $files[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');
    //                 $files[$key]->file_type =  pathinfo(strtolower($file->orginalfilename), PATHINFO_EXTENSION);
    //                 if($download=='view'){

    //                     if($file->convert_images!=''){

    //                         $convertFile=json_decode($file->convert_images,true);
    //                         $convImg=[];
    //                         foreach($convertFile as $cFile){
    //                         $gpath=public_path()."/TeckPack/images/".$cFile;
    //                         if(!file_exists($gpath)){
    //                             //$files[$key]->convert_images=
    //                             $convImg[]=Storage::disk('s3')->temporaryUrl($cFile, '+15 minutes');
    //                         }else{
    //                             $convImg[]=$gpath;
    //                         }
    //                         }
    //                         if(!empty($convImg)){
    //                             $files[$key]->convert_images =  $convImg;
    //                         }

    //                     }

    //                 }

    //             }
    //         }
    //     } catch (Exception $e) {
    //         //return "error: ".$e->getMessage();
    //     }

    //     return $files;
    // }
    public function getTechPackImages($referenceId, $type, $download,$techpack_details_id=0,$after_publish_edit=0,$alldata=0)
    {
        $files = [];
        try {
            if($type == 'Garmentimage' || $type == 'MeasurementChart'){
                $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
                    ->where('reference_id', $referenceId)
                    ->where("techpack_type", $type)
                    ->where('deleted', 0)
                    ->orderBy("convert_images", 'asc')
                    ->orderBy("image_width", 'asc')->get();
            }
            else if($after_publish_edit==1){
                if($alldata==0)
                {
                    $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
                    ->where('edit_reference_id', $referenceId)
                    ->where("techpack_type", $type)
                    ->where('after_publish_edit', 1)
                    ->where('deleted', 0)
                    ->orderBy("id", 'asc')->get();
                }else{
                    $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
                    ->where('edit_reference_id', $referenceId)
                    ->where("techpack_type", $type)
                    ->where('after_publish_edit', 1)
                    //->where('deleted', 0)
                    ->orWhere(function ($query) use($type,$referenceId) {
                        $query->where("techpack_type",$type)->where('all_reference_id','like','%'.$referenceId.'%')
                            ->where('after_publish_edit','1');
                            //->where('deleted', 0);
                    })
                    ->orderBy("id", 'asc')->get();
                }
            }else{
                if($techpack_details_id!=0){
                    $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
                    ->where('reference_id', $referenceId)
                    ->where('techpack_details_id', $techpack_details_id)
                    ->where('after_publish_edit', 0)
                    ->where("techpack_type", $type)
                    ->where('deleted', 0)
                    ->orderBy("convert_images", 'asc')
                    ->orderBy("image_width", 'asc')->get();
                }else{
                    $files = TechPackImages::select("id as media_id", "techpack_type", "filepath", "orginalfilename", "reference_id", "convert_images","image_width","image_height","techpack_details_id")
                    ->where('reference_id', $referenceId)
                    ->where("techpack_type", $type)
                    ->where('after_publish_edit', 0)
                    ->where('deleted', 0)
                    ->orderBy("convert_images", 'asc')
                    ->orderBy("image_width", 'asc')->get();
                }
            }

            if (count($files) > 0) {
                foreach ($files as $key => $file) {
                    $files[$key]->org_filepath = $file->filepath;
                    $files[$key]->filepath = Storage::disk('s3')->temporaryUrl($file->filepath, '+15 minutes');
                    $files[$key]->file_type =  pathinfo(strtolower($file->orginalfilename), PATHINFO_EXTENSION);
                    if ($download == 'view'|| 1) {

                        if ($file->convert_images != '') {

                            $convertFile = json_decode($file->convert_images, true);
                            $convImg = [];
                            foreach ($convertFile as $cFile) {
                                $gpath = public_path() . "/TeckPack/images/" . $cFile;
                                if (!file_exists($gpath)) {
                                    //$files[$key]->convert_images=
                                    $convImg[] = Storage::disk('s3')->temporaryUrl($cFile, '+15 minutes');
                                } else {
                                    $convImg[] = $gpath;
                                }
                            }
                            if (!empty($convImg)) {
                                $files[$key]->convert_images =  $convImg;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return $files;
    }

    /*Delete Techpack Details */
    public function deleteTeckPackDetails(Request $request)
    {
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_id' => 'required',
             ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
          ['workspace_id', '=', $request->workspace_id],
          ['company_id', '=', $request->company_id],
          ['id', '=', $request->techpack_id]
        ];
        $tec = TechPack::where($whereCondition)->first();
        if(!empty($tec)) {
            $whereConditionImg = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['filepath', '!=', null]
            ];
            $getImg=TechPackImages::where($whereConditionImg)->get();
            foreach($getImg as $imgd){
                if (!empty($imgd)) {
                    $filepath = $imgd['filepath'];
                    /* Delete AWS S3 File */
                    try {
                        Uploads::deleteS3File($filepath);
                    } catch (Exception $e) {
                    }
                    /* Delete Local File Path */
                    try {
                        $contImg=$imgd['convert_images'];
                        if($contImg){
                            $contImgCnt=json_decode($contImg,true);
                            if(!empty($contImgCnt)){
                                foreach($contImgCnt as $deleImg){
                                    $deleteImg = public_path() . '/TeckPack/images/' . $deleImg;
                                    if(file_exists($deleteImg)){
                                        unlink($deleteImg);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                    }
                }
            }
          /* Delete Teckpack Details */
            try {
                $whereConditionImgs = [
                    ['workspace_id', '=', $request->workspace_id],
                    ['company_id', '=', $request->company_id],
                    ['techpack_id', '=', $request->techpack_id]
                                ];
                TechPackImages::where($whereConditionImgs)->delete();
                TechPackDetails::where($whereConditionImgs)->delete();
                TechPack::where($whereCondition)->delete();
            } catch (Exception $e) {
            }
            $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Techpack Deleted Successfully"]);
            /* Delete Techpack Log starts */
            try{
                $ip_address = $header->header('ip-address') ?? '';
                $platform = $header->header('platform') ?? '';
                TechpackLog::techpack_delete_update_log($request,$ip_address,$platform);
            }catch(Exception $e){

            }
            /* Delete Techpack Log end */
        }else{
          $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Techpack Details Not Exists"]);

        }
        return CommonApp::apiEncrypt($res);
    }

    /*Publish */
    public function publishTeckPack(Request $request)
    {
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_id' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id', '=', $request->techpack_id]
        ];
        $tec = TechPack::select("is_publish")->where($whereCondition)->first();
        if(!empty($tec)) {
            if($tec['is_publish']=="1"){
                $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Techpack Already Published."]);
            }else{
                TechPack::where($whereCondition)->update(array("is_publish"=>"1"));
                $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Techpack Successfully Published"]);
                /* Techpack status update Log starts */
                try{
                    $ip_address = $header->header('ip-address') ?? '';
                    $platform = $header->header('platform') ?? '';
                    TechpackLog::techpack_status_update_log($request);
                }catch(Exception $e){

                }
                /* Techpack status update Log end */
            }
        }else{
            $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Techpack Details Not Exists"]);

        }
        return CommonApp::apiEncrypt($res);
    }

    /*Get Tech Pack Details */
    public function getTeckPackDetails(Request $request)
    {
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //  $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_id' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id', '=', $request->techpack_id]
        ];
        $whereCondition2 = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['techpack_id', '=', $request->techpack_id]
        ];

        $tec = TechPack::select("id as techpack_id", "po_no", "po_id", "style_no", "article_id", "article_name", "category_id", "category_name", "fabric_id", "fabric_name", "size_id", "size_name", "reference_id","is_publish as published")->where($whereCondition)->first();
        $tecDet = TechPackDetails::select("id", "techpack_type", "techpack_details", "reference_id","seq_ord","comments","techpack_id","created_by","created_by_type","created_at","updated_by","update_by_type","updated_at","is_publish","after_publish_edit","edit_reference_id")
                ->where($whereCondition2)
                ->orderBy("seq_ord", "ASC")
                ->orderBy("created_at", "ASC")->get();
        $tecDetAry = [];
        if (!empty($tecDet)) {

            foreach ($tecDet as $techdata) {
                $tecp = [];
                $tecp['techpackdetail_id'] = $techdata['id'];
                $tecp['techpack_type'] = $techdata['techpack_type'];
                $tecp['techpack_detail'] = $techdata['techpack_details'];
                $tecp['seq_ord'] = $techdata['seq_ord'];
                $tecp['comments'] = $techdata['comments'];
                $tecp['is_publish'] = $techdata['is_publish'];
                $tecp['created_by_type'] = $techdata['created_by_type'];
                $tecp['created_id'] = $techdata['created_by'];
                $tecp['after_publish_edit'] = $techdata['after_publish_edit'];

                $referenceId = $techdata['reference_id'];
                if($techdata['after_publish_edit']==1){
                    $referenceId = $techdata['edit_reference_id'];
                }

                $tecp['files'] = $this->getTechPackImages($referenceId, $techdata['techpack_type'], '',$techdata['id'],$techdata['after_publish_edit']);
                $commets_count = 1;
                $unread_count = 0;
                if($techdata['comments']==0)
                {
                    if($request->staff_id > 0)
                    {
                        // $commets_count =TechPackDetails::where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])->where('created_by_type','Staff')->where('created_by',$request->staff_id)
                        // ->orWhere(function ($query) use($techdata) {
                        //     $query->where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])
                        //         ->where('is_publish','1');
                        // })
                        // ->count();

                        $unread_count = TechPackComments::where("techpack_type",$techdata['techpack_type'])
                                ->where('techpack_id',$techdata['techpack_id'])
                                ->whereRaw("NOT FIND_IN_SET(?, techpack_comments.staff_read)",[$request->staff_id])
                                ->where('techpack_comments.created_by_type','!=','Staff')
                                ->where('techpack_comments.created_by','!=',$request->staff_id)
                                ->count();
                    }
                    else
                    {
                        // $commets_count =TechPackDetails::where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])->where('created_by_type','Admin')
                        // ->orWhere(function ($query) use($techdata) {
                        //     $query->where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])
                        //         ->where('is_publish','1');
                        // })->count();
                        $unread_count = TechPackComments::where("techpack_type",$techdata['techpack_type'])
                                ->where('techpack_id',$techdata['techpack_id'])
                                ->where('techpack_comments.admin_read','0')
                                ->where('techpack_comments.created_by_type','!=','Admin')
                                ->count();
                    }
                }
                //echo $commets_count; exit;

                $tecp['comments_count'] = $commets_count;
                $tecp['unread_count'] = $unread_count;

                $created_by = $updated_by = '';
                if($techdata['created_by_type']=='Admin')
                {
                    $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
                }else if($techdata['created_by_type']=='Staff')
                {
                    $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                    $created_by = $staff->first_name." ".$staff->last_name;
                }
                $tecp['created_by'] = $created_by;
                $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

                if($techdata['update_by_type']=='Admin')
                {
                    $updated_by = User::where('id',$techdata['updated_by'])->pluck('name')->first();
                }else if($techdata['update_by_type']=='Staff')
                {
                    $staff = Staff::where('id',$techdata['updated_by'])->select('first_name','last_name')->first();
                    $updated_by = $staff->first_name." ".$staff->last_name;
                }
                $tecp['updated_by'] = $updated_by;
                $tecp['update_date'] =$techdata['updated_at']!='0000-00-00 00:00:00' ? date('Y-m-d H:i:s',strtotime($techdata['updated_at'])) : '';

                $tecDetAry[] = $tecp;
            }
            $GarmentSheet = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', '');
            $MeasurementChart = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', '');
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => array("techpack" => $tec, "techpackdetails" => $tecDetAry, "GarmentSheet" => $GarmentSheet, "MeasurementChart" => $MeasurementChart)]);
        } else {
            $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Tech Pack Details Not Exists"]);
        }
        return CommonApp::apiEncrypt($res);
    }

    /*Add Tech Pack Comments*/
    public function addTechPackComments(Request $request)
    {
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_type' => 'required',
            'reference_id' => 'required',
            'comments' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'techpack_id'=> 'required',
            'seq_ord'=> 'required',
            'techpack_details'=> 'required',
            'is_publish'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $teckPackDetAr = [];
        $teckPackDetAr['company_id'] = $request->company_id;
        $teckPackDetAr['workspace_id'] = $request->workspace_id;
        $teckPackDetAr['user_id'] = $request->user_id;
        $teckPackDetAr['techpack_details'] = $request->techpack_details;
        $teckPackDetAr['techpack_type'] = $request->techpack_type;
        $teckPackDetAr['techpack_id'] = $request->techpack_id;
        $teckPackDetAr['seq_ord'] = $request->seq_ord;
        $teckPackDetAr['staff_id'] = $request->staff_id;
        $teckPackDetAr['comments'] = $request->comments ?? 0;
        $teckPackDetAr['is_publish'] = $request->is_publish ?? 0;
        $teckPackDetAr['reference_id'] = $request->reference_id;
        $teckPackDetAr['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
        $teckPackDetAr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
        $teckPackDetAr['created_at'] = date('Y-m-d H:i:s');
        TechPackDetails::insert($teckPackDetAr);
        $techpackDetID = DB::getPdo()->lastInsertId();
        $DataArray = array(
            "techpack_id" => $request->techpack_id,
            "techpack_details_id" => $techpackDetID
        );
        TechPackImages::where("reference_id", $request->reference_id)->where("techpack_type", $request->techpack_type)->where("comments", 1)->where("techpack_details_id", 0)->update($DataArray);

        /* Generate Techpack comments Log starts */
        try {
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            TechpackLog::generate_techpack_comments_log($request->techpack_id, $request,$ip_address,$platform);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        /* Generate Techpack comments Log end */
        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Added Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /*Get techpack comments details*/
    public function getTechPackCommentsDetails(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'techpack_details_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $tecDet = TechPackDetails::select("id", "techpack_type", "techpack_details", "reference_id","seq_ord","comments","techpack_id","is_publish")
                ->where('id',$request->techpack_details_id)
                ->orderBy("seq_ord", "ASC")
                ->orderBy("created_at", "ASC")->get();
        $tecDetAry = [];
        if (!empty($tecDet)) {

            foreach ($tecDet as $techdata) {
                $tecp = [];
                $tecp['techpackdetail_id'] = $techdata['id'];
                $tecp['techpack_type'] = $techdata['techpack_type'];
                $tecp['techpack_detail'] = $techdata['techpack_details'];
                $tecp['seq_ord'] = $techdata['seq_ord'];
                $tecp['comments'] = $techdata['comments'];
                $tecp['is_publish'] = $techdata['is_publish'];
                $tecp['files'] = $this->getTechPackImages($techdata['reference_id'], $techdata['techpack_type'], '',$techdata['id']);

                $tecDetAry[] = $tecp;
            }
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => array("techpackdetails" => $tecDetAry)]);
        } else {
            $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Tech Pack Details Not Exists"]);
        }
        return CommonApp::apiEncrypt($res);

    }
    /* Update techpack comments*/
    public static function updateTechPackCommentsDetails(Request $request){
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'techpack_details'=> 'required',
            'techpack_details_id'=> 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'is_publish'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $teckPackDetAr = [];
        $teckPackDetAr['techpack_details'] = $request->techpack_details;
        $teckPackDetAr['updated_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
        $teckPackDetAr['update_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
        $teckPackDetAr['updated_at'] = date('Y-m-d H:i:s');
        $teckPackDetAr['is_publish'] = $request->is_publish ?? 0;
        $before_value = TechPackDetails::where("id", $request->techpack_details_id)->pluck('techpack_details','is_publish')->first();
        TechPackDetails::where("id", $request->techpack_details_id)->update($teckPackDetAr);

        /* Generate Techpack comments Log starts */
        try {
            $before_values=$after_values=array();
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            $before_values['techpack_details']= $before_value;
            $after_values['techpack_details']= $request->techpack_details;
            TechpackLog::generate_techpack_comments_log($request->techpack_id, $request,$ip_address,$platform,$before_values,$after_values);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        /* Generate Techpack comments Log end */
        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Updated Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /* Upload Audio files Testing */
     public static function addAudioFile(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'audio' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->store('audio', 'public');

            // You can save the file path in the database if needed
            // Example: Audio::create(['path' => $filePath]);
            $res = json_encode(["status_code" => 200, "status" => "success", "message" => "Audio file uploaded successfully","Url" => config('app.public_url').Storage::url($filePath)]);
            return $res;

        }
        $res = json_encode(["status_code" => 401, "status" => "error", "message" => "Failed to upload the audio file."]);

        return $res;
    }

    /* Upload Audio files Techpach Comments */
    public static function addTechpackAudioFile(Request $request){
        $header = $request;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'audio' => 'required',
            'techpack_type' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'techpack_id'=> 'required',
            'translate'=>'required',
            'language'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->store('audio', 'public');
            try{
                // $result = Helper::speechToText(public_path().Storage::url($filePath));
                // $is_translate = $request->translate ?? 0;
                // $trans_arr['lang']= 'en';
                // $trans_arr['translate'] = $is_translate;
                // $text_data = '';
                // if(count($result['prediction_raw']['transcription']) > 0){
                //     foreach ($result['prediction_raw']['transcription'] as $value) {
                //         $text_data.= $value['transcription']." ";
                //         $trans_arr['lang']= $value['language']=='ja'?'jp':'en';
                //     }
                //     $translated_data = $text_data;
                //     if($is_translate){
                //         $translated_data = Helper::translate($request->techpack_details,$trans_arr);
                //     }
                // }

                $text_data = '';
                $translated_data = $text_data;
                $is_translate = 0;

                $teckPackDetAr = [];
                $teckPackDetAr['company_id'] = $request->company_id;
                $teckPackDetAr['workspace_id'] = $request->workspace_id;
                $teckPackDetAr['user_id'] = $request->user_id;
                $teckPackDetAr['comment_type'] = 'Audio';
                $teckPackDetAr['comment_data'] = $text_data;
                $teckPackDetAr['techpack_type'] = $request->techpack_type;
                $teckPackDetAr['techpack_id'] = $request->techpack_id;
                $teckPackDetAr['staff_id'] = $request->staff_id;
                $teckPackDetAr['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
                $teckPackDetAr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
                $teckPackDetAr['created_at'] = date('Y-m-d H:i:s');
                $teckPackDetAr['is_translate'] = $is_translate;
                $teckPackDetAr['translated_data'] = $translated_data;
               // $teckPackDetAr['filepath'] = config('app.public_url').Storage::url($filePath);
                $teckPackDetAr['filepath'] = config('app.public_url').'storage/'.$filePath;
                TechPackComments::insert($teckPackDetAr);

            }catch(Exception $e){
                return response()->json(["status_code"=>401,"error"=>$e]);
            }

            /* Generate Techpack comments Log starts */
            try {
                $ip_address = $header->header('Ip-Address') ?? '';
                $platform = $header->header('Platform') ?? '';
                TechpackLog::generate_techpack_comments_log($request->techpack_id, $request,$ip_address,$platform);
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
            /* Generate Techpack comments Log end */

            /* Android Push Notification Starts*/
            MobileNotification::send_techpack_push_notification($request);
            /* Android Push Notification end*/

            $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Added Successfully"]);
            return $res;

            // $res = json_encode(["status_code" => 200, "status" => "success", "message" => "Audio file uploaded successfully","Url" => config('app.public_url').Storage::url($filePath)]);
            // return $res;

        }
        $res = json_encode(["status_code" => 401, "status" => "error", "message" => "Failed to upload the audio file."]);

        return $res;
    }


    /*Add New Tech Pack Comments*/
    public function addNewTechPackComments(Request $request)
    {
        $header = $request;
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_type' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'techpack_id'=> 'required',
            'techpack_details'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $teckPackDetAr = [];
        $teckPackDetAr['company_id'] = $request->company_id;
        $teckPackDetAr['workspace_id'] = $request->workspace_id;
        $teckPackDetAr['user_id'] = $request->user_id;
        $teckPackDetAr['comment_type'] = 'Text';
        $teckPackDetAr['comment_data'] = $request->techpack_details;
        $teckPackDetAr['techpack_type'] = $request->techpack_type;
        $teckPackDetAr['techpack_id'] = $request->techpack_id;
        $teckPackDetAr['staff_id'] = $request->staff_id;
        $teckPackDetAr['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
        $teckPackDetAr['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
        $teckPackDetAr['created_at'] = date('Y-m-d H:i:s');
        TechPackComments::insert($teckPackDetAr);

        /* Generate Techpack comments Log starts */
        try {
            $ip_address = $header->header('Ip-Address') ?? '';
            $platform = $header->header('Platform') ?? '';
            TechpackLog::generate_techpack_comments_log($request->techpack_id, $request,$ip_address,$platform);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
        /* Generate Techpack comments Log end */

        /* Android Push Notification Starts*/
            MobileNotification::send_techpack_push_notification($request);
        /* Android Push Notification end*/

        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Comment Added Successfully"]);
        return CommonApp::apiEncrypt($res);
    }

    /*Get New techpack comments details*/
    public function getNewTechPackCommentsDetails(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'techpack_id'=> 'required',
            'staff_id'=> 'required',
            'user_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $whereCondition = [
            ['techpack_id', '=', $request->techpack_id],
        ];
        $techAry = [];
        if(isset($request->techpack_type) && $request->techpack_type!=''){
            $whereCondition[] = ['techpack_type', '=', $request->techpack_type];

            $tecDet = TechPackDetails::select("id", "techpack_type", "techpack_details", "reference_id","seq_ord","comments","techpack_id","is_publish","created_by_type","created_by","created_at","after_publish_edit","edit_reference_id","update_by_type","updated_by","updated_at")
                ->where('techpack_type',$request->techpack_type)
                ->where('techpack_id',$request->techpack_id)
                ->orderBy("seq_ord", "ASC")
                ->orderBy("created_at", "ASC")->get();

            if (!empty($tecDet)) {

                foreach ($tecDet as $techdata) {
                    $tecp = [];
                    $tecp['techpackdetail_id'] = $techdata['id'];
                    $tecp['techpack_type'] = $techdata['techpack_type'];
                    $tecp['techpack_detail'] = $techdata['techpack_details'];
                    $tecp['seq_ord'] = $techdata['seq_ord'];
                    $tecp['comments'] = $techdata['comments'];
                    $tecp['is_publish'] = $techdata['is_publish'];

                    $referenceId = $techdata['reference_id'];
                    if($techdata['after_publish_edit']==1){
                        $referenceId = $techdata['edit_reference_id'];
                    }

                    $tecp['files'] = $this->getTechPackImages($referenceId, $techdata['techpack_type'], '',$techdata['id'],$techdata['after_publish_edit']);
                    $created_by = '';
                    if($techdata['created_by_type']=='Admin')
                    {
                        $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
                        $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));
                    }else if($techdata['created_by_type']=='Staff')
                    {
                        $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                        $created_by = $staff->first_name." ".$staff->last_name;
                        $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));
                    }
                    if($techdata['update_by_type']=='Admin')
                    {
                        $created_by = User::where('id',$techdata['updated_by'])->pluck('name')->first();
                        $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['updated_at']));
                    }else if($techdata['update_by_type']=='Staff')
                    {
                        $staff = Staff::where('id',$techdata['updated_by'])->select('first_name','last_name')->first();
                        $created_by = $staff->first_name." ".$staff->last_name;
                        $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['updated_at']));
                    }
                    $tecp['created_by'] = $created_by;
                    $tecp['created_id'] = $techdata['created_by'];
                   // $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

                    $techAry[] = $tecp;
                }
            }
        }

        $tecDet = TechPackComments::select("id", "techpack_type", "comment_type", "comment_data","user_id","staff_id","techpack_id","created_by_type","created_at","created_by","orginalfilename","is_translate","translated_data","convert_images","filepath")
                ->where($whereCondition)
                ->orderBy("created_at", "ASC")
                ->orderBy("techpack_type", "ASC")->get();
        $tecDetAry = [];
        if (!empty($tecDet)) {

            foreach ($tecDet as $techdata) {
                $tecp = [];
                $tecp['id'] = $techdata['id'];
                $tecp['techpack_type'] = $techdata['techpack_type'];
                $tecp['comment_type'] = $techdata['comment_type'];
                $tecp['comment_data'] = $techdata['comment_type'] == 'Image' ? Storage::disk('s3')->temporaryUrl($techdata['comment_data'], '+15 minutes') : $techdata['comment_data'];
                $tecp['user_id'] = $techdata['user_id'];
                $tecp['staff_id'] = $techdata['staff_id'];
                $tecp['techpack_id'] = $techdata['techpack_id'];
                $tecp['created_by_type'] = $techdata['created_by_type'];
                $tecp['file_type'] = $techdata['comment_type'] == 'Image' ? pathinfo(strtolower($techdata['orginalfilename']), PATHINFO_EXTENSION) : '';
                $tecp['orginalfilename'] = $techdata['orginalfilename'];
                $tecp['is_translate'] = $techdata['is_translate'];
                $tecp['translated_data'] = $techdata['translated_data'];
                $tecp['filepath'] = $techdata['filepath'];
                $created_by = '';
                if($techdata['created_by_type']=='Admin')
                {
                    $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
                }else if($techdata['created_by_type']=='Staff')
                {
                    $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                    $created_by = $staff->first_name." ".$staff->last_name;
                }
                $tecp['created_by'] = $created_by;
                $tecp['created_id'] = $techdata['created_by'];
                $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));
                $convImg = [];
                if ($techdata['comment_type'] == 'Image') {
                    if ($techdata['convert_images'] != '' && $techdata['convert_images']!= NULL) {
                        $convertFile = json_decode($techdata['convert_images'], true);
                        foreach ($convertFile as $cFile) {
                            $convImg[] = Storage::disk('s3')->temporaryUrl($cFile, '+45 minutes');
                        }
                    }
                }
                $tecp['convert_images'] =  $convImg;

                $tecDetAry[] = $tecp;
            }
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => array("commentdetails" => $tecDetAry,"techpackdetails" => $techAry)]);
            /**Comments Read status Update starts */
            $techpack_type = $request->techpack_type ?? '';
            if($techpack_type!=''){
                if($request->staff_id >0){
                    TechpackComments::updateStaffReadStatus($request->techpack_id,$techpack_type,$request->staff_id);

                }else{
                    TechpackComments::updateAdminReadStatus($request->techpack_id,$techpack_type);
                }
            }
            /**Comments Read status Update End */
        } else {
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => array("commentdetails" => $tecDetAry,"techpackdetails" => $techAry)]);
        }
        return CommonApp::apiEncrypt($res);

    }

    /*Download Tech pack PDF */
    public function downloadNewTechPackPDF(Request $request)
    {
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'techpack_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $comments = [];
        $whereCondition = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['id', '=', $request->techpack_id]
        ];
        if(isset($request->techpack_type) && $request->techpack_type!=''){
            $whereCondition2 = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['techpack_type', '=', $request->techpack_type],
                ['comments', '=', 0]
               // ['is_publish', '=', 1],
            ];

            $whereCondition3 = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['techpack_type', '=', $request->techpack_type]
            ];

            $tecDet = TechPackComments::select("id", "techpack_type", "comment_type", "comment_data","user_id","staff_id","techpack_id","created_by_type","created_at","created_by","orginalfilename","convert_images")
                ->where($whereCondition3)
                ->orderBy("created_at", "ASC")
                ->orderBy("techpack_type", "ASC")->get();
            if (!empty($tecDet)) {

                foreach ($tecDet as $techdata) {
                    $tecp = [];
                    $tecp['id'] = $techdata['id'];
                    $tecp['techpack_type'] = $techdata['techpack_type'];
                    $tecp['comment_type'] = $techdata['comment_type'];
                    $tecp['comment_data'] = $techdata['comment_type'] == 'Image' ? Storage::disk('s3')->temporaryUrl($techdata['comment_data'], '+15 minutes') : $techdata['comment_data'];
                    $tecp['user_id'] = $techdata['user_id'];
                    $tecp['staff_id'] = $techdata['staff_id'];
                    $tecp['techpack_id'] = $techdata['techpack_id'];
                    $tecp['created_by_type'] = $techdata['created_by_type'];
                    $tecp['file_type'] = $techdata['comment_type'] == 'Image' ? pathinfo(strtolower($techdata['orginalfilename']), PATHINFO_EXTENSION) : '';
                    $tecp['orginalfilename'] = $techdata['orginalfilename'];
                    $created_by = '';
                    if($techdata['created_by_type']=='Admin')
                    {
                        $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
                    }else if($techdata['created_by_type']=='Staff')
                    {
                        $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                        $created_by = $staff->first_name." ".$staff->last_name;
                    }
                    $tecp['created_by'] = $created_by;
                    $tecp['created_id'] = $techdata['created_by'];
                    $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));
                    $tecp['convert_images'] =[];
                    if ($techdata['convert_images'] != '' && $techdata['convert_images'] != NULL) {

                        $convertFile = json_decode($techdata['convert_images'], true);
                        $convImg = [];
                        foreach ($convertFile as $cFile) {
                            $convImg[] = Storage::disk('s3')->temporaryUrl($cFile, '+15 minutes');
                        }
                        if (!empty($convImg)) {
                            $tecp['convert_images'] =  $convImg;
                        }
                    }

                    $comments[] = $tecp;
                }
            }

        }else{
            $whereCondition2 = [
                ['workspace_id', '=', $request->workspace_id],
                ['company_id', '=', $request->company_id],
                ['techpack_id', '=', $request->techpack_id],
                ['is_publish', '=', 1],
                ['comments', '=', 0],
            ];
        }

        // $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        // App::setlocale($dateFormatAndLanguage['language']);
        $data = [];
        $tec = TechPack::select("id as techpack_id", 'staff_id', 'user_id', "po_no", "po_id", "style_no", "article_id", "article_name", "category_id", "category_name", "fabric_id", "fabric_name", "size_id", "size_name", "reference_id", "created_at", "updated_at", "created_by", "updated_by", "update_by_type","published_date","is_publish")->where($whereCondition)->first();
        $tecDet = TechPackDetails::select("id", "techpack_type", "techpack_details", "reference_id","seq_ord","comments","techpack_id","created_by","created_by_type","created_at","updated_by","update_by_type","updated_at","is_publish","after_publish_edit","edit_reference_id")
        ->where($whereCondition2)
        ->orderBy("seq_ord", "ASC")
        ->orderBy("created_at", "ASC")
        ->get();
        $tecDetAry = [];
        $updated_by_all= $updated_date = '';
        $i=0;
        foreach ($tecDet as $techdata) {
            $tecp = [];
            $tecp['techpackdetail_id'] = $techdata['id'];
            $tecp['techpack_type'] = $techdata['techpack_type'];
            $tecp['techpack_details'] = $techdata['techpack_details'];
            $tecp['seq_ord'] = $techdata['seq_ord'];
            $tecp['comments'] = $techdata['comments'];
            $referenceId = $techdata['reference_id'];
            if($techdata['after_publish_edit']==1){
                $referenceId = $techdata['edit_reference_id'];
            }
            $tecp['files'] = $this->getTechPackImages($referenceId, $techdata['techpack_type'], 'view',$techdata['id'],$techdata['after_publish_edit']);
            $commets_count = 1;
            // if($techdata['comments']==0)
            //     $commets_count =TechPackDetails::where("techpack_type",$techdata['techpack_type'])->where('techpack_id',$techdata['techpack_id'])->count();

            $tecp['comments_count'] = $commets_count;

            $created_by = $updated_by = '';
            if($techdata['created_by_type']=='Admin')
            {
                $created_by = User::where('id',$techdata['created_by'])->pluck('name')->first();
            }else if($techdata['created_by_type']=='Staff')
            {
                $staff = Staff::where('id',$techdata['created_by'])->select('first_name','last_name')->first();
                $created_by = $staff->first_name." ".$staff->last_name;
            }
            $tecp['created_by'] = $created_by;
            $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

            if($techdata['update_by_type']=='Admin')
            {
                $updated_by = User::where('id',$techdata['updated_by'])->pluck('name')->first();
            }else if($techdata['update_by_type']=='Staff')
            {
                $staff = Staff::where('id',$techdata['updated_by'])->select('first_name','last_name')->first();
                $updated_by = $staff->first_name." ".$staff->last_name;
            }
            $tecp['updated_by'] = $updated_by;
            $tecp['update_date'] =$techdata['updated_at']!='0000-00-00 00:00:00' ? date('Y-m-d H:i:s',strtotime($techdata['updated_at'])) : '';
            if($i==0){
                $updated_date = $techdata['updated_at'];
                $updated_by_all = $updated_by;
            }
            if($updated_date < $techdata['updated_at'] && $i>0){
                $updated_date = $techdata['updated_at'];
                $updated_by_all = $updated_by;
            }
            $tecDetAry[] = $tecp;
            $i++;
        }
        //dd($tecDetAry);
        $created_by = '-';
        if ($tec['staff_id'] > 0) {
            $staffDet = CommonApp::getStaffDetailsByID($tec['staff_id']);
            if (!empty($staffDet)) {
                $created_by = $staffDet['first_name'] . ' ' . $staffDet['last_name'];
            }
        } else {
            $iserDet = CommonApp::getUserDetailsById($tec['user_id']);
            if (!empty($iserDet)) {
                $created_by = $iserDet['name'];
            }
        }
        //$updated_by = '-';
        if ($tec['updated_by'] > 0 && $updated_by_all!='') {
            if (strtolower($tec['update_by_type']) == 'admin') {
                $iserDetu = CommonApp::getUserDetailsById($tec['updated_by']);
                if (!empty($iserDetu)) {
                    $updated_by_all = $iserDetu['name'];
                } else {
                    $staffDetu = CommonApp::getStaffDetailsByID($tec['updated_by']);
                    if (!empty($staffDetu)) {
                        $updated_by_all = $staffDetu['first_name'] . ' ' . $staffDetu['last_name'];
                    }
                }
            }
        }
        $data['company_id'] = $request->company_id;
        $data['workspace_id'] = $request->workspace_id;
        $data['teckpackINFO'] = $tec;
        if(isset($request->techpack_type) && $request->techpack_type=='Garmentimage')
        {
            $data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        }else if(!isset($request->techpack_type) ){
            $data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        }else{
            $data['GarmentSheet']=[];
        }
        if(isset($request->techpack_type) && $request->techpack_type=='MeasurementChart')
        {
            $data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        }else if(!isset($request->techpack_type) ){
            $data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        }else{
            $data['MeasurementChart']=[];
        }
        //$data['GarmentSheet'] = $this->getTechPackImages($tec['reference_id'], 'Garmentimage', 'view');
        //$data['MeasurementChart'] = $this->getTechPackImages($tec['reference_id'], 'MeasurementChart', 'view');
        $data['teckpackINFO'] = $tec;
        $data['created_by'] = ucfirst($created_by);
        $data['updated_by'] = ucfirst($updated_by_all);
        $data['updated_date'] = $updated_date;
        $data['teckpack_details'] = $tecDetAry;
        $data['comments'] = $comments;
        $data['techpack_type'] = (isset($request->techpack_type) && $request->techpack_type!='')?$request->techpack_type:'';
        // $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        // dd($data);
        $data['serverURL'] = config('filesystems.disks.s3.url');
        // $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        // $data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        view()->share("data", $data);
        $pdf = Pdf::loadView('TeckPackPDFNew');
        $pdf->setPaper('A4', 'landscape');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni', 'poppins']);
        $pdf->setOption("enable_php", true);
        $pdf->setOption("isHtml5ParserEnabled", true);
        // $filePath = public_path() . '/TeckPack';
        // if (!file_exists($filePath)) {
        //     File::makeDirectory($filePath, 0777, true, true);
        // }
        // $path = public_path() . '/TeckPack/' . $request->teckpack_id . '.pdf';
        // $pdf->save($path);
        return $pdf->download();
    }

    /*update techpack comments Read Status*/
    public function UpdateCommentsReadStatus(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'techpack_id'=> 'required',
            'user_id'=> 'required',
            'staff_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $techpack_type = $request->techpack_type ?? '';
        if($request->staff_id >0){
            TechpackComments::updateStaffReadStatus($request->techpack_id,$techpack_type,$request->staff_id);

        }else{
            TechpackComments::updateAdminReadStatus($request->techpack_id,$techpack_type);
        }
        $res = json_encode(["status_code" => 200, "status" => "Success", "message" => "Status update successfully."]);

        return CommonApp::apiEncrypt($res);

    }

    /*Get techpack comments notifications*/
    public function getCommentsNotifications(Request $request){
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'user_id'=> 'required',
            'staff_id'=> 'required',
            'company_id'=> 'required',
            'workspace_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }

        $result = Techpack::getCommentsCount($request);
        $res = json_encode(["status_code" => 200, "status" => "Success", "data"=>$result]);

        return CommonApp::apiEncrypt($res);

    }

    /*Upload Tech pack File */
    public function addTechPackFile(Request $request)
    {
        $header = $request;
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'type' => 'required',
            'reference_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereConditionTechP = [
            ['workspace_id', '=', $request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['reference_id', '=', $request->reference_id]
        ];

        $teckP = TechPack::select("is_publish")->where($whereConditionTechP)->first();
        $comments = $request->comments ?? 0;
        $is_publish = $teckP['is_publish'] ?? 0;
        $edit_referenceId = $request->edit_referenceId ?? 0;
        // if (!empty($teckP)) {
        //     if ($teckP['is_publish'] == "1" && $comments==0) {
        //         $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Tech Pack Published.So,Unable to add File."]);
        //         return $res;
        //     }
        // }
        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $companyFolder = $companyDetails->aws_s3_path;
        $free_storage = CommonApp::calculateFreeStorage($companyDetails->max_storage_size,$companyDetails->storage_used);
        $storageUsed = $companyDetails->storage_used * 1024 * 1024;
        $storageToBeAdded = 0;
        $image_width='';
        $image_height='';
        if ($request->file('file')) {
                $file = $request->file('file');
                if ($file->getSize() > $free_storage && config('constant.plan_storage_size_validation') == 1) {
                return response()->json(["status_code" => 401, "status" => "failure", "error" => "Your Plan storage is full. Please contact DMS Admin"]);
            }
            if ($request->reference_id == 0) {
                $referenceId = $this->generateUniqueCode();
            } else {
                $referenceId = $request->reference_id;
            }
            $string = str_replace(' ', '_', $file->getClientOriginalName()); // Replaces all spaces with hyphens.
            $nameOfFile = preg_replace('/[^A-Za-z0-9\-.]/', '', $string); // Removes special chars.
            $fileName = time() . '_' . $nameOfFile;
            $filepath = $companyFolder . '/TechPack/' . $referenceId . '/' . $fileName;
            $upawsfilepath = $filepath;

            $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

            if (strtolower($file_ext) != "pdf" || strtolower($file_ext) != "xlsx" || strtolower($file_ext) != "xls") {
                if(strtolower($file_ext) == "jpg" || strtolower($file_ext) == "png" ||strtolower($file_ext) == "jpeg"){
                    $image = $file;
                    $fileSize = $image->getSize();
                    // Convert the file size to kilobytes (KB)
                    $fileSizeKB = $fileSize / 1024;
                    // Process only if the image size is larger than 100 KB
                    if ($fileSizeKB > 400) {
                        // Resize and optimize the image
                        $resizedImage = Image::make($image)
                            ->resize(1500, 1300, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode('jpg', 80); // Adjust the quality as needed
                        // Ensure directory exists
                        if (!file_exists(public_path('TeckPack/images'))) {
                            mkdir(public_path('TeckPack/images'), 0755, true);
                        }
                        // Save the optimized image
                        $localpath=public_path('TeckPack/images/' . $fileName);
                        $resizedImage->save($localpath);
                        $image=$localpath;
                    }
                    Uploads::orderAddtionalSpec($image, $filepath);
                }else{
                    Uploads::orderAddtionalSpec($file, $filepath);
                }
            }

            if (strtolower($file_ext) == "jpg" || strtolower($file_ext) == "jpeg" || strtolower($file_ext) == "png") {
                $imagickImgDimension = new Imagick($file->getPathname());
                 // Get width and height
                $image_width = $imagickImgDimension->getImageWidth();
                $image_height = $imagickImgDimension->getImageHeight();
                $imagickImgDimension->clear();
                $imagickImgDimension->destroy();
            }

            /*Convert PDF To Image Start */
            //  if ($request->type == 'MeasurementChart') {
            if (strtolower($file_ext) == "pdf") {
                /*Convert Multiple Page Start */
                $pdfPath = $request->file('file')->getRealPath();
                try {
                    $imagick = new Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($pdfPath);
                } catch (Exception $e) {
                    $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Unable to Read PDF File."]);
                    //return CommonApp::apiEncrypt($res);
                    return $res;
                }
                Uploads::orderAddtionalSpec($file, $filepath);

                $pages = $imagick->getNumberImages();
                $imageUrls = [];

                for ($i = 0; $i < $pages; $i++) {
                    //$imagick->setIteratorIndex($i);
                    //$imagick->setImageFormat('jpg');
                    // Initialize a new Imagick object for each page
                    $pageImagick = new Imagick();
                    $pageImagick->setResolution(150, 150);

                    // Read the specific page of the PDF
                    $pageImagick->readImage($pdfPath . "[$i]");

                    // Ensure the image background is white
                    $pageImagick->setImageBackgroundColor(new ImagickPixel('white'));

                    // Flatten the image to apply the white background
                    $pageImagick = $pageImagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
                    $pageImagick->resizeImage(800, 0, Imagick::FILTER_LANCZOS, 1);

                    // Create a new canvas with white background
                    $canvas = new Imagick();
                    $canvas->newImage($pageImagick->getImageWidth(), $pageImagick->getImageHeight(), new ImagickPixel('white'));

                    // Composite the flattened image onto the white canvas
                    $canvas->compositeImage($pageImagick, Imagick::COMPOSITE_OVER, 0, 0);

                    // Resize the image while maintaining aspect ratio
                    //$canvas->resizeImage(1024, 0, Imagick::FILTER_LANCZOS, 1);
                    $canvas->setImageCompressionQuality(70);

                    // Rotate the image if in portrait mode
                    $originalWidth = $canvas->getImageWidth();
                    $originalHeight = $canvas->getImageHeight();

                    if ($originalWidth < $originalHeight) {
                        $rotationDegrees = -90; // Rotate for portrait mode
                        $canvas->rotateImage(new ImagickPixel('white'), $rotationDegrees);
                    }

                    $imageName = time() . "_measurement_{$i}.jpg";
                    $imagePath = public_path('TeckPack/images/' . $imageName);

                    // Ensure directory exists
                    if (!file_exists(public_path('TeckPack/images'))) {
                        mkdir(public_path('TeckPack/images'), 0755, true);
                    }
                    $pageImagick->writeImage($imagePath);
                    // Uploads::orderAddtionalSpec($file,$filepath);
                    // Check if image was created
                    if (!file_exists($imagePath)) {
                        //throw new \Exception("Image for page {$i} not created");
                    } else {
                        // Set the file permissions
                        chmod($imagePath, 0775);
                    }
                    $imageUrls[] = $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;

                    /*Move To AWS S3 */
                    $awsFilePath = $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;
                    Uploads::orderAddtionalSpec($imagePath, $awsFilePath);
                    unlink($imagePath);
                    $pageImagick->clear();
                    $pageImagick->destroy();
                    $canvas->clear();
                    $canvas->destroy();
                }
                $imagick->clear();
                $imagick->destroy();
                /*Convert Multiple Page End */
            } else if (strtolower($file_ext) == "xlsx" || strtolower($file_ext) == "xls") {

                // Load the Excel file
                $filePathc = $request->file('file')->getRealPath();
                try {
                    $spreadsheet = IOFactory::load($filePathc);
                } catch (Exception $e) {
                    $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Unable to Load Excel File."]);
                    //return CommonApp::apiEncrypt($res);
                    return $res;
                }
                Uploads::orderAddtionalSpec($file, $upawsfilepath);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                $getUnquieC = date("YmdHis") . "_" . rand();
                for ($row = $highestRow; $row >= 1; $row--) {
                    $cellValues = $worksheet->rangeToArray('A' . $row . ':' . $worksheet->getHighestColumn() . $row, null, true, true, true);
                    $isEmptyRow = true;
                    foreach ($cellValues[$row] as $cellValue) {
                        if (!empty($cellValue)) {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if ($isEmptyRow) {
                        $worksheet->removeRow($row);
                    }
                }

                // Remove empty columns
                $highestColumn = $worksheet->getHighestColumn();
                $highestRow = $worksheet->getHighestRow(); // Get the updated highest row after removing empty rows
                $columnIndexes = range('A', $highestColumn);
                foreach ($columnIndexes as $columnIndex) {
                    $columnValues = $worksheet->rangeToArray($columnIndex . '1:' . $columnIndex . $highestRow, null, true, true, true);
                    $isEmptyColumn = true;
                    foreach ($columnValues as $rowValues) {
                        if (!empty($rowValues[$columnIndex])) {
                            $isEmptyColumn = false;
                            break;
                        }
                    }
                    if ($isEmptyColumn) {
                        $worksheet->removeColumn($columnIndex);
                    }
                }

                // Set column width
                $highestColumn = $worksheet->getHighestColumn();
                $columnIndexes = range('A', $highestColumn);
                foreach ($columnIndexes as $columnIndex) {
                    // Set the column width

                    $worksheet->getColumnDimension($columnIndex)->setAutoSize(true);

                    $styleArray = [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                            'indent' => 1 // Adjust the indent to create left padding effect
                        ]
                    ];
                    $worksheet->getStyle($columnIndex . '1:' . $columnIndex . $highestRow)->applyFromArray($styleArray);
                }

                // Optionally, add spaces to cell content to create padding effect
                for ($row = 1; $row <= $highestRow; $row++) {
                    foreach ($columnIndexes as $columnIndex) {
                        $cell = $worksheet->getCell($columnIndex . $row);
                        $cellValue = $cell->getValue();
                        if (!empty($cellValue)) {
                            $cell->setValue('  ' . $cellValue . '   '); // Add spaces to the beginning and end
                        }
                    }
                }

                // Optional: Recalculate column widths after setting them to auto-size
                // $worksheet->calculateColumnWidths();

                if (!file_exists(public_path('TeckPack/temp_convert'))) {
                    mkdir(public_path('TeckPack/temp_convert/'), 0755, true);
                }


                // Convert the sheet to HTML

                $htmlWriter = IOFactory::createWriter($spreadsheet, 'Html');
                $htmlFilePath = public_path("TeckPack/temp_convert/" . $getUnquieC . ".html");
                // $htmlWriter->setSheetIndex($columnIndex); // Set the current sheet index
                $htmlWriter->save($htmlFilePath);

                $html = File::get($htmlFilePath);

                // Load the HTML content into DomPDF
                $pdf = PDF::loadHTML($html);
                $pdf->setPaper('A4', 'landscape');
                $pdf->getOptions()->setIsFontSubsettingEnabled(true);
                $pdf->setOption(['defaultFont' => 'arialuni', 'poppins']);
                $pdf->setOption("enable_php", true);
                $pdf->setOption("isHtml5ParserEnabled", true);

                // Save the PDF in the public folder
                $pdf->save(public_path('TeckPack/temp_convert/' . $getUnquieC . '.pdf'));
                $pdfPath = public_path() . '/TeckPack/temp_convert/' . $getUnquieC . '.pdf';
                try{
                $imagick = new Imagick();
                $imagick->setResolution(150,150);
                $imagick->readImage($pdfPath);

                $pages = $imagick->getNumberImages();
                $imageUrls = [];

                for ($i = 0; $i < $pages; $i++) {

                    $imagick->setIteratorIndex($i);
                    $page = clone $imagick;
                    $page->setImageFormat('jpg');
                    $page = $page->flattenImages();
                    $page->resizeImage(500, 0, Imagick::FILTER_LANCZOS, 1);
                    $page->setImageCompressionQuality(80);

                    /*Start Image Rotate */
                    $originalWidth = $page->getImageWidth();
                    $originalHeight = $page->getImageHeight();
                    if ($originalWidth < $originalHeight) {

                        $width = 800; // Set your desired width
                        $height = 900; // Set your desired height
                        // Rotate the image
                        $rotationDegrees = -90; // Set your desired rotation angle
                        $page->rotateImage(new ImagickPixel(), $rotationDegrees);
                    }

                    //$imagick->resizeImage(1024, 768, Imagick::FILTER_LANCZOS, 1);

                    $imageName = time() . "_measurement_{$i}.jpg";
                    $imagePath = public_path('TeckPack/images/' . $imageName);

                    // Ensure directory exists
                    if (!file_exists(public_path('TeckPack/images'))) {
                        mkdir(public_path('TeckPack/images/'), 0755, true);
                    }
                    $imagick->writeImage($imagePath);
                    // Check if image was created
                    if (!file_exists($imagePath)) {
                        // throw new \Exception("Image for page {$i} not created");
                    }
                    $imageUrls[] =  $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;
                    /*Move To AWS S3 */
                    $awsFilePath = $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;
                    Uploads::orderAddtionalSpec($imagePath, $awsFilePath);
                    unlink($imagePath);
                    $page->clear();
                    $page->destroy();

                }
                if (file_exists($htmlFilePath)) {
                    chmod($htmlFilePath, 0775);
                    unlink($htmlFilePath);
                }
                if (file_exists($pdfPath)) {
                    chmod($pdfPath, 0775);
                    unlink($pdfPath);
                }
                $imagick->clear();
                $imagick->destroy();
                }
                catch (Exception $e) {
                    Log::info($e->getMessage());
                }
                /*End excel */
            } else if (strtolower($file_ext) == "ai" || strtolower($file_ext) == "psd" || strtolower($file_ext) == "cdr") {
                $file = $request->file('file');
                $filePathc = $file->getPathName();
                $imageUrls = [];

                /*Single Page Start */
                $outputDir = public_path('TeckPack/images');
                try {
                    $imagick = new Imagick();
                    $imagick->setResolution(150,150);
                    $imagick->readImage($filePathc . '[0]'); // Read the first page of the AI file
                    $imagick->setImageFormat('png');
                    $imagick->stripImage(); // Remove any metadata
                    $imagick = $imagick->flattenImages();
                    $imagick->resizeImage(700, 0, Imagick::FILTER_LANCZOS, 1);
                    $imagick->setImageCompressionQuality(70);


                    $originalWidth = $imagick->getImageWidth();
                    $originalHeight = $imagick->getImageHeight();
                    if ($originalWidth < $originalHeight) {
                        $width = 800; // Set your desired width
                        $height = 900; // Set your desired height
                        $rotationDegrees = -90; // Set your desired rotation angle
                        $imagick->rotateImage(new ImagickPixel(), $rotationDegrees);
                    }
                    $imageName = time() . '_' . $referenceId . '.png';
                    $imagePath = $outputDir . '/' . $imageName;
                    $imagick->writeImage($imagePath);

                    $imagick->clear();
                    $imagick->destroy();
                    $imageUrls[] =  $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;
                    /*Move To AWS S3 */
                    $awsFilePath = $companyFolder . '/TechPack/' . $referenceId . '/Convert_image/' . $imageName;
                    Uploads::orderAddtionalSpec($imagePath, $awsFilePath);
                    if (file_exists($imagePath)) {
                        chmod($imagePath, 0775);
                        unlink($imagePath);
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
                /*Single Page End */

                //Convert Multiple Page
                // try{
                //  // Convert AI file to PNG using Imagick
                //  $imagick = new Imagick();
                //  $imagick->readImage($filePath);

                //  foreach ($imagick as $index => $page) {
                //      $page->setImageFormat('png');
                //      $originalWidth = $imagick->getImageWidth();
                //      $originalHeight = $imagick->getImageHeight();
                //      if ($originalWidth < $originalHeight) {
                //         $width = 800; // Set your desired width
                //         $height = 900; // Set your desired height
                //         $rotationDegrees = -90; // Set your desired rotation angle
                //         $imagick->rotateImage(new ImagickPixel(), $rotationDegrees);
                //      }
                //      $imageName=time().'_'.$referenceId.'.png';
                //      $imageUrls[] = $imageName;
                //      $outputPath = $outputDir . '/' .$imageName;
                //      $page->writeImage($outputPath);
                //  }

                //  $imagick->clear();
                //  $imagick->destroy();
                // } catch (Exception $e) {

                // }
                $imagick->clear();
                $imagick->destroy();
            }
            //  }
            /*Convert PDF To Image End */
            $old_design = $request->old_design ?? 0;
            $filedata['techpack_id'] = $request->techpack_id ?? 0;
            $filedata['techpack_type'] = $request->type;
            $filedata['company_id'] = $request->company_id ?? 0;
            $filedata['workspace_id'] = $request->workspace_id ?? 0;
            $filedata['filename'] = $fileName;
            $filedata['orginalfilename'] = $file->getClientOriginalName();
            $filedata['image_width'] = $image_width;
            $filedata['image_height'] = $image_height;
            $filedata['filesize'] = $file->getSize();
            $storageToBeAdded += $file->getSize();
            if (!empty($imageUrls)) {
                $filedata['convert_images'] = json_encode($imageUrls, true);
            }
            $filedata['created_at'] = date('Y-m-d H:i:s');
            $filedata['user_id'] = $request->user_id ?? 0;
            $filedata['staff_id'] = $request->staff_id ?? 0;
            $filedata['created_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
            $filedata['created_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
            $comments = $request->comments ?? 0;
            if($comments==0 || $old_design==1){
                $filedata['filepath'] = $filepath;
                $filedata['reference_id'] = $referenceId;
                $filedata['comments'] = $request->comments ?? 0;
                $filedata['techpack_details_id'] = $request->techpack_details_id ?? 0;
            }else{
                $filedata['comment_data'] = $filepath;
                $filedata['comment_type'] = 'Image';
            }

            if($is_publish=='1' && $comments==0){
                $filedata['edit_reference_id'] = $edit_referenceId;
                $filedata['after_publish_edit'] = 1;

                $techpack_details_id = $request->techpack_details_id ?? 0;
                if($techpack_details_id!=0){
                    $edit_reference_id = TechPackDetails::where('id',$techpack_details_id)->value('edit_reference_id');
                    if($edit_reference_id!=0){
                        $whereCondition1 = [
                            ['edit_reference_id', '=', $edit_reference_id],
                            ['techpack_type', '=', $request->type],
                            ['deleted', '=', 0],
                        ];
                        $DataArray = array(
                            "after_publish_edit" => 1,
                            "edit_reference_id" => $edit_referenceId,
                            'all_reference_id' => DB::raw("CONCAT(all_reference_id, ',', '$edit_reference_id')")
                        );
                        TechPackImages::where($whereCondition1)->update($DataArray);
                    }else{
                        $whereCondition1 = [
                            ['reference_id', '=', $request->reference_id],
                            ['techpack_id', '=', $request->techpack_id],
                            ['techpack_type', '=', $request->type],
                            ['deleted', '=', 0],
                        ];
                        $DataArray = array(
                            "after_publish_edit" => 1,
                            "edit_reference_id" => $edit_referenceId,
                            'all_reference_id' => DB::raw("CONCAT(all_reference_id, ',', '$request->reference_id')")
                        );
                        TechPackImages::where($whereCondition1)->update($DataArray);
                    }
                }
            }

            try {
                if($comments==0 || $old_design==1){
                    TechPackImages::insert($filedata);
                }
                else{
                    TechPackComments::insert($filedata);
                }
                //$lastID = DB::getPdo()->lastInsertId();
                $companyDetails->storage_used = ($storageUsed + (int)$storageToBeAdded) / (1024 * 1024);
                $companyDetails->save();

                /* Techpack file Add Log starts */
                try {
                    if (isset($request->upload_type) && $request->upload_type == 'edit') {
                        $ip_address = $header->header('Ip-Address') ?? '';
                        $platform = $header->header('Platform') ?? '';
                        TechpackLog::techpack_add_media_log($referenceId, $request, $filedata, $ip_address, $platform);

                        if($comments==0){
                            $whereCondition2['techpack_id']= $request->techpack_id;
                            $whereCondition2['techpack_type']= $request->type;
                            $teckPackDetAr['updated_at'] = date('Y-m-d H:i:s');
                            $teckPackDetAr['updated_by'] = $request->staff_id > 0 ? $request->staff_id : $request->user_id;
                            $teckPackDetAr['update_by_type'] = $request->staff_id > 0 ? "Staff" : "Admin";
                            TechPackDetails::where($whereCondition2)->update($teckPackDetAr);
                        }

                    }
                } catch (Exception $e) {
                }
                /* Techpack file Add Log end */
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
            $after_edit = 0;
            if($edit_referenceId!=0 && $request->type!='Garmentimage' && $request->type!='MeasurementChart'){
                $referenceId = $edit_referenceId;
                $after_edit = 1;
            }

            $files = $this->getTechPackImages($referenceId, $request->type, '',$request->techpack_details_id??0,$after_edit);
            return response()->json(["status_code" => 200, 'status' => "success", "message" => "Files Added Successfully", "reference_id" => $referenceId, "type" => $request->type, "files" => $files], 200);
        }
    }

    /*Edit Tech Pack info after Publish */
    public function getEditTechpackInfo(Request $request)
    {
        $request = CommonApp::apiDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            //  $validator = Validator::make($request->all(), [
            'teckpack_id' => 'required',
            'techpack_type' => 'required',
            'refrence_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["status_code" => 401, "error" => $validator->errors()]);
        }
        $whereCondition = [
            ['techpack_id', '=', $request->teckpack_id],
            ['techpack_type', '=', $request->techpack_type],
        ];

        $tecDet = TechPackEditDetails::select()
                ->where($whereCondition)
                ->orderBy("created_at", "ASC")->get();
               // dd($tecDet);
        $tecDetAry = [];
        $i=0;
        $refrence_id = $request->refrence_id;
        if (!empty($tecDet)) {
            foreach ($tecDet as $techdata) {
                $tecp = [];
                $tecp['techpack_type'] = $techdata['techpack_type'];
                $tecp['techpack_detail'] = $techdata['techpack_details'];
                $tecp['files'] = $this->getTechPackImages($refrence_id, $techdata['techpack_type'], '',0,1,1);
                $created_by = '';
                if($techdata['staff_id']==0)
                {
                    $created_by = User::where('id',$techdata['user_id'])->pluck('name')->first();
                }else if($techdata['staff_id']>0)
                {
                    $staff = Staff::where('id',$techdata['staff_id'])->select('first_name','last_name')->first();
                    $created_by = $staff->first_name." ".$staff->last_name;
                }
                $tecp['created_by'] = $created_by;
                $tecp['create_date'] = date('Y-m-d H:i:s',strtotime($techdata['created_at']));

                $tecDetAry[] = $tecp;
                $i++;
                $refrence_id = $techdata['reference_id'];
                $after_edit = 1;
            }
            $res = json_encode(["status_code" => 200, "status" => "Success", "data" => $tecDetAry]);
        } else {
            $res = json_encode(["status_code" => 201, "status" => "Failed", "message" => "Tech Pack Details Not Exists"]);
        }
        return CommonApp::apiEncrypt($res);
    }


}
