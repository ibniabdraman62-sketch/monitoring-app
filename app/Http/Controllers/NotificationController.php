<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\AlerteLecture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            // ─── Filtrage RBAC défensif ───
            $query = Alerte::query()->latest('created_at');

            if ($user->role === 'client') {
                // Selon ton schéma, Alerte → Incident → Site → user_id
                $query->whereHas('incident.site', fn($q) => $q->where('user_id', $user->id));
            }

            $alertes = $query->with(['incident.site'])->take(10)->get();

            // ─── Compter les non lues ───
            $idsLues = AlerteLecture::where('user_id', $user->id)
                ->pluck('alerte_id')
                ->toArray();

            // Total non-lues (sur tout le périmètre visible, pas seulement les 10)
            $countQuery = Alerte::query();
            if ($user->role === 'client') {
                $countQuery->whereHas('incident.site', fn($q) => $q->where('user_id', $user->id));
            }
            $count = $countQuery->whereNotIn('id', $idsLues)->count();

            // ─── Formatage défensif ───
            $payload = $alertes->map(function ($a) use ($idsLues) {
                $site = optional($a->incident)->site;

                return [
                    'id'       => $a->id,
                    'site'     => $site->client_name ?? $site->name ?? $site->url ?? 'Site',
                    'type'     => $a->type ?? 'alerte',
                    'message'  => $this->buildMessage($a, $site),
                    'severite' => $this->guessSeverite($a),
                    'lue'      => in_array($a->id, $idsLues),
                    'date'     => optional($a->created_at)->diffForHumans() ?? '',
                    'url'      => route('alertes.index'),
                ];
            });

            return response()->json([
                'count'    => $count,
                'alertes'  => $payload,
            ]);

        } catch (\Throwable $e) {
            Log::error('[NotificationController] ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'count'   => 0,
                'alertes' => [],
                'error'   => config('app.debug') ? $e->getMessage() : 'Erreur serveur',
            ], 500);
        }
    }

    public function marquerLue(Alerte $alerte)
    {
        try {
            $user = Auth::user();
            AlerteLecture::firstOrCreate(
                ['user_id' => $user->id, 'alerte_id' => $alerte->id],
                ['lu_at' => now()]
            );
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('[NotificationController::marquerLue] ' . $e->getMessage());
            return response()->json(['ok' => false], 500);
        }
    }

    public function marquerToutesLues()
    {
        try {
            $user = Auth::user();

            $query = Alerte::query();
            if ($user->role === 'client') {
                $query->whereHas('incident.site', fn($q) => $q->where('user_id', $user->id));
            }

            $idsDejaLues = AlerteLecture::where('user_id', $user->id)->pluck('alerte_id')->toArray();
            $ids = $query->whereNotIn('id', $idsDejaLues)->pluck('id');

            if ($ids->isEmpty()) {
                return response()->json(['ok' => true, 'count' => 0]);
            }

            $now = now();
            $rows = $ids->map(fn($id) => [
                'user_id'    => $user->id,
                'alerte_id'  => $id,
                'lu_at'      => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            AlerteLecture::insert($rows);

            return response()->json(['ok' => true, 'count' => $ids->count()]);

        } catch (\Throwable $e) {
            Log::error('[NotificationController::marquerToutesLues] ' . $e->getMessage());
            return response()->json(['ok' => false], 500);
        }
    }

    // ─── HELPERS ───

    private function buildMessage($alerte, $site): string
    {
        $type = $alerte->type ?? '';
        $siteName = $site->client_name ?? $site->name ?? 'Site';

        return match (strtolower($type)) {
            'down', 'offline'    => "{$siteName} est hors ligne",
            'slow'               => "{$siteName} est lent",
            'resolved'           => "{$siteName} est de nouveau en ligne",
            'ssl'                => "Certificat SSL bientôt expiré pour {$siteName}",
            'domain', 'whois'    => "Nom de domaine bientôt expiré pour {$siteName}",
            default              => ucfirst($type ?: 'Alerte') . " — {$siteName}",
        };
    }

    private function guessSeverite($alerte): string
    {
        $type = strtolower($alerte->type ?? '');

        return match (true) {
            in_array($type, ['down', 'offline', 'domain', 'ssl']) => 'critique',
            in_array($type, ['slow', 'warning'])                  => 'avertissement',
            $type === 'resolved'                                  => 'info',
            default                                               => 'info',
        };
    }
}