<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class InquiryContact extends Model
{
    use HasFactory;

    protected $table = 'inquiry_contact';

    public static function get_factory_contact_id($factory_id){
        $whereConditions=[
            ['factory_id','=',$factory_id]
        ];

        $res = InquiryContact::where($whereConditions)->pluck('id')->first();
        return $res ?? 0;
    }
}
