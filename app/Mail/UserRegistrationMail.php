<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistrationMail extends Mailable
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
        $subject = "Welcome to Digital Merchandising System";
        if($this->details['language'] === "en")
            $subject = "Welcome to Digital Merchandising System";
        if($this->details['language'] === "jp")
            $subject = "DMS -Digital Merchandising System-へようこそ";
        return $this->subject($subject)
        ->view('UserRegistration')
        ->with('details', $this->details);
    }
}
