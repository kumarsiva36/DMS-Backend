<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class size extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('size')->truncate();
        $size=[[
            'name' =>'S',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'M',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'L',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Men',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'S',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'M',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'L',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Women',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'S',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'M',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'L',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XXXL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Youth',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'S',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'M',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'L',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'XL',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'3-4',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'5-6',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ],[
            'name' =>'7-8',
            'company_id' => 0,
            'workspace_id' => 0,
            'user_id' => 0,
            'staff_id' => 0,
            'is_default' => '0',
            'status' => '1',
            'category' => 'Children',
            'created_by' => '0',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),

        ]];
        DB::table('size')->insert($size);

    }
    }

