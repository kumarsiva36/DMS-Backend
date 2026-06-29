<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('task:dueToday')->daily();
        $schedule->command('task:dueTomorrow')->daily();
        $schedule->command('task:dailyRemainder')->daily();
        $schedule->command('task:weeklyRemainder')->weeklyOn(1,'2:30');
        $schedule->command('order:status')->daily();
        $schedule->command('order:delay')->daily();
        $schedule->command('order:finished')->daily();
        $schedule->command('order:taskReport')->daily();
        $schedule->command('order:productionReport')->daily();
        $schedule->command('plan:validity')->daily();
        $schedule->command('passport:purge')->hourly();
        $schedule->command('order:email-notification-for-delay-task')->dailyAt('2:00');
        $schedule->command('order:delete-email-pdfs')->dailyAt('23:00');
       // $schedule->command("backup:run --only-db --only-to-disk=s3")->dailyAt('2:00');
       $schedule->command('task:MobileDueToday')->daily();
       $schedule->command('task:MobileStartToday')->daily();
       $schedule->command('task:MobileDelayedTask')->daily();
       $schedule->command('log:ErrorlogBackup')->daily('1:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
