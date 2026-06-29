<?php

namespace App\Jobs;

use App\Mail\TaskDelayNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmailNotificationDelayTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $details;
    public function __construct($details)
    {
        //
        $this->details = $details;

        // $to = 'sivakumar.a@catech.co.in';
        // $emailDetails = new TaskDelayNotificationMail($this->details);
        // Mail::to($to)->locale($this->details['language'])->send($emailDetails);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $emails = $this->details['to'];
        foreach(explode(",",$emails) as $to){
            //$to = $this->details['to'];
            $emailDetails = new TaskDelayNotificationMail($this->details);
            Mail::to($to)->locale($this->details['language'])->send($emailDetails);
        }
    }
}
