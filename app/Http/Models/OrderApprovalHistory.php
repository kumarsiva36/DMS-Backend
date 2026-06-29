<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderBOM;
use App\Models\Staff;
use App\Models\User;
use App\Common\CommonApp;


class OrderApprovalHistory extends Model
{
    use HasFactory;
    protected $table = 'order_bom_approval_log';
    public static function addBOMApprovalLogHistory($request){

        $whereConditions=[
           // ['company_id','=',$request->company_id],
            //['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['id','=',$request->order_bom_id]
        ];
        $getBom=OrderBOM::select('is_approval')->where($whereConditions)->first();
        $isApproval_upd='';
        if(!empty($getBom)){
          $isApproval=$getBom['is_approval'];
        if($isApproval==1){
          $res = json_encode(["status_code"=>401,"error"=>"Order BOM Already Approved"]);
          return CommonApp::webEncrypt($res);
          }
        
          if($isApproval==0){
            $isApproval_txt="Approval";
            $isApproval_upd=1;
          }else if($isApproval==3){
            $isApproval_txt="Re-Approval";
            $isApproval_upd=1;
          }
          if($isApproval_upd>0){
          try{
          OrderBOM::select('is_approval')->where($whereConditions)->update(['is_approval' => $isApproval_upd]);
          if($request->staff_id>0){
            $getStaff=Staff::select("first_name","last_name")->where("id",$request->staff_id)->first();
            if($getStaff['last_name']!=''){
            $appName=$getStaff['first_name'].' '.$getStaff['last_name'];
            }else{
                $appName=$getStaff['first_name'];
            }
          }else{
            $getStaff=User::select("name")->where("id",$request->user_id)->first();
         
            $appName=$getStaff['name'];
           
          }
          $approvalLogArr=[];
          $approvalLogArr['company_id'] = $request->company_id;
          $approvalLogArr['workspace_id'] = $request->workspace_id;
          $approvalLogArr['user_id'] = $request->user_id;
          $approvalLogArr['staff_id'] =  $request->staff_id??0;
          $approvalLogArr['order_id'] =  $request->order_id??0;
          $approvalLogArr['order_bom_id'] =  $request->order_bom_id??0;
          $approvalLogArr['approval_date'] =  date("Y-m-d H:i:s");
          $approvalLogArr['approval_type'] =  $isApproval_txt;
          $approvalLogArr['approved_by'] =  $appName;
          $approvalLogArr['comments'] =  $request->comments??null;
          OrderApprovalHistory::insert($approvalLogArr);
          }catch(Exception $e){
           $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
           return CommonApp::webEncrypt($res);
         }
    }
     $res = json_encode(["status_code"=>200,"status" =>"Success","message"=>"BOM Approved Successfully"]);
    return CommonApp::webEncrypt($res);
        }else{
            $res = json_encode(["status_code"=>401,"error"=>"Order BOM Not Found"]);
            return CommonApp::webEncrypt($res);
        }

    }

    
    public static function viewBOMApprovalLogHistory($request){

        $whereConditions=[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['order_id','=',$request->order_id],
            ['order_bom_id','=',$request->order_bom_id]
        ];
        $data=OrderApprovalHistory::where($whereConditions)->get();
        $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }
}
