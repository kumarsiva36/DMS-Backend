<?php

namespace App\Common;

use App\Mail\staffOtpMail;
use App\Mail\userOtpMail;
use Illuminate\Support\Facades\Mail;

class Mailconfig{
    /* To Send OTP for User Login */
    public static function userOtpSendMail($email,$data,$language,$resend=0){
        if($resend==0)
            Mail::to($email)->send(new userOtpMail($data,$language));
        else
            Mail::mailer('second_mailer')->to($email)->send(new userOtpMail($data,$language));
    }
    /* To Send OTP for Staff Login */
    public static function staffOtpSendMail($email,$data,$language,$resend=0){
        if($resend==0)
            Mail::to($email)->send(new staffOtpMail($data,$language));
        else
            Mail::mailer('second_mailer')->to($email)->send(new staffOtpMail($data,$language));
    }
}
