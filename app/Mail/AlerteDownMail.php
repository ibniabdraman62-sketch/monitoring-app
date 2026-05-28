<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteDownMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public Incident $incident;
    public ?int $httpCode;

    public function __construct(Site $site, Incident $incident, ?int $httpCode = null)
    {
        $this->site     = $site;
        $this->incident = $incident;
        $this->httpCode = $httpCode;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro Site hors ligne — {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerte-down',
            with: [
                'site'     => $this->site,
                'incident' => $this->incident,
                'httpCode' => $this->httpCode,
            ],
        );
    }
}