<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'country';

    public static function getCountries(){
        $countries = Country::select('id','name','code')->get();
        return $countries;
    }
}
