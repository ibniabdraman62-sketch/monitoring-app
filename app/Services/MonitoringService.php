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

        try {
            $start = microtime(true);
            $response = $client->get($site->url);
            $responseTime = round((microtime(true) - $start) * 1000);
            $httpCode = $response->getStatusCode();
            $isUp = $httpCode >= 200 && $httpCode < 400;
        } catch (RequestException $e) {
            $httpCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $isUp = false;
        }

        // Vérification SSL
        if ($site->ssl_check) {
            try {
                $host = parse_url($site->url, PHP_URL_HOST);
                $ssl = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
                $socket = stream_socket_client(
                    "ssl://{$host}:443", $errno, $errstr, 10,
                    STREAM_CLIENT_CONNECT, $ssl
                );
                if ($socket) {
                    $params = stream_context_get_params($socket);
                    $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                    $sslValid = true;
                    $sslExpiresAt = Carbon::createFromTimestamp($cert['validTo_time_t'])->toDateString();
                }
            } catch (\Exception $e) {
                $sslValid = false;
            }
        }

        // Enregistrer la vérification
        Verification::create([
            'site_id'          => $site->id,
            'checked_at'       => now(),
            'http_code'        => $httpCode,
            'response_time_ms' => $responseTime,
            'ssl_valid'        => $sslValid,
            'ssl_expires_at'   => $sslExpiresAt,
            'is_up'            => $isUp,
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

    private function sendAlert(Site $site, Incident $incident, string $type): void
    {
        $user = $site->user;

        Alerte::create([
            'incident_id' => $incident->id,
            'sent_at'     => now(),
            'type'        => $type,
            'email_to'    => $user->email,
        ]);

        $subject = match($type) {
            'down'     => "🔴 ALERTE : {$site->client_name} est HORS LIGNE",
            'slow'     => "🟡 ALERTE : {$site->client_name} est LENT",
            'resolved' => "🟢 RÉSOLU : {$site->client_name} est de nouveau EN LIGNE",
        };

        $message = match($type) {
            'down'     => "Le site {$site->url} est hors ligne depuis " . now()->format('H:i:s'),
            'slow'     => "Le site {$site->url} répond lentement.",
            'resolved' => "Le site {$site->url} est de nouveau accessible.",
        };

        Mail::raw($message, function($mail) use ($user, $subject) {
            $mail->to($user->email)->subject($subject);
        });
    }
}