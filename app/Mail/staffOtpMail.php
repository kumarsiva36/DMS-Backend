<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Staff;

class staffOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
    */
    public $staff , $language;
    public function __construct(Staff $staffDetails,$language)
    {
        $this->staff = $staffDetails;
        $this->language = $language;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Verification OTP";
        if($this->language === "en")
            $subject = "Verification OTP";
        if($this->language === "jp")
            $subject = "認証OTP";
        return $this->subject($subject)
            ->view('mailOtp')->with([
            'otp'=> $this->staff->otp,
            'name'=>$this->staff->first_name." ".$this->staff->last_name,
        ]);
    }
}
