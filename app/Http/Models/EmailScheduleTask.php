<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailScheduleTask extends Model
{
    use HasFactory;
    protected $table = 'email_schedule_task';

    public static function getEmailSchedule(){
      $emailScheduleSettings = EmailScheduleTask::select('id','name')->where("status","1")->get();
      return $emailScheduleSettings;
    }
}
