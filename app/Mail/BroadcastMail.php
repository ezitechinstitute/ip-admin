<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class BroadcastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $messageBody;

    public function __construct($name, $messageBody)
    {
        $this->name = $name;
        $this->messageBody = $messageBody;
    }

    /**
     * English comments: This is the magic part. 
     * It runs inside the queue worker right before sending.
     */
    public function handle()
    {
        $settings = DB::table('admin_settings')->first();

        if ($settings && $settings->smtp_active_check == 1) {
            Config::set('mail.mailers.smtp.host', $settings->smtp_host);
            Config::set('mail.mailers.smtp.port', (int)$settings->smtp_port);
            Config::set('mail.mailers.smtp.username', $settings->smtp_email);
            Config::set('mail.mailers.smtp.password', $settings->smtp_password);
            Config::set('mail.mailers.smtp.encryption', ($settings->smtp_port == 465) ? 'ssl' : 'tls');
            Config::set('mail.from.address', $settings->smtp_email);
            Config::set('mail.from.name', 'Ezitech Learning Institute');

            Mail::purge('smtp');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important Notification from Ezitech Institute',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.emails-broadcast',
        );
    }
}