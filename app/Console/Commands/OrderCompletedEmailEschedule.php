<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\FinishedEmailScheduleSettings;
use Illuminate\Console\Command;

class OrderCompletedEmailEschedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The completed orders of the Company in the week';

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
        FinishedEmailScheduleSettings::finishedOrders();
        return 0;
    }
}
