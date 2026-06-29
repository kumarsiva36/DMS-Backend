<?php

namespace App\Http\Controllers;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Common\Uploads;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
ini_set('memory_limit',-1);
class PdfChart extends Controller
{
    public static function download_pdf_chart(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereCondition =[
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['id','=',$request->orderNo]
        ];
        $orders = Order::where($whereCondition)->get();
        $dateFormatAndLanguage = CommonApp::getDateAndLanguage($request);
        App::setlocale($dateFormatAndLanguage['language']);


        $chart = '{"type": "doughnut","data": {"datasets": [{"label": "foo","data": [90, 10],"backgroundColor": ["rgba(4,170,109, 0.5)","rgba(0, 0, 0, 0.1)"],"textcolor":["#000000","#000000"],"borderWidth": 0,}]},"options": {"rotation": Math.PI,"circumference": Math.PI,"cutoutPercentage": 75,"plugins": {"datalabels": { "display": false },"doughnutlabel": {"labels": [{"text": "\nEmail Open Rate","color": "#000","font": {"size": "25"},},{"text": "\n90%","font": {"size": "40"},},]}}}}';

        // return view('pdfchart', [
        //     'chart' => urlencode($chart),
        // ]);
        $responses= $orders;
       // $data['filter_type']=$request->filter_type??[];
        $data['responses']=$responses;
        $data['user_info']=array();
        $data['dateFormat']=$dateFormatAndLanguage['dateFormat'];
        $data['serverURL'] = config('filesystems.disks.s3.url');
        $data['useLogo'] = $dateFormatAndLanguage['useLogo'];
        $data['userLogo'] = $dateFormatAndLanguage['userLogo'];
        $data['chart'] = urlencode($chart);

        $data['chart_url'] = "https://quickchart.io/chart?width=200&height=100&c=".urlencode($chart);
        $data['imageData'] = base64_encode(file_get_contents($data['chart_url']));

        //echo   "https://quickchart.io/chart?c=".$data['chart'] ; exit;
        if(count($responses)>0 || 1){
            view()->share("data",$data);
            $pdf = Pdf::loadView('pdfchart');
            $pdf->setPaper('A4', 'landscape');
            $pdf->getOptions()->setIsFontSubsettingEnabled(true);
            $pdf->setOption(['defaultFont' => 'arialuni','poppins']);
            $pdf->setOption("enable_php", true);
            //$pdf->setOption("enable_remote", true);
            // $path = public_path() . '/OrderPO/chart.pdf';
            // $pdf->save($path);
            return $pdf->download();
        }
    }



}
