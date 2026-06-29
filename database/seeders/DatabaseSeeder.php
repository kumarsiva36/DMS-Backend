<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            color::class,
            size::class,
            roles::class,
            country::class,
            language::class,
            permissions::class,
            WorkspaceType::class,
            fabricType::class,
            orderCategory::class,
            orderArticle::class,
            TaskTemplate::class,
            IncomeTerms::class,
            PlanDetails::class,
            emailScheduleTask::class,
            currency::class,
            timezone::class,
            inquiryMaster::class,
            fabricMaster::class,
            OrderUnits::class,
            FabricComposition::class
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
