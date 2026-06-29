<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Color extends Model
{
    use HasFactory;
    protected $table = 'color';
    protected $fillable = [
        'name','company_id','workspace_id','user_id','staff_id','is_default','status','created_by'
    ];

    /* Get Colors */
    public static function getColors($request){
        $companyId = (int)$request->company_id;
        $workspaceId = (int)$request->workspace_id;
        $userId = (int)$request->user_id;
        $whereCondition=[
            ['company_id','=',$companyId],
            ['workspace_id','=',$workspaceId],
            ['user_id','=',$userId],
            ['status','!=',"3"]
            ];

        $getColors = Color::where($whereCondition)->orWhere('is_default', '=', '0')->get();

        return $getColors;
    }

    /* Check If the color of same name exists */
    public static function checkIfColorExists($whereCondition){
        $existsColor = Color::select('name')->where($whereCondition)->first();

        return $existsColor;
    }

    /* Delete A Color */
    public static function deleteColor($id,$colorArray){
        Color::where('id',$id)->update($colorArray);
    }

    /*Get Color Name Using ID*/
    public static function getColorNameUsingId($id){

        $whereCondition=[
            ['id','=',$id],
        ];
        $color = Color::where($whereCondition)->first();

       return $color;
    }

    /* Get The Color */
    public static function getColorForEdit($request){
        $whereCondition=[
            ['id','=',$request->id],
            ['company_id','=',$request->company_id],
            ['workspace_id','=',$request->workspace_id],
        ];
        $color = Color::where($whereCondition)->first();

        return $color;
    }
}
