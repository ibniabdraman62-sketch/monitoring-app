<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RapportHebdoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public array $data;
    public string $pdfPath;

    public function __construct(Site $site, array $data, string $pdfPath)
    {
        $this->site    = $site;
        $this->data    = $data;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro Rapport hebdomadaire — {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rapport-hebdo',
            with: array_merge(['site' => $this->site], $this->data),
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as("rapport_{$this->site->client_name}_" . now()->format('Y-m-d') . ".pdf")
                ->withMime('application/pdf'),
        ];
    }
}