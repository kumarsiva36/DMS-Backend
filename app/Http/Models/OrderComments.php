<?php

namespace App\Models;

use App\Common\GetUserLanguage;
use App\Http\Controllers\WebSite\Common\GetUserSettings;
use App\Jobs\CreateOrderEmail;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class OrderComments extends Model
{
    use HasFactory;

    protected $table = 'order_comments';


}
