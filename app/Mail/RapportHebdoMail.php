<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RapportHebdoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Site $site;
    public array $data;
    public string $pdfPath;

    public function __construct(Site $site, array $data, string $pdfPath = '')
    {
        $this->site    = $site;
        $this->data    = $data;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MonitorPro — Rapport hebdomadaire : {$this->site->client_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rapport-hebdo',
        );
    }

   public function attachments(): array
{
    if (!empty($this->pdfPath) && file_exists($this->pdfPath)) {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->pdfPath)
                ->as('Rapport-' . $this->site->client_name . '-' . date('d-m-Y') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
    return [];
}
}