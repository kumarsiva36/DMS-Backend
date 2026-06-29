<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeTerms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('income_terms')->truncate();
        $incomeTerms=[
            [
                'name' => 'CIF',
                'description'=>'Cost Insurance Freight',
                'created_by' => '0',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'FOB',
                'description'=>'Free On Board',
                'created_by' => '0',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'LDP',
                'description'=>'Limited Duration Preferred',
                'created_by' => '0',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];
        DB::table('income_terms')->insert($incomeTerms);
    }
}
