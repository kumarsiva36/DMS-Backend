<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Common\GetUserLanguage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Models\Buyer;
use App\Models\Color;
use App\Models\Factory;
use App\Models\Order;
use App\Models\PCU;
use App\Models\Size;
use App\Models\UpdateSkuQuantity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Common\CommonApp;
use Illuminate\Support\Facades\Storage;

class SKUDailyProdReportDownload extends Controller
{
    /**
     * Handle the incoming request.
     * SKU Daily Report download
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());

        $sizeses   = isset($request->sizes)?$request->sizes:[];
        $colores   = isset($request->colors)?$request->colors:[];

        $whereCondition =[
            ['company_id',"=",$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['step_level','=','6'],
            ['id','=',$request->order_id]
        ];

        if(isset($request->factory_id) && $request->factory_id>0){
            $whereCondition[]=['factory_id','=',$request->factory_id];
        }
        if(isset($request->buyer_id) && $request->buyer_id>0){
            $whereCondition[]=['buyer_id','=',$request->buyer_id];
        }
        if(isset($request->pcu_id) && $request->pcu_id>0){
            $whereCondition[]=['pcu_id','=',$request->pcu_id];
        }
        $whereCondition[]=['status','=',"1"];

        $orders = Order::where($whereCondition)->first();

        /* Get User/staff language and Dateformat */
        if(isset($request->user_id) && $request->user_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['id','=',$request->user_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("User",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"User",$request->user_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        else if(isset($request->staff_id) && $request->staff_id>0){
            $whereConditionToSend=[
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$request->staff_id]
            ];
            // $dateFormat=GetUserSettings::getPeopleDateFormat("Staff",$whereConditionToSend);
            // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$request->staff_id);
            $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
            $dateFormat = $dateFormatAndLanguage['dateFormat'];
            $language = $dateFormatAndLanguage['language'];
        }
        App::setlocale($language);

        $whereCondition1 =[
            ['update_sku_quantities.company_id',"=",$request->company_id],
            ['update_sku_quantities.workspace_id','=',$request->workspace_id],
            ['update_sku_quantities.order_id','=',$request->order_id]
        ];
        // if($request->has("production_type")){
        //     $whereCondition1[]=["type_of_production",$request->production_type];
        // }

        $dailyUpdatesArr=$dailyUpdate=[];
        /* Advanced Filter Data for View */
        $advFilterArr=[];
        if(isset($request->start_date) && isset($request->end_date)){
            $advFilterArr['startDate']=date($dateFormat,strtotime($request->start_date));
            $advFilterArr['endDate']=date($dateFormat,strtotime($request->end_date));
        }
        if(isset($request->production_type)){
            $advFilterArr['prodType']= $request->production_type;
        }
        if(isset($request->colors)){
            $colors = Color::whereIn('id', $request->colors)->select('name')->get();
            foreach($colors as $color){
                $advFilterArr['color'][]= $color->name;
            }
        }
        if(isset($request->sizes)){
            $sizes = Size::whereIn('id', $request->sizes)->select('name')->get();
            foreach($sizes as $size){
                $advFilterArr['size'][]= $size->name;
            }
        }

        $dailyUpdatesArr['advFilter']=$advFilterArr;
        /* Advanced Filter View Ends */
        $startDate= $orders->cutting_start_date;
        $endDate= $orders->packing_end_date;

        if($orders->factory_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->factory_id]
            ];
            $dailyUpdatesArr['factory'] = ($this->getDetails($forType,"Factory"))->name;
        }
        if($orders->pcu_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->pcu_id]
            ];
            $dailyUpdatesArr['pcu'] = ($this->getDetails($forType,"PCU"))->name;
        }
        if($orders->buyer_id != NULL){
            $forType = [
                ['company_id','=',$request->company_id],
                ['workspace_id','=',$request->workspace_id],
                ['id','=',$orders->buyer_id]
            ];
            $dailyUpdatesArr['buyer'] =  ($this->getDetails($forType,"Buyer"))->name;
        }

        if(isset($request->start_date) && isset($request->end_date)){
            if(!empty($size) || !empty($colores)){
                if(!empty($size) && !empty($colores)){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->whereIn('update_sku_quantities.size_id',$sizeses)
                        ->whereIn('update_sku_quantities.color_id',$colores)
                        ->leftjoin('color','color.id','update_sku_quantities.color_id')
                        ->leftjoin('size','size.id','update_sku_quantities.size_id')
                        ->select('update_sku_quantities.type_of_production',"updated_quantity",
                            'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                        ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                }
                else if(!empty($size)){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->whereIn('update_sku_quantities.size_id',$sizeses)
                        ->leftjoin('color','color.id','update_sku_quantities.color_id')
                        ->leftjoin('size','size.id','update_sku_quantities.size_id')
                        ->select('update_sku_quantities.type_of_production',"updated_quantity",
                            'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                        ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                }
                else if(!empty($colores)){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                        ->whereIn("update_sku_quantities.color_id",$colores)
                        ->leftjoin('color','color.id','update_sku_quantities.color_id')
                        ->leftjoin('size','size.id','update_sku_quantities.size_id')
                        ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                            'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                        ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                }
            }
            else{
                $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                ->whereBetween('update_sku_quantities.sku_date',[date("Y-m-d",strtotime($request->start_date)),date("Y-m-d",strtotime($request->end_date))])
                ->leftjoin('color','color.id','update_sku_quantities.color_id')
                ->leftjoin('size','size.id','update_sku_quantities.size_id')
                ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                    'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                ->orderBy('update_sku_quantities.sku_date','ASC')->get();
            }
        }else{
            if(!empty($size) || !empty($colores)){
                    if(!empty($size) && !empty($colores)){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereIn('update_sku_quantities.size_id',$sizeses)
                        ->whereIn('update_sku_quantities.color_id',$colores)
                        ->leftjoin('color','color.id','update_sku_quantities.color_id')
                        ->leftjoin('size','size.id','update_sku_quantities.size_id')
                        ->select('update_sku_quantities.type_of_production','update_sku_quantities.updated_quantity',
                            'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                        // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                        ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                    }
                    else if(!empty($size)){
                        $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                        ->whereIn('update_sku_quantities.size_id',$sizeses)
                        ->leftjoin('color','color.id','update_sku_quantities.color_id')
                        ->leftjoin('size','size.id','update_sku_quantities.size_id')
                        ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                            'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                        ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                    }
                    else if(!empty($colores)){
                            $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                            ->whereIn('update_sku_quantities.color_id',$colores)
                            ->leftjoin('color','color.id','update_sku_quantities.color_id')
                            ->leftjoin('size','size.id','update_sku_quantities.size_id')
                            ->select('update_sku_quantities.type_of_production',"update_sku_quantities.updated_quantity",
                                'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                            // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                            ->orderBy('update_sku_quantities.sku_date','ASC')->get();
                    }
            }
            else{
                $dailyUpdates = UpdateSkuQuantity::where($whereCondition1)
                ->leftjoin('color','color.id','update_sku_quantities.color_id')
                ->leftjoin('size','size.id','update_sku_quantities.size_id')
                ->select('update_sku_quantities.type_of_production',"updated_quantity",
                    'update_sku_quantities.sku_date','update_sku_quantities.target_value','color.name as colorName','size.name as sizeName')
                // ->groupBy('update_sku_quantities.type_of_production')->groupBy('update_sku_quantities.sku_date')
                // ->groupBy('update_sku_quantities.color_id')->groupBy('update_sku_quantities.size_id')
                ->orderBy('update_sku_quantities.sku_date','ASC')->get();
            }
        }
        $i=0;
        $dailyUpdatesArr['orderNo']=$orders->order_no;
        $dailyUpdatesArr['styleNo']=$orders->style_no;
        $dailyUpdatesArr['startDate']=date($dateFormat,strtotime($startDate));
        $dailyUpdatesArr['endDate']=date($dateFormat,strtotime($endDate));
        $dailyUpdatesArr['dateFormat']=$dateFormat;
        $dailyUpdatesArr['serverURL'] = config('filesystems.disks.s3.url');
        $dailyUpdatesArr['useLogo'] = $dateFormatAndLanguage['useLogo'];
        //$dailyUpdatesArr['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $dailyUpdatesArr['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";
        foreach($dailyUpdates as $updates){
            if((int)$updates->updated_quantity>0){
                $updatesArr=[];
                $updatesArr['sku_date']=date($dateFormat,strtotime($updates->sku_date));
                $updatesArr[$updates->type_of_production."_actual"]=(int)$updates->updated_quantity;
                $updatesArr[$updates->type_of_production."_target"]=(int)$updates->target_value;
                $updatesArr[$updates->type_of_production]
                [$updates->colorName." - ".$updates->sizeName]=(int)$updates->updated_quantity;
                $updatesArr['total_qty']=(int)$orders->total_quantity;
                /* To place the prod datas in their respective dates */
                $res=array_search(date($dateFormat,strtotime($updates->sku_date)), array_column($dailyUpdate, 'sku_date')) ?? -1;

                if($res>=0 && strlen($res)>0){
                    $dailyUpdate[$res][$updates->type_of_production."_actual"]=(int)$updates->updated_quantity ;
                    $dailyUpdate[$res][$updates->type_of_production."_target"]=(int)$updates->target_value;
                    $dailyUpdate[$res][$updates->type_of_production]
                    [$updates->colorName." - ".$updates->sizeName]=(int)$updates->updated_quantity;
                }else{
                    $dailyUpdate[$i]=$updatesArr;
                    $i++;
                }
            }
        }

        $dailyUpdatesArr['prodData']=$dailyUpdate;
        // dd($dailyUpdatesArr);
        // echo '<pre>'; print_r($dailyUpdatesArr); exit;
        view()->share("dailyUpdates",$dailyUpdatesArr);
        // return view("DayByDayReportPDF",["dailyUpdates"=>$dailyUpdatesArr]);
        $pdf = Pdf::loadView('DailySKUReportPDF');
        // $path = public_path() . '/PendingTask/' .$request->orderNo.date('d-m-Y').'.pdf';
        // $pdf->save($path);
        // return response()->download($path);
        // $pdf->setPaper('A4', 'portrait');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));
        return $pdf->download();
    }
     /* To get the Factory/Buyer/PCU details */
     public function getDetails($data, $type){
        if($type === "Factory"){
            $name = Factory::where($data)->select('name')->first();
        }
        if($type === "PCU"){
            $name = PCU::where($data)->select('name')->first();
        }
        if($type === "Buyer"){
            $name = Buyer::where($data)->select('name')->first();
        }
        return $name;
    }
}
