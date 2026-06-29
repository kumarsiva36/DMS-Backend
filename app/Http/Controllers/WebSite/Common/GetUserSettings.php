<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetUserSettings extends Controller
{
    /* To Get the User/Staff Date Format */
    public static function getPeopleDateFormat($type,$whereCondition){
        $dateFormat = UserPreferences::getTheDateFormat($type,$whereCondition);
        return $dateFormat;
    }
}
