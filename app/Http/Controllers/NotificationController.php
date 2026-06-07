<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\AlerteLecture;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $alertes = Alerte::visiblesPour($user)
            ->with('site')
            ->latest()
            ->take(10)
            ->get();

        $idsLues = $user->alertesLues()->pluck('alertes.id')->toArray();

        return response()->json([
            'count' => $user->compterNotificationsNonLues(),
            'alertes' => $alertes->map(fn($a) => [
                'id'       => $a->id,
                'site'     => $a->site->nom ?? $a->site->url ?? 'Site',
                'type'     => $a->type ?? 'alerte',
                'message'  => $a->message ?? $a->type ?? 'Nouvelle alerte',
                'severite' => $a->severite ?? $a->niveau ?? 'info',
                'lue'      => in_array($a->id, $idsLues),
                'date'     => optional($a->created_at)->diffForHumans(),
                'url'      => route('alertes.index'),
            ]),
        ]);
    }

    public function marquerLue(Alerte $alerte)
    {
        $user = Auth::user();

        AlerteLecture::firstOrCreate(
            ['user_id' => $user->id, 'alerte_id' => $alerte->id],
            ['lu_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function marquerToutesLues()
    {
        $user = Auth::user();

        $ids = Alerte::visiblesPour($user)
            ->whereNotIn('id', $user->alertesLues()->pluck('alertes.id'))
            ->pluck('id');

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

        // Audit (facultatif — laisse si AuditService dispo, sinon supprime)
        if (class_exists(AuditService::class)) {
            app(AuditService::class)->log(
                'notifications.marquer_toutes_lues',
                "Marqué {$ids->count()} notification(s) comme lue(s)"
            );
        }

        return response()->json(['ok' => true, 'count' => $ids->count()]);
    }
}