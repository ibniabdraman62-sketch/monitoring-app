<?php
namespace App\Services;

use App\Models\Site;
use App\Models\Verification;
use App\Models\Incident;
use App\Models\Alerte;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MonitoringService
{
    public function checkSite(Site $site): void
    {
        $client = new Client(['timeout' => 10, 'verify' => false]);
        $isUp = false;
        $httpCode = 0;
        $responseTime = 0;
        $sslValid = null;
        $sslExpiresAt = null;
        $sslDaysRemaining = null;

        try {
            $start = microtime(true);
            $response = $client->get($site->url);
            $responseTime = round((microtime(true) - $start) * 1000);
            $httpCode = $response->getStatusCode();
            $isUp = $httpCode >= 200 && $httpCode < 400;
        } catch (RequestException $e) {
    $httpCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
    $isUp = false;
} catch (\GuzzleHttp\Exception\ConnectException $e) {
    $httpCode = 0;
    $isUp = false;
} catch (\Exception $e) {
    $httpCode = 0;
    $isUp = false;
}

        // Vérification SSL
        if ($site->ssl_check) {
            try {
                $host = parse_url($site->url, PHP_URL_HOST);
                $ssl = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false]]);
                $socket = stream_socket_client(
                    "ssl://{$host}:443", $errno, $errstr, 10,
                    STREAM_CLIENT_CONNECT, $ssl
                );
                if ($socket) {
                    $params = stream_context_get_params($socket);
                    $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                    $sslValid = true;
                    $sslExpiresAt = Carbon::createFromTimestamp($cert['validTo_time_t'])->toDateString();
                    $sslDaysRemaining = (int) ceil(($cert['validTo_time_t'] - time()) / 86400);
                } else {
                    $sslValid = false;
                }
            } catch (\Exception $e) {
                $sslValid = false;
            }
        }

        // Enregistrer la vérification
        Verification::create([
            'site_id'            => $site->id,
            'checked_at'         => now(),
            'http_code'          => $httpCode,
            'response_time_ms'   => $responseTime,
            'ssl_valid'          => $sslValid,
            'ssl_expires_at'     => $sslExpiresAt,
            'ssl_days_remaining' => $sslDaysRemaining,
            'is_up'              => $isUp,
        ]);

        // Gestion des incidents
        $openIncident = Incident::where('site_id', $site->id)
            ->whereNull('resolved_at')->latest()->first();

        if (!$isUp && !$openIncident) {
            // Nouveau incident
            $incident = Incident::create([
                'site_id'    => $site->id,
                'started_at' => now(),
                'type'       => 'offline',
            ]);
            $this->sendAlert($site, $incident, 'down');

        } elseif ($isUp && $openIncident) {
            // Résolution incident
            $duration = now()->diffInMinutes($openIncident->started_at);
            $openIncident->update([
                'resolved_at'  => now(),
                'duration_min' => $duration,
                'is_resolved'  => true,
            ]);
            $this->sendAlert($site, $openIncident, 'resolved');

        } elseif ($isUp && $responseTime > $site->response_threshold_ms && !$openIncident) {
            // Site lent
            $incident = Incident::create([
                'site_id'    => $site->id,
                'started_at' => now(),
                'type'       => 'slow',
            ]);
            $this->sendAlert($site, $incident, 'slow');
        }
    }

    private function sendAlert(Site $site, Incident $incident, string $type, ?int $responseTime = null, ?int $httpCode = null): void
{
    $user = $site->user;

    Alerte::create([
        'incident_id' => $incident->id,
        'sent_at'     => now(),
        'type'        => $type,
        'email_to'    => $user->email,
    ]);

    $recipients = [$user->email];

    if (!empty($site->notify_emails)) {
        $recipients = array_merge(
            $recipients,
            array_filter(array_map('trim', explode(',', $site->notify_emails)))
        );
    }

    $mailable = match ($type) {

        'down' => new \App\Mail\AlerteDownMail(
            $site,
            $incident,
            $httpCode ?? 0
        ),

        'slow' => new \App\Mail\AlerteSlowMail(
            $site,
            $incident,
            $responseTime ?? 0
        ),

        'resolved' => new \App\Mail\AlerteResolvedMail(
            $site,
            $incident
        ),

        // BONUS si tu veux les activer plus tard
        'ssl' => new \App\Mail\AlerteSslMail(
            $site,
            $site->ssl_days_remaining ?? 0,
            $site->ssl_expires_at ?? ''
        ),

        'domain' => new \App\Mail\AlerteDomainMail(
            $site,
            $site->domain_days_remaining ?? 0,
            $site->domain_expires_at ?? ''
        ),

        default => null,
    };

    if (!$mailable) {
        logger()->warning("Type email inconnu: {$type}");
        return;
    }

    \Illuminate\Support\Facades\Mail::to($recipients)->send($mailable);
}

    public function calculateHealthScore(Site $site): int
    {
        return $site->getHealthScore();
    }

    public function checkSSL(string $url): array
    {
        try {
            $host = parse_url($url, PHP_URL_HOST);
            $context = stream_context_create([
                'ssl' => ['capture_peer_cert' => true, 'verify_peer' => false]
            ]);
            $client = stream_socket_client(
                "ssl://{$host}:443", $errno, $errstr, 10,
                STREAM_CLIENT_CONNECT, $context
            );
            if (!$client) return ['valid' => false, 'days_remaining' => 0];
            $params = stream_context_get_params($client);
            $cert   = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            $expires = $cert['validTo_time_t'] ?? 0;
            $days    = (int) ceil(($expires - time()) / 86400);
            return ['valid' => $days > 0, 'days_remaining' => max(0, $days)];
        } catch (\Exception $e) {
            return ['valid' => false, 'days_remaining' => 0];
        }
    }
}