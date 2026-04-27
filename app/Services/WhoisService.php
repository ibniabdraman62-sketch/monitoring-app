<?php
namespace App\Services;

use Iodev\Whois\Factory;
use App\Models\Site;
use App\Models\WhoisInfo;
use App\Services\AlerteService;

class WhoisService {
    public function checkDomain(Site $site): array {
        try {
            $host = parse_url($site->url, PHP_URL_HOST);
            $host = preg_replace('/^www\./', '', $host);

            $whois = Factory::get()->createWhois();
            $info = $whois->loadDomainInfo($host);

            if (!$info) {
                return ['success' => false, 'error' => 'Aucune info WHOIS'];
            }

            $expiresAt = $info->expirationDate
                ? \Carbon\Carbon::createFromTimestamp($info->expirationDate)->toDateString()
                : null;
            $createdAt = $info->creationDate
                ? \Carbon\Carbon::createFromTimestamp($info->creationDate)->toDateString()
                : null;

            $daysLeft = $expiresAt
                ? (int) now()->diffInDays(\Carbon\Carbon::parse($expiresAt), false)
                : null;

            // Stocke dans whois_infos
            WhoisInfo::updateOrCreate(
                ['site_id' => $site->id],
                [
                    'registrar'             => $info->registrar ?? 'Inconnu',
                    'registered_at'         => $createdAt,
                    'expires_at'            => $expiresAt,
                    'domain_days_remaining' => $daysLeft,
                    'checked_at'            => now(),
                ]
            );

            // Stocke aussi dans sites pour compatibilite
            $site->domain_registrar  = $info->registrar ?? 'Inconnu';
            $site->domain_expires_at = $expiresAt;
            $site->domain_created_at = $createdAt;
            $site->whois_checked_at  = now();
            $site->save();

            // Alerte si < 30 jours
            if ($daysLeft !== null && $daysLeft <= 30 && $daysLeft >= 0) {
                $alerteService = new AlerteService();
                $alerteService->sendDomainAlert($site, $daysLeft);
            }

            return ['success' => true, 'expires_at' => $expiresAt, 'days_left' => $daysLeft];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}