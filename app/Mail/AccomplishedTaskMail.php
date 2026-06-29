<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccomplishedTaskMail extends Mailable
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
        $subject = "Accomplished Task Notification for Order and Style ".$this->details['orderNo']." & ".$this->details['styleNo'];
        if($this->details['language'] === "en")
            $subject = "Accomplished Task Notification for Order and Style ".$this->details['orderNo']." & ".$this->details['styleNo'];
        if($this->details['language'] === "jp")
            $subject = "完了タスク通知／オーダー番号 ".$this->details['orderNo']."・品番 & ".$this->details['styleNo'];
        return $this->subject($subject)
        ->view('TaskAccomplished')
        ->with('details', $this->details);
    }
}
