<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyRemainderMail extends Mailable
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
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $subject = "Daily Reminder for  ".$this->details['orderNo']." & ".$this->details['styleNo'];
        // if($this->details['language'] === "en")
        //     $subject = "Daily Reminder for  ".$this->details['orderNo']." & ".$this->details['styleNo'];
        // if($this->details['language'] === "jp")
        //     $subject = "遅延リマインダー／オーダー番号 ".$this->details['orderNo']."・品番 & ".$this->details['styleNo'];

        $subject = "Daily Reminder for tasks starting Today (".date($this->details['dateFormat']).")";

        return $this->subject($subject)
        ->view('TaskDailyRemainder')
        ->with('details', $this->details)
        ->attach($this->details['pdf_path']);
    }
}
