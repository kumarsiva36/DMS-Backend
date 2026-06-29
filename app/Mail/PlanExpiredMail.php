<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanExpiredMail extends Mailable
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
        $subject = "Plan Expired in DMS";
        if($this->details['language'] === "en")
            $subject = "Plan Expired in DMS";
        if($this->details['language'] === "jp")
            $subject = "DMSのプラン有効期限切れ";
        return $this->subject($subject)
        ->view('UserPlanExpired')
        ->with('details', $this->details);
    }
}
