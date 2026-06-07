<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // ═══ Sécurité : admin uniquement ═══
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Accès réservé à l\'administrateur.');
        }

        // ═══ Base query avec filtres ═══
        $query = AuditLog::with('user')->latest();

        // Filtre par utilisateur
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtre par action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filtre par recherche libre
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('user_name', 'LIKE', "%{$search}%")
                  ->orWhere('model_name', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // ═══ Statistiques globales (sans filtre) ═══
        $stats = [
            'total'     => AuditLog::count(),
            'today'     => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'failures'  => AuditLog::where('status', 'failure')->count(),
        ];

        // ═══ Catégories disponibles ═══
        $categories = AuditService::CATEGORIES;

        // ═══ Actions disponibles ═══
        $actions = AuditService::ACTION_LABELS;

        // ═══ Liste des utilisateurs pour le filtre ═══
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('audit.index', compact(
            'logs', 'stats', 'categories', 'actions', 'users'
        ));
    }

    public function show(AuditLog $auditLog)
    {
        // ═══ Sécurité : admin uniquement ═══
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        return response()->json([
            'id'          => $auditLog->id,
            'user_name'   => $auditLog->user_name,
            'user_role'   => $auditLog->user_role,
            'action'      => $auditLog->action,
            'action_label' => AuditService::ACTION_LABELS[$auditLog->action] ?? $auditLog->action,
            'category'    => $auditLog->category,
            'category_label' => AuditService::CATEGORIES[$auditLog->category] ?? $auditLog->category,
            'description' => $auditLog->description,
            'model_type'  => $auditLog->model_type ? class_basename($auditLog->model_type) : null,
            'model_id'    => $auditLog->model_id,
            'model_name'  => $auditLog->model_name,
            'old_values'  => $auditLog->old_values,
            'new_values'  => $auditLog->new_values,
            'ip_address'  => $auditLog->ip_address,
            'user_agent'  => $auditLog->user_agent,
            'status'      => $auditLog->status,
            'created_at'  => $auditLog->created_at->format('d/m/Y H:i:s'),
        ]);
    }
}