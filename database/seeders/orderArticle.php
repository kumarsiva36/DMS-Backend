<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class orderArticle extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_article_name')->truncate();
        $fabricType = [
            [
                'name' => 'Shirt',
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
                'name' => 'Pants',
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
                'name' => 'T-Shirts',
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
                'name' => 'Shorts',
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
                'name' => 'Vests',
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
                'name' => 'Briefs',
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
        DB::table('order_article_name')->insert($fabricType);
    }
}
