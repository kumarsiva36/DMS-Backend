<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechPackImages extends Model
{
    use HasFactory;
    protected $table = 'techpack_images';
    protected $fillable = [
        'company_id','workspace_id','user_id','staff_id','techpack_id','techpack_details_id','reference_id','techpack_type','image','image_height','image_width','convert_images','created_at','updated_at'
    ];
}
