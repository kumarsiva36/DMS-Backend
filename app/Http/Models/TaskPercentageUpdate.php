<?php

namespace App\Models;

use App\Common\CommonApp;
use Aws\Arn\Exception\InvalidArnException;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TaskPercentageUpdate extends Model
{
    use HasFactory;

    protected $table = 'order_task_inprogerss_percentage_update';


}
