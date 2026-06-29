<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\TaskReportEmailScheduleSettings;
use Illuminate\Console\Command;

class OrderProductionReportEmailEschedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:productionReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Production Status Report for the selected orders of the Company';

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
        TaskReportEmailScheduleSettings::productionReportOrders();
        return 0;
    }
}
