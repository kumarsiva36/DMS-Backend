<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class language extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('language')->truncate();
        $language = [
            [
                'name' => 'English',
                'lang_code'=>'en',
                'status' =>'1',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Japanese',
                'lang_code'=>'jp',
                'status' =>'1',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Thai',
                'lang_code'=>'th',
                'status' =>'0',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Vietnamese',
                'lang_code'=>'kh',
                'status' =>'0',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Burmese',
                'lang_code'=>'br',
                'status' =>'0',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Chinese',
                'lang_code'=>'zh',
                'status' =>'0',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Bengali',
                'lang_code'=>'bd',
                'status' =>'1',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Hindi',
                'lang_code'=>'in',
                'status' =>'1',
                'created_at' =>date("Y-m-d H:i:s"),
                'updated_at' =>date("Y-m-d H:i:s")
            ],
        ];
        DB::table('language')->insert($language);
    }
}
