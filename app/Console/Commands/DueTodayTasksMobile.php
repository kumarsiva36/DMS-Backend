<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\MobileNotification;
use Illuminate\Console\Command;

class DueTodayTasksMobile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:MobileDueToday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tasks that are due today';

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
        MobileNotification::mobileTasksDueToday();
        return 0;
    }
}
