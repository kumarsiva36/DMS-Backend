<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\EmailForUserSettings;
use Illuminate\Console\Command;

class DailyRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:dailyRemainder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The task that are to Start Today';

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
        EmailForUserSettings::taskDailyRemainder();
        return 0;
    }
}
