<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class fabricMaster extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fabric_master')->truncate();
        $fabricMaster = [
            [
                'type' => 'YarnQuality',
                'content' =>'Combed',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'YarnQuality',
                'content' =>'Semi Combed',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'YarnQuality',
                'content' =>'Karded',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'YarnQuality',
                'content' =>'Customize',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'Meterial',
                'content' =>'100% Cotton',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'Meterial',
                'content' =>'Cotton / Polyester',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'Meterial',
                'content' =>'Modal',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'type' => 'Meterial',
                'content' =>'Cotton / Modal',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Meterial',
                'content' =>'Bamboo',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Meterial',
                'content' =>'Customize',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Composition',
                'content' =>'100%',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Composition',
                'content' =>'75%',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Composition',
                'content' =>'50%',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],[
                'type' => 'Composition',
                'content' =>'25%',
                'reference_id' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('fabric_master')->insert($fabricMaster);
    }
}
