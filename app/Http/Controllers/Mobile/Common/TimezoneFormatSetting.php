<?php

namespace App\Http\Controllers\Mobile\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeZoneFormat;

class TimezoneFormatSetting extends Controller
{
    /*  Get the Time zones */
    public function index(Request $request)
    {
        $listOfTimeZoneFormat = TimeZoneFormat::select('id','name','timezone')->where('status','1')->get();

        return response()->json(["status_code"=>200,"status" =>"Success","data"=>$listOfTimeZoneFormat]);
    }
}
