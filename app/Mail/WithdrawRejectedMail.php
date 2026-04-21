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

class WithdrawRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $withdraw;
    public $reason;

    public function __construct($withdraw, $reason)
    {
        $this->withdraw = $withdraw;
        $this->reason = $reason;
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
            subject: 'Withdrawal Request Rejected ❌',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdraw-rejected',
            with: [
                'amount' => $this->withdraw->amount,
                'bank' => $this->withdraw->bank,
                'reason' => $this->reason,
                'date' => \Carbon\Carbon::parse($this->withdraw->created_at)->format('d M Y'),
            ]
        );
    }
}
