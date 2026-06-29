<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'language';

    public static function getLanguages(){
        $languages = Language::where('status','1')->select('id','name','lang_code')->get();
        return $languages;
    }

    public static function getUserLanguage($whereCondition){
        $language = UserPreferences::where($whereCondition)
                    ->join('language','language.id','user_preferences.language_id')
                    ->select('language.lang_code')
                    ->first();
        return $language;
    }

    public static function getLanguagesCodeUsingId($id){
        $languages = Language::select('id','name','lang_code')->where('id',$id)->first();
        return $languages;
    }
}
