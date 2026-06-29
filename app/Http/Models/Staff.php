<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use InvalidArgumentException;
use Laravel\Passport\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;

    protected $table = 'staff';
    protected $fillable =[
       'id','first_name','last_name','company_id','workspace_id','user_id','role_id','mobile','email','address1','address2','city','state','zipcode','country','status','language','timezone','user_type','profile_img','is_confidentional','created_at','updated_at'
    ];
    protected $hidden=[
        'otp',
        //'remember_token',
    ];
     //
    protected $casts=[
        'email_verified_at' => 'datetime',
    ];

    public static function checkIfUserExistsInSameCompany($request){
    $whereConditions = [
        ['company_id','=',$request->company_id],
        ['workspace_id','=',$request->workspace_id],
        ['email','=',$request->email]
    ];
    $staff = Staff::where($whereConditions)->first();

    return $staff;
    }

    public static function getStaffByEmailLogin($email,$type = ""){

        if($type === "validateOTP"){
            $staff =  Staff::where('email',$email)->where('status',"1")->get();
        }else{
            $staff =  Staff::where('email',$email)->where('status',"1")->first();
        }

       return $staff;
    }

    public static function getStaffByID($id){
       $staff =  Staff::where('id',$id)->first();

       return $staff;
    }

    public static function getStaffDetailForEdit($request){
        $whereConditions = [
            ['id',$request->staff_id],
            ['company_id',$request->company_id],
            ['workspace_id',$request->workspace_id]
        ];
        $staffDetails = Staff::where($whereConditions)->select('id','company_id','role_id','email',
                    'first_name','last_name','mobile','address1','address2','city','state','country','zipcode','status','user_type','profile_img','staff_type','is_confidentional')->first();
        return $staffDetails;
    }

    public static function getStaffLists($request){
        if(isset($request->page_type) && $request->page_type=='order_contacts'){
            $whereCondition = [
                ['staff.company_id','=',$request->company_id],
                ['staff.status','=','1'],
            ];
        }else{
            $whereCondition = [
                ['staff.company_id','=',$request->company_id],
            ];
        }
        $staffList = Staff::where($whereCondition)
                            ->join('roles','roles.id','staff.role_id')
                            ->join('company_settings','company_settings.id','staff.company_id')
                            ->select('staff.id','staff.first_name','staff.last_name','staff.email','staff.status'
                            ,'roles.name as role','company_settings.company_name as companyName','staff.user_type','staff.is_confidentional')
                            ->orderBy('staff.id','asc')
                            ->get();
        return $staffList;
    }

    public static function logout($request,$header){
        $token = $header->user()->token();
        $staffLog = UserHistory::getStaffLog($request->staff_id);
        if($staffLog!=null){
            $staffLog->logged_out_datetime = date('Y-m-d H:i:s');
            $staffLog->save();
        }
        $token->revoke();
    }

    public static function addLastSeen($request){
        $whereCondition[]=['id','=',$request->staff_id];
        $whereCondition[]=['company_id','=',$request->company_id];
        $whereCondition[]=['workspace_id','=',$request->workspace_id];

        try{
            $theStaff = Staff::where($whereCondition)->first();
            $theStaff->last_seen = date('Y-m-d H:i:s');
            $theStaff->save();
        }catch(Exception $e){
            throw new InvalidArgumentException("Unable To Save Data");
        }
    }
}
