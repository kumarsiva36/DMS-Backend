<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class color extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('color')->truncate();
        $color=[[
            'name' =>'Green',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Blue',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Red',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'White',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Black',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ]];
        DB::table('color')->insert($color);
    
    }
}
