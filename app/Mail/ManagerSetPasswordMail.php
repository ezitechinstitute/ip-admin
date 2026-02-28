<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerSetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function build()
    {
        return $this->subject('ETI Platform Account Created')
                    ->view('mail.manager_account_password_set')
                    ->with([
                        'name' => $this->manager['name'],
                        'email' => $this->manager['email'] 
                    ]);
    }
}