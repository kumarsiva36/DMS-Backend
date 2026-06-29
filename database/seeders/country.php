<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class country extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('country')->truncate();
        $country = [
            [
                'name'=>'India',
                'currency'=>'Rupees',
                'code'=>'+91',
                'language'=>'English',
                'currency_sign' =>'₹',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'Japan',
                'currency'=>'Yen',
                'code'=>'+81',
                'language'=>'Japanese',
                'currency_sign' =>'¥',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'Thailand',
                'currency'=>'Baht',
                'code'=>'+66',
                'language'=>'Thai',
                'currency_sign' =>'฿',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'Vietnam',
                'currency'=>'Vietnamese Dong',
                'code'=>'+84',
                'language'=>'Vietnamese',
                'currency_sign' =>'₫',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'Mynanmar',
                'currency'=>'Kyat',
                'code'=>'+95',
                'language'=>'Burmese',
                'currency_sign' =>'K',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'China',
                'currency'=>'Renminbi',
                'code'=>'+86',
                'language'=>'Chinese',
                'currency_sign' =>'¥',
                'created_at'=> date("Y-m-d H:i:s")
            ],
            [
                'name'=>'Bangladesh',
                'currency'=>'Taka',
                'code'=>'+880',
                'language'=>'Bengali',
                'currency_sign' =>'৳',
                'created_at'=> date("Y-m-d H:i:s")
            ]
        ];
        DB::table('country')->insert($country);
    }
}
