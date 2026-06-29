<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class fabricType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fabric_type')->truncate();
        $fabricType = [
            [
                'name' => 'Cotton',
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
                'name' => 'Linen',
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
                'name' => 'Wool',
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
                'name' => 'Synthetic Cotton',
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
                'name' => 'Satin',
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
                'name' => 'Silk',
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
                'name' => 'Denim',
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
                'name' => 'Polyester',
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
        DB::table('fabric_type')->insert($fabricType);
    }
}
