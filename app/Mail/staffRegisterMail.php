<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class staffRegisterMail extends Mailable
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
        $subject = "Invitation to Workspace";
        if($this->details['language'] === "en")
            $subject = "Invitation to Workspace";
        if($this->details['language'] === "jp")
            $subject = "ワークスペースへの招待";
        return $this->subject($subject)
        ->view('StaffRegisterInWorkspace')
        ->with('details', $this->details);
    }
}
