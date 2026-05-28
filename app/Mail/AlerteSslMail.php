<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteSslMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public int $daysRemaining;
    public string $expiresAt;

    public function __construct(Site $site, int $daysRemaining, string $expiresAt)
    {
        $this->site          = $site;
        $this->daysRemaining = $daysRemaining;
        $this->expiresAt     = $expiresAt;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro Certificat SSL bientôt expiré — {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerte-ssl',
            with: [
                'site'          => $this->site,
                'daysRemaining' => $this->daysRemaining,
                'expiresAt'     => $this->expiresAt,
            ],
        );
    }
}