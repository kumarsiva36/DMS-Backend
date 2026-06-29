<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class orderCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_category')->truncate();
        $fabricType = [
            [
                'name' => 'Men',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Women',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kids/Children',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Boys',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Girls',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Neutral',
                'company_id' =>'0',
                'workspace_id' => '0',
                'user_id' => '0',
                'staff_id' => '0',
                'is_default' => '0',
                'status' => '0',
                'created_by' =>'0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        DB::table('order_category')->insert($fabricType);
    }
}
