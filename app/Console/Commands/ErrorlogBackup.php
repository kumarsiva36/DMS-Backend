<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\ErrorlogsBackup;
use Illuminate\Console\Command;

class ErrorlogBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:ErrorlogBackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Error log files backup';

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
        ErrorlogsBackup::erorrlogbackup();
        return 0;
    }
}
