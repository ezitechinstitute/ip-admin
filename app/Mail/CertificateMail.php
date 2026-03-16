<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use SerializesModels;

    public $certificatePath;

    public function __construct($certificatePath)
    {
        $this->certificatePath = $certificatePath;
    }

    public function build()
    {
        return $this->subject('Your Internship Certificate')
                    ->view('mail.certificate')
                    ->attach(storage_path('app/public/' . $this->certificatePath));
    }
}