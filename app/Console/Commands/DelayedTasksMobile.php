<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\MobileNotification;
use Illuminate\Console\Command;

class DelayedTasksMobile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:MobileDelayedTask';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tasks that are Delayed';

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
        MobileNotification::mobileDelayedTasks();
        return 0;
    }
}
