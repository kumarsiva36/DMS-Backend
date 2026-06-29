<?php

namespace App\Console\Commands;

use App\Http\Controllers\WebSite\Common\EmailScheduleSettings;
use Illuminate\Console\Command;

class OrderMailPdfsDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delete-email-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Status of the Orders of the Company';

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
        EmailScheduleSettings::deleteEmailPDFs();
        return 0;
    }
}
