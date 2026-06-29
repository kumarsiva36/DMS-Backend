<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeZoneFormat;
use App\Common\CommonApp;

class TimeZoneFormatSettings extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listOfTimeZoneFormat = TimeZoneFormat::select('id','name','timezone')->where('status','1')->get();

        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$listOfTimeZoneFormat]);
        return CommonApp::webEncrypt($res);
    }
}
