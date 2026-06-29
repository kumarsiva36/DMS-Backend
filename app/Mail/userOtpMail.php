<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class userOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user, $language;
    public function __construct(User $userDetails,$language)
    {
          $this->user = $userDetails;
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
            'otp'=> $this->user->otp,
            'name'=>$this->user->name,
        ]);
    }
}
