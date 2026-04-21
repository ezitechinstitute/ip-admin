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

class WithdrawSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $withdrawData;
    public $manager;

    public function __construct($withdrawData, $manager)
    {
        $this->withdrawData = $withdrawData;
        $this->manager = $manager;
    }

    public function handle()
    {
        $settings = DB::table('admin_settings')->first();

        if ($settings && $settings->smtp_active_check == 1) {
            Config::set('mail.mailers.smtp.host', $settings->smtp_host);
            Config::set('mail.mailers.smtp.port', (int)$settings->smtp_port);
            Config::set('mail.mailers.smtp.username', $settings->smtp_email);
            Config::set('mail.mailers.smtp.password', $settings->smtp_password);
            Config::set('mail.from.address', $settings->smtp_email);
            Config::set('mail.from.name', $settings->smtp_email);
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Withdrawal Request Submitted',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdraw-submitted',
            with: [
                'amount' => $this->withdrawData['amount'] ?? 0,
                'bank' => $this->withdrawData['bank'] ?? '',
                'accountHolder' => $this->withdrawData['ac_name'] ?? '',
                'managerName' => $this->manager->name ?? 'Manager',
                'description' => $this->withdrawData['description'] ?? '',
            ]
        );
    }
}
