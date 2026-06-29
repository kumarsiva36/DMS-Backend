<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\MobileNotification;
use Illuminate\Console\Command;

class StartTodayTasksMobile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:MobileStartToday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tasks that are Starts today';

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
        MobileNotification::mobileTasksStartToday();
        return 0;
    }
}
