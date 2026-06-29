<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialPlanExpiredMail extends Mailable
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
        $subject = "DMS - Trial Plan Ended";
        if($this->details['language'] === "en")
            $subject = "DMS - Trial Plan Ended";
        if($this->details['language'] === "jp")
            $subject = "トライアルプランの期限が終了しました。";
        return $this->subject($subject)
        ->view('UserPlanTrialEnded')
        ->with('details', $this->details);
    }
}
