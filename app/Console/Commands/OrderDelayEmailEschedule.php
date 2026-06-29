<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\DelayEmailScheduleSettings;
use Illuminate\Console\Command;

class OrderDelayEmailEschedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The delayed orders of the Company';

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
        DelayEmailScheduleSettings::delayOrderStatus();
        return 0;
    }
}
