<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanPaymentConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $details;
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
        $subject = "Payment Confirmation";
        if($this->details['language'] === "en")
            $subject = "Payment Confirmation";
        if($this->details['language'] === "jp")
            $subject = "Payment Confirmation";
        return $this->subject($subject)
        ->view('PlanPaymentConfirmation')
        ->with('details', $this->details);
    }
}
