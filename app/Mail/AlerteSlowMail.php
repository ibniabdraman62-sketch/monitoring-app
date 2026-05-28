<?php

namespace App\Mail;

use App\Models\Site;
use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlerteSlowMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public Incident $incident;
    public int $responseTime;

    public function __construct(Site $site, Incident $incident, int $responseTime)
    {
        $this->site         = $site;
        $this->incident     = $incident;
        $this->responseTime = $responseTime;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro Lenteur détectée — {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alerte-slow',
            with: [
                'site'         => $this->site,
                'incident'     => $this->incident,
                'responseTime' => $this->responseTime,
            ],
        );
    }
}