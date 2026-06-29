<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechPackEditDetails extends Model
{
    use HasFactory;
    protected $table = 'techpack_edit_details';
    protected $fillable = [
        'company_id','workspace_id','user_id','staff_id','techpack_id','techpack_type','techpack_details','created_at','updated_at','reference_id'
      ];


}
