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
            ? explode(',', $site->notify_emails)
            : [config('mail.from.address')];

        $subjects = [
            'down'     => "🔴 PANNE — {$site->client_name} est hors ligne",
            'slow'     => "🟡 LENTEUR — {$site->client_name} répond lentement",
            'resolved' => "🟢 RÉSOLU — {$site->client_name} est de nouveau en ligne",
            'ssl'      => "⚠️ SSL — Certificat expire bientôt — {$site->client_name}",
        ];

        $messages = [
            'down' => "🔴 ALERTE PANNE\n\nLe site {$site->client_name} ({$site->url}) est HORS LIGNE.\n\nDétecté le : " . now()->format('d/m/Y à H:i:s') . "\n\n— MonitorPro | Soft Seven Art",
            'slow' => "🟡 ALERTE LENTEUR\n\nLe site {$site->client_name} ({$site->url}) répond lentement.\n\nDétecté le : " . now()->format('d/m/Y à H:i:s') . "\n\n— MonitorPro | Soft Seven Art",
            'resolved' => "🟢 RÉSOLUTION\n\nLe site {$site->client_name} ({$site->url}) est de nouveau EN LIGNE.\n\nRésolu le : " . now()->format('d/m/Y à H:i:s') . "\n\n— MonitorPro | Soft Seven Art",
            'ssl' => "⚠️ ALERTE SSL\n\nLe certificat SSL du site {$site->client_name} ({$site->url}) expire bientôt.\n\n— MonitorPro | Soft Seven Art",
        ];

        foreach ($emails as $email) {
            try {
                Mail::raw(
                    $messages[$type] ?? "Alerte MonitorPro — {$site->client_name}",
                    fn($m) => $m->to(trim($email))
                                 ->subject($subjects[$type] ?? "Alerte — {$site->client_name}")
                );

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
            ? explode(',', $site->notify_emails)
            : [config('mail.from.address')];

        foreach ($emails as $email) {
            try {
                Mail::raw(
                    "⚠️ ALERTE DOMAINE — {$site->client_name}\n\n" .
                    "Le nom de domaine du site {$site->url} expire dans {$daysLeft} jours.\n" .
                    "Registrar : {$site->domain_registrar}\n" .
                    "Date expiration : {$site->domain_expires_at}\n\n" .
                    "— MonitorPro | Soft Seven Art",
                    fn($m) => $m->to(trim($email))
                                 ->subject("⚠️ Domaine expire bientôt — {$site->client_name}")
                );
            } catch (\Exception $e) {
                \Log::error("Erreur alerte domaine : " . $e->getMessage());
            }
        }
    }
}