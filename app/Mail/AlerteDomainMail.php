<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteDomainMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public int $daysRemaining;

    public function __construct(Site $site, int $daysRemaining)
    {
        $this->site          = $site;
        $this->daysRemaining = $daysRemaining;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: " MonitorPro — Domaine expire dans {$this->daysRemaining} jours : {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerte-domain',
        );
    }
}