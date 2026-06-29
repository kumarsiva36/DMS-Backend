<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class staffOrderInviteMail extends Mailable
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
       // dd($this->details);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Invitation to Participate in the Order ".$this->details['orderNo']." .";
        if($this->details['language'] === "en")
            $subject = "Invitation to Participate in the Order ".$this->details['orderNo']." .";
        if($this->details['language'] === "jp")
            $subject = "参加者への招待状／オーダー番号 ".$this->details['orderNo'];
        return $this->subject($subject)
        ->view('StaffRegisterInOrder')
        ->with('details', $this->details)
        ->attach($this->details['pdf_path']);
    }
}
