<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public Incident $incident;

    public function __construct(Site $site, Incident $incident)
    {
        $this->site     = $site;
        $this->incident = $incident;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro Service rétabli — {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerte-resolved',
            with: [
                'site'     => $this->site,
                'incident' => $this->incident,
            ],
        );
    }
}