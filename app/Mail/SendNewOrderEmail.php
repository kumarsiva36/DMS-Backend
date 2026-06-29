<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNewOrderEmail extends Mailable
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
        $subject = 'Order Created'.' - '.$this->details['orderNo'];
        if($this->details['language'] === "en")
            $subject = 'Order Created'.' - '.$this->details['orderNo'];
        if($this->details['language'] === "jp")
            $subject = "オーダー作成／オーダー番号 ".$this->details['orderNo'];
        return $this->subject($subject)
                ->view('CreateOrder')
                ->with('details', $this->details);
    }
}
