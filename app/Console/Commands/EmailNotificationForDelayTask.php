<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WebSite\Common\EmailForUserSettings;

class EmailNotificationForDelayTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:email-notification-for-delay-task';

    /**
     * The console command description.
     *
     * @var string
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        EmailForUserSettings::tasksDelayNotification();
        return 0;
    }
}
