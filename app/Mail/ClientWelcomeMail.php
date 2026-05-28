<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $client;
    public string $plainPassword;
    public string $loginUrl;

    public function __construct(User $client, string $plainPassword)
    {
        $this->client        = $client;
        $this->plainPassword = $plainPassword;
        $this->loginUrl      = config('app.url') . '/login';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur MonitorPro — Vos identifiants de connexion',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-welcome',
        );
    }
}