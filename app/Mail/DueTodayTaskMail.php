<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DueTodayTaskMail extends Mailable
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
        // $subject = "Task Due Today Notification for ".$this->details['orderNo']." & ".$this->details['styleNo'];
        // if($this->details['language'] === "en")
        //     $subject = "Task Due Today Notification for ".$this->details['orderNo']." & ".$this->details['styleNo'];
        // if($this->details['language'] === "jp")
        //     $subject = "本日完了予定タスク／オーダー番号 ".$this->details['orderNo']."・品番 & ".$this->details['styleNo'];

        $subject = "Task Due Today (".date($this->details['dateFormat']).") Notification";

        return $this->subject($subject)
        ->view('TaskDueTodayRemainder')
        ->with('details', $this->details)
        ->attach($this->details['pdf_path']);
    }
}
