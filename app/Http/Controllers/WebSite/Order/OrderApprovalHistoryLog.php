<?php

namespace App\Http\Controllers\Website\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderApprovalHistory;
use App\Models\OrderBOM;
use App\Common\CommonApp;

class OrderApprovalHistoryLog extends Controller
{
       /* Add New Order BOM Approval History*/
       public static function addBOMApprovalLog(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'order_bom_id' => 'required',
           
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
       
        return OrderApprovalHistory::addBOMApprovalLogHistory($request);
      
    }
    
       /* View Order BOM Approval History*/
       public static function viewBOMApprovalLog(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'order_id' => 'required',
            'order_bom_id' => 'required',
           
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
       
        return OrderApprovalHistory::viewBOMApprovalLogHistory($request);
      
    }
}
