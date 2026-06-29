<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserLoginConfirmationMail extends Mailable
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
        $subject = "Login Activity confirmation";
        if($this->details['language'] === "en")
            $subject = "Login Activity confirmation";
        if($this->details['language'] === "jp")
            $subject = "ログイン確認";
        return $this->subject($subject)
        ->view('UserLoginConfirmation')
        ->with('details', $this->details);
    }
}
