<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\EmailForUserSettings;
use Illuminate\Console\Command;

class DueTomorrowTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:dueTomorrow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tasks that are due tomorrow';

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
        EmailForUserSettings::tasksDueTomorrow();
        return 0;
    }
}
