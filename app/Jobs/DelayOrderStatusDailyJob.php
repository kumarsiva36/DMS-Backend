<?php

namespace App\Jobs;

use App\Mail\DelayOrderStatusDailyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DelayOrderStatusDailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $details;
    public function __construct($details)
    {
        //
        $this->details = $details;
        // $to = 'sivakumar.a@catech.co.in';
        // $emailDetails = new DelayOrderStatusDailyMail($this->details);
        // Mail::to($to)->locale($this->details[0]['language'])->send($emailDetails);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to = $this->details[0]['to'];
        $emailDetails = new DelayOrderStatusDailyMail($this->details);
        Mail::to($to)->locale($this->details[0]['language'])->send($emailDetails);
    }
}
