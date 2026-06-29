<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HolidaySetting extends Model
{
    use HasFactory;

    protected $table = 'holiday_settings';

    /* Get a list of holidays */
    public static function getHolidays($request){
        $whereConditions = [
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
            ['status','=',"1"]
        ];
        $holidays = HolidaySetting::where($whereConditions)
                    ->select('id','name','description','holiday_start_date','holiday_end_date','days')
                    ->orderBy('holiday_start_date','ASC')
                    ->get();
        return $holidays;
    }

    /* Get a Holiday */
    public static function getHoliday($whereConditions){
       $holiday =  HolidaySetting::where($whereConditions)->first();
       return $holiday;
    }

    /* Create a holiday */
    public static function createHoliday($request,$companySettings){
        try{
            $holidaySettingArr=[];
            $holidaySettingArr['company_id']=$request->company_id;
            $holidaySettingArr['workspace_id']=$request->workspace_id;
            $holidaySettingArr['user_id']=$companySettings->user_id;
            $holidaySettingArr['staff_id']=$request->staff_id ?? 0;
            $holidaySettingArr['name']=$request->name;
            $holidaySettingArr['description']=$request->description ?? '';
            $holidaySettingArr['holiday_start_date']=date('Y-m-d',strtotime($request->holiday_start_date));
            $holidaySettingArr['holiday_end_date']=date('Y-m-d',strtotime($request->holiday_end_date));

            $start_date = new DateTime($request->holiday_start_date);
            $end_date = new DateTime($request->holiday_end_date);
            $days = ($start_date->diff($end_date)->days)+1;

            $holidaySettingArr['days']=$days;
            $holidaySettingArr['status']="1";
            $holidaySettingArr['created_at']=date('Y-m-d H:i:s');
            $holidaySettingArr['updated_at']=date('Y-m-d H:i:s');
            HolidaySetting::insert($holidaySettingArr);
            $holidayId = DB::getPdo()->lastInsertId();
            return $holidayId;
        }catch(Exception $e){
            throw new InvalidArgumentException("Unable to Post Data");
        }
    }

    /* Delete a Holiday */
    public static function deleteHoliday($whereConditions){
        try{
            $deleteHoliday = HolidaySetting::getHoliday($whereConditions);
            if(empty($deleteHoliday)){
                return response()->json(['status_code'=>400,"status"=>"failure","message"=>"Holiday already removed"]);
            }
            $deleteHoliday->status = "2";
            $deleteHoliday->save();
        }catch(Exception $e){
            throw new InvalidArgumentException("Unable to Post Data");
        }
    }
}
