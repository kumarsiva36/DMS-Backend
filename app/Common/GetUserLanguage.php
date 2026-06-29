<?php
namespace App\Common;

use App\Models\Language;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Support\Facades\Log;

class GetUserLanguage
{
    /* Get User defined language */
    public static function getLanguageOfUserWithId($companyID,$workspaceID,$type,$userId){
        $whereCondition = [
            ['company_id','=',$companyID],
            ['workspace_id','=',$workspaceID]
        ];
        if($type === 'User'){
            $whereCondition[]=['user_id','=',$userId];
        }
        else if($type === 'Staff'){
            $whereCondition[]=['staff_id','=',$userId];
        }

        $language = Language::getUserLanguage($whereCondition);
        $userLanguage="en";

        if(!empty($language)){
            $userLanguage = $language->lang_code;
        }

        return $userLanguage;
    }

    public static function getUserLanguageWithEmail($email,$type){
        if($type === 'User'){
            $user= User::where('email',$email)->first();
            $language = UserPreferences::where('company_id',$user->company_id)->where('user_id',$user->id)
                            ->join('language','language.id','user_preferences.language_id')
                            ->select('language.lang_code')
                            ->first();
        }
        else if($type === 'Staff'){
            $staff= Staff::where('email',$email)->first();
            $language = UserPreferences::where('company_id',$staff->company_id)->where('staff_id',$staff->id)
                            ->join('language','language.id','user_preferences.language_id')
                            ->select('language.lang_code')
                            ->first();
        }

        $userLanguage="en";

        if(!empty($language)){
            $userLanguage = $language->lang_code;
        }

        return $userLanguage;
    }

    public static function getLanguageOfCompanyWithUser($companyID,$userId){
        $whereCondition = [
            ['company_id','=',$companyID],
            ['user_id','=',$userId]
        ];

        $language = UserPreferences::where($whereCondition)
                    ->join('language','language.id','user_preferences.language_id')
                    ->select('language.lang_code')
                    ->first();
        $userLanguage="en";

        if(!empty($language)){
            $userLanguage = $language->lang_code;
        }

        return $userLanguage;
    }

}
