<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // English comments: Define public variable to access it in blade

    public function __construct($otp)
    {
        // English comments: Assign OTP to the class property
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Password Reset Verification Code')
                    ->view('mail.forget-password'); // English comments: This is your email template
    }
}