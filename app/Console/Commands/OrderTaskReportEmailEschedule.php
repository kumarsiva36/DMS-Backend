<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\TaskReportEmailScheduleSettings;
use Illuminate\Console\Command;

class OrderTaskReportEmailEschedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:taskReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Task Report for the selected orders of the Company';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TaskReportEmailScheduleSettings::finishedOrders();
        return 0;
    }
}
