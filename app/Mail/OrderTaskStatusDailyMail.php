<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderTaskStatusDailyMail extends Mailable
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
        $this->details= $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->details[0]['type']=='Production')
        {
            $subject = "Order Production Status Details";
            if($this->details[0]['language'] === "en")
                $subject = "Order Production Status Details";
            if($this->details[0]['language'] === "jp")
                $subject = "Order Production Status Details";

        }else{
            $subject = "Order Task Status Details";
            if($this->details[0]['language'] === "en")
                $subject = "Order Task Status Details";
            if($this->details[0]['language'] === "jp")
                $subject = "Order Task Status Details";
        }

        return $this->subject($subject)
        ->view('OrderTaskStatus')
        ->with('details_arr', $this->details)
        ->attach($this->details['pdf_path']);
    }
}
