<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinishedOrderStatusDailyMail extends Mailable
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
        $subject = "Finished Status for Order ".$this->details['orderNo']." & Style ".$this->details['styleNo'];
        if($this->details['language'] === "en")
            $subject = "Finished Status for Order ".$this->details['orderNo']." & Style ".$this->details['styleNo'];
        if($this->details['language'] === "jp")
            $subject = "完了状況／オーダー番号 ".$this->details['orderNo']."・品番 & ".$this->details['styleNo'];
        return $this->subject($subject)
        ->view('FinishedOrderStatus')
        ->with('details', $this->details);
    }
}
