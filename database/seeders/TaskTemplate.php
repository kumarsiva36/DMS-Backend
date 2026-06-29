<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTemplate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_task_template')->truncate();
        $templateData = [
            [
                'company_id'=>'0',
                'workspace_id'=>'0',
                'user_id'=>'0',
                'staff_id'=>'0',
                'order_id'=>'0',
                'template_name'=>'Standard',
                'status'=>'0',
                'is_default' => '0',
                'task_template_structure'=>'[{"task_title":"Contract","task_subtitles":["Sales Contract/Proforma Order","LC Opening","SKU Details"]
                },{"task_title":"Fabric","task_subtitles":["Fabric Order","Fabric Shipment ETA","Fabric Swatch Submission","Fabric Swatch Approval"]},{"task_title":"Accessories","task_subtitles":["Accessories Sheet Receipt","Accessories Order Date","Accessories Shipment / Dispatch","Accessories Arrival","Accessories Approval"]},{"task_title":"Print","task_subtitles":["Print Data Instruction","Print Sample Submission","Print Approval"]},{"task_title":"Sample","task_subtitles":["APP Sample Submission","APP Sample Approval"]},{"task_title":"Factory Inspection","task_subtitles":["Factory Inspection 1","Factory Inspection 2"]},{"task_title":"3rd Party Inspection","task_subtitles":["Inspection 1","Inspection 2"]}
                ]',
                'created_by'=>'0',
                'created_user_type'=>'0',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]
        ];
        DB::table('order_task_template')->insert($templateData);
    }
}
