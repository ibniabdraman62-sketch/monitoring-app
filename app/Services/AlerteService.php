<?php
namespace App\Services;

use App\Models\Site;
use App\Models\Incident;
use App\Models\Alerte;
use Illuminate\Support\Facades\Mail;

class AlerteService
{
    public function sendAlert(Incident $incident, string $type): void
    {
        $site = $incident->site;
        $emails = $site->notify_emails
            ? array_filter(array_map('trim', explode(',', $site->notify_emails)))
            : [config('mail.from.address')];

        $mailable = match ($type) {
            'down'     => new \App\Mail\AlerteDownMail($site, $incident, 0),
            'slow'     => new \App\Mail\AlerteSlowMail($site, $incident, 0),
            'resolved' => new \App\Mail\AlerteResolvedMail($site, $incident),
            'ssl'      => new \App\Mail\AlerteSslMail($site, 0, ''),
            default    => null,
        };

        if (!$mailable) return;

        foreach ($emails as $email) {
            try {
                Mail::to(trim($email))->send($mailable);

                Alerte::create([
                    'incident_id' => $incident->id,
                    'sent_at'     => now(),
                    'type'        => $type,
                    'email_to'    => trim($email),
                ]);
            } catch (\Exception $e) {
                \Log::error("Erreur envoi alerte : " . $e->getMessage());
            }
        }
    }

    public function sendDomainAlert(Site $site, int $daysLeft): void
    {
        $emails = $site->notify_emails
            ? array_filter(array_map('trim', explode(',', $site->notify_emails)))
            : [config('mail.from.address')];

        $expiresAt = $site->domain_expires_at ?? '';

        foreach ($emails as $email) {
            try {
                Mail::to(trim($email))->send(
                    new \App\Mail\AlerteDomainMail($site, $daysLeft, $expiresAt)
                );
            } catch (\Exception $e) {
                \Log::error("Erreur alerte domaine : " . $e->getMessage());
            }
        }
    }

    public function sendSslAlert(Site $site, int $daysLeft, string $expiresAt): void
    {
        $emails = $site->notify_emails
            ? array_filter(array_map('trim', explode(',', $site->notify_emails)))
            : [config('mail.from.address')];

        foreach ($emails as $email) {
            try {
                Mail::to(trim($email))->send(
                    new \App\Mail\AlerteSslMail($site, $daysLeft, $expiresAt)
                );
            } catch (\Exception $e) {
                \Log::error("Erreur alerte SSL : " . $e->getMessage());
            }
        }
    }
}