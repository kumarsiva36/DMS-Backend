<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Common\CommonApp;

class PoComments extends Model
{
    use HasFactory;
    protected $table = 'inquiry_po_comments';
    protected $fillable = [
        'company_id','workspace_id','user_id','staff_id','po_id','comment_type','comment_data','created_at','updated_at'
    ];

    public static function deletePOCommentFile($request){
        $whereCondition=[
            ['id','=',$request->id]
        ];
        $filepath = PoComments::where($whereCondition)->select('filepath','filesize','company_id')->limit(1)->get();
        if(!empty($filepath) && isset($filepath[0]['filepath'])){
            if($filepath[0]['company_id'] > 0){
               // dd($filepath[0]['filesize']);
                $companyDetails = CommonApp::getCompanyDetailsbyID($filepath[0]['company_id']);
                $storageUsed = $companyDetails->storage_used*1024*1024;
                $storageToBeFreed = $filepath[0]['filesize'];
                $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                $companyDetails->storage_used = $freedStorage;
                $companyDetails->save();
            }
            $file = $filepath[0]['filepath'];
            Storage::disk('s3')->delete($file);
        }
        PoComments::where($whereCondition)->delete();
        return true;
    }
}
