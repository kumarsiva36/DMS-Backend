<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricComposition extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fabric_composition')->truncate();
        $fabricComposition = [
            [
                'name' => 'Cotton',
                'is_default' =>'0',
                'status' => '0',
                'inquiry_reference_id'=>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Linen',
                'is_default' =>'0',
                'status' => '0',
                'inquiry_reference_id'=>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Wool',
                'is_default' =>'0',
                'status' => '0',
                'inquiry_reference_id'=>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Synthetic Cotton',
                'is_default' =>'0',
                'status' => '0',
                'inquiry_reference_id'=>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('fabric_composition')->insert($fabricComposition);
    }
}
