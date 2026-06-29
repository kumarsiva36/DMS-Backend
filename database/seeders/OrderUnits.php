<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderUnits extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_units')->truncate();
        $orderUnits = [
            [
                'name' => 'Nos',
                'bom_unit'=>'1',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Cones',
                'bom_unit'=>'1',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Meters',
                'bom_unit'=>'1',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Rolls',
                'bom_unit'=>'1',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kgs',
                'bom_unit'=>'1',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Pieces',
                'bom_unit'=>'0',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Set',
                'bom_unit'=>'0',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Pack of 2',
                'bom_unit'=>'0',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Pack of 3',
                'bom_unit'=>'0',
                'is_default' =>'0',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('order_units')->insert($orderUnits);
    }
}
