<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanExpiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
    */
    public $details;
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "DMS - Plan 3 days to expire";
        if($this->details['language'] === "en")
            $subject = "DMS - Plan 3 days to expire";
        if($this->details['language'] === "jp")
            $subject = "DMSプラン有効期限残り3日";
        return $this->subject($subject)
        ->view('UserPlanExpiry')
        ->with('details', $this->details);
    }
}
