<?php

namespace App\Models;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
use App\Jobs\staffOrderInviteJob;
use App\Jobs\OrderInfoPDFJob;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\WebSite\Common\MobileNotification;

class OrderContacts extends Model
{
    use HasFactory;

    protected $table = 'order_contacts';

    public static function addContact($request){

        $companyDetails = CommonApp::getCompanyDetailsbyID($request->company_id);
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $aldreadyExists = OrderContacts::where($whereConditions)->get();
        if(!empty($aldreadyExists)){
            OrderContacts::where($whereConditions)->delete();
        }


        DB::beginTransaction();
        try{
            $orderNo = Order::where('id',$request->order_id)->where('company_id',$request->company_id)->where('workspace_id',$request->workspace_id)
                        ->select('order_no','style_no')->first();
            //$workspaceName = (Workspace::where('id',$request->workspace_id)->where('company_id',$request->company_id)->first())->name;
            $orderId=$request->order_id;
            $orderContactsArr = [];
            $orderContactsArr['user_id']= $companyDetails->user_id;
            $orderContactsArr['company_id']= $request->company_id;
            $orderContactsArr['workspace_id']= $request->workspace_id;
            $orderContactsArr['order_id']= $orderId;
            foreach ($request->contacts as $contact){
                $orderContactsArr['staff_id']= $contact['staff_id'];
                $orderContactsArr['created_at']=date('Y-m-d H:i:s');
                $orderContactsArr['updated_at']=date('Y-m-d H:i:s');
                OrderContacts::insert($orderContactsArr);
                /* For Contact inivite mail for the order*/
                // $language = GetUserLanguage::getLanguageOfUserWithId($request->company_id,$request->workspace_id,"Staff",$contact['staff_id']);
                // $details=[];
                // $staff = Staff::where('id',$contact['staff_id'])->first();
                // $details['to']=$staff->email;
                // $details['userName']=$staff->first_name." ".$staff->last_name;
                // $details['workspaceName'] = $workspaceName;
                // $details['orderNo'] = $orderNo->order_no;
                // $details['language'] = $language;
                // $details['pdf_path'] =config('app.public_url').'OrderInfo/'.$request->order_id.'.pdf';
                // staffOrderInviteJob::dispatch($details);
                $ord_arr['order_no']=$orderNo->order_no;
                $ord_arr['style_no']=$orderNo->style_no;
                $ord_arr['order_id']=$orderId;
                $ord_arr['company_id']=$request->company_id;
                $ord_arr['workspace_id']=$request->workspace_id;
                $ord_arr['type']='order';
                $staff_arr[0]['staff_id'] = $contact['staff_id'];
                MobileNotification::send_push_notification($ord_arr,array(),$staff_arr,'staff','OrderInvite');
            }
            /*Update Order Step Status*/
            $addOrderArr=[];
            $addOrderArr['step_level'] = '3';
            Order::where('id',$orderId)->update($addOrderArr);
        }catch(Exception $e){
            DB::rollback();
            throw new InvalidArgumentException($e->getMessage());
        }
        DB::commit();
          /*Order basic info pdf creation starts*/
       OrderInfoPDFJob::dispatch($request);
        /*Order basic info pdf creation end*/
    }

    public static function getContact($request){
        $whereConditions =[
            ['order_contacts.workspace_id','=',$request->workspace_id],
            ['order_contacts.company_id', '=', $request->company_id],
            ['order_contacts.order_id','=',$request->order_id]
        ];

        $skuDetails = OrderContacts::where($whereConditions)
                        ->join('staff','staff.id','order_contacts.staff_id')
                        ->select('order_contacts.staff_id', 'staff.first_name', 'staff.last_name')
                        ->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
    		$arr[$i]['staff_id']=$value->staff_id;

    		$arr[$i]['staff_name']=$value->first_name.' '.$value->last_name;
    		$i++;
		}

        return $arr;
    }

    public static function getContacts($request){
        $whereConditions =[
            ['workspace_id','=',$request->workspace_id],
            ['company_id', '=', $request->company_id],
            ['order_id','=',$request->order_id]
        ];

        $skuDetails = OrderContacts::where($whereConditions)
                        ->select('staff_id')
                        ->get();
		$arr=array();$i=0;
    	foreach ($skuDetails as $value) {
    		$arr[$i]=$value->staff_id;
    		$i++;
		}

        return $arr;
    }

    public static function create_orderinfo_pdf($request){
        $responses= Order::where('orders.id',$request->order_id)
                    //->leftjoin('order_sku','order_sku.order_id','orders.id')
                    //->leftjoin('color','color.id','order_sku.sku_color_id')
                    //->leftjoin('size','size.id','order_sku.sku_size_id')
                    ->leftjoin('order_buyer','order_buyer.id','orders.buyer_id')
                    ->leftjoin('order_pcu','order_pcu.id','orders.pcu_id')
                    ->leftjoin('order_factory','order_factory.id','orders.factory_id')
                    ->leftjoin('fabric_type','fabric_type.id','orders.fabric_id')
                    ->leftjoin('order_category','order_category.id','orders.category_id')
                    ->leftjoin('order_article_name','order_article_name.id','orders.article_id')
                    ->leftjoin('income_terms','income_terms.id','orders.income_terms')
                    ->leftjoin('multiple_delivery_dates','multiple_delivery_dates.order_id','orders.id')
                    ->leftjoin('order_units','order_units.id','orders.units')
                    ->select('orders.id','orders.user_id','orders.staff_id','orders.order_no','orders.style_no','orders.inquiry_date','orders.order_price',
                    'orders.total_quantity','orders.no_of_deliverys','orders.tolerance_volume','orders.tolerance_perc','orders.currency_type',
                    'orders.is_tolerance_req','orders.delivery_date','order_buyer.name as buyer','order_units.name as order_units',
                    'order_pcu.name as pcu','order_factory.name as factory','fabric_type.name as fabric','order_category.name as category',
                    'order_article_name.name as article','orders.created_at','income_terms.name as income_terms','multiple_delivery_dates.delivery_date as del_date')
                    ->get();

        $sku = OrderSku::order_sku($request);
        $colors = [];
        $sizes = [];
        if(!empty($sku)){
            foreach( $sku as $s ){
                $colors[] = array("id"=>$s['color_id'],"name"=>$s['color']);
                $sizes[] = array("id"=>$s['size_id'],"name"=>$s['size']);
            }
        }

        $request->user_id  = $responses[0]->user_id;
        $request->staff_id = $responses[0]->staff_id;

        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        $data['responses']=$responses;
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        $data['userLogo'] =$dateFormatAndLanguage['useLogo'] !=0 ?  Storage::disk('s3')->temporaryUrl($dateFormatAndLanguage['userLogo'], '+15 minutes') : "";

       // $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['sku'] = $sku;
        $data['sizes'] = array_unique($sizes,SORT_REGULAR);
        $data['colors'] = array_unique($colors,SORT_REGULAR);

        $folderPath = public_path() . '/OrderInfo';
        if (!file_exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }

        if(count($responses)>0){
            view()->share("datas",$data);
            $pdf = Pdf::loadView('OrderInfo');
            $pdf->setPaper('A4', 'portrait');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            $pdf->setOption("enable_php", true);
            $path = public_path() . '/OrderInfo/order_info_'.$request->order_id.'.pdf';
            $pdf->save($path);
            //return $pdf->download();
        }
    }
}
