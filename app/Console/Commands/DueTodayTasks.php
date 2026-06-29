<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\EmailForUserSettings;
use Illuminate\Console\Command;

class DueTodayTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:dueToday';

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
        EmailForUserSettings::tasksDueToday();
        return 0;
    }
}
