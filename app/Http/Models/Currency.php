<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    public static function getCurrencies(){
        $listOfCurrencies = Currency::select('id','name','symbol')->get();
        return $listOfCurrencies;
    }
}
