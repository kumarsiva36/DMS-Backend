<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Schema::disableForeignKeyConstraints();
       DB::table('roles')->truncate();
     //  DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $roles=[[
            'name' =>'Admin',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'0',
            'status' => '2',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'Manager',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'5',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'Staff',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'2',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'Guest',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'1',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'Supervisor',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'3',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'Merchandiser',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'order_sequence' =>'4',
            'status' => '1',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],];
        DB::table('roles')->insert($roles);
       // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      //  Schema::enableForeignKeyConstraints();
    }
}
