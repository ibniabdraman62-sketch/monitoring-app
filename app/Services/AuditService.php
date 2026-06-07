<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Catégories d'actions disponibles.
     */
    const CATEGORIES = [
        'auth'    => 'Authentification',
        'site'    => 'Sites',
        'user'    => 'Utilisateurs',
        'report'  => 'Rapports',
        'profile' => 'Profil',
        'system'  => 'Système',
    ];

    /**
     * Libellés humains des actions.
     */
    const ACTION_LABELS = [
        // Auth
        'login'                  => 'Connexion',
        'login_failed'           => 'Échec de connexion',
        'logout'                 => 'Déconnexion',
        'password_reset_request' => 'Demande de réinitialisation',
        'password_changed'       => 'Mot de passe modifié',
        
        // Site
        'site_created'           => 'Création de site',
        'site_updated'           => 'Modification de site',
        'site_deleted'           => 'Suppression de site',
        'site_activated'         => 'Activation de site',
        'site_deactivated'       => 'Désactivation de site',
        'site_check_now'         => 'Vérification manuelle',
        
        // User
        'user_created'           => 'Création d\'utilisateur',
        'user_updated'           => 'Modification d\'utilisateur',
        'user_deleted'           => 'Suppression d\'utilisateur',
        'user_activated'         => 'Activation d\'utilisateur',
        'user_deactivated'       => 'Désactivation d\'utilisateur',
        'password_reset_admin'   => 'Réinitialisation mot de passe (admin)',
        
        // Report
        'report_generated'       => 'Génération de rapport',
        'report_downloaded'      => 'Téléchargement de rapport',
        'report_emailed'         => 'Envoi de rapport par email',
        
        // Profile
        'profile_updated'        => 'Mise à jour du profil',
    ];

    /**
     * Enregistre une action dans le journal d'audit.
     *
     * @param string $action      Code de l'action (ex: site_created)
     * @param string $category    Catégorie (auth, site, user, report, profile, system)
     * @param string $description Texte lisible décrivant l'action
     * @param mixed  $model       Modèle Eloquent concerné (optionnel)
     * @param array  $oldValues   Anciennes valeurs (pour les UPDATE)
     * @param array  $newValues   Nouvelles valeurs (pour les UPDATE)
     * @param string $status      success | failure
     * @param array  $userData    Pour les actions sans user authentifié (ex: login_failed)
     */
    public static function log(
        string $action,
        string $category,
        string $description,
        $model = null,
        array $oldValues = [],
        array $newValues = [],
        string $status = 'success',
        array $userData = []
    ): ?AuditLog {
        try {
            $user = Auth::user();
            
            return AuditLog::create([
                'user_id'     => $user?->id ?? ($userData['user_id'] ?? null),
                'user_name'   => $user?->name ?? ($userData['user_name'] ?? '[Anonyme]'),
                'user_role'   => $user?->role ?? ($userData['user_role'] ?? null),
                'action'      => $action,
                'category'    => $category,
                'description' => $description,
                'model_type'  => $model ? get_class($model) : null,
                'model_id'    => $model?->id,
                'model_name'  => self::extractModelName($model),
                'old_values'  => empty($oldValues) ? null : $oldValues,
                'new_values'  => empty($newValues) ? null : $newValues,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
                'status'      => $status,
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            // Ne jamais bloquer une action métier à cause d'un échec d'audit
            \Log::error('AuditService::log failed', [
                'error'  => $e->getMessage(),
                'action' => $action,
            ]);
            return null;
        }
    }

    /**
     * Extrait un nom lisible du modèle pour les logs.
     */
    private static function extractModelName($model): ?string
    {
        if (!$model) return null;
        
        // Essaie plusieurs attributs courants pour trouver un nom
        foreach (['client_name', 'name', 'email', 'url', 'title'] as $attr) {
            if (isset($model->{$attr}) && !empty($model->{$attr})) {
                return (string) $model->{$attr};
            }
        }
        
        return null;
    }

    /**
     * Helper : log d'une création.
     */
    public static function logCreated($model, string $category, ?string $description = null): ?AuditLog
    {
        $modelName = self::extractModelName($model);
        $type = class_basename($model);
        
        return self::log(
            action:      strtolower($type) . '_created',
            category:    $category,
            description: $description ?? "Création de {$type}" . ($modelName ? " « {$modelName} »" : ''),
            model:       $model,
            newValues:   $model->getAttributes()
        );
    }

    /**
     * Helper : log d'une modification.
     */
    public static function logUpdated($model, array $original, string $category, ?string $description = null): ?AuditLog
    {
        $modelName = self::extractModelName($model);
        $type = class_basename($model);
        
        // Ne garder que les attributs modifiés
        $changes = $model->getChanges();
        $oldFiltered = array_intersect_key($original, $changes);
        
        // Masquer les mots de passe
        if (isset($oldFiltered['password'])) $oldFiltered['password'] = '***';
        if (isset($changes['password']))     $changes['password']     = '***';
        if (isset($oldFiltered['remember_token'])) unset($oldFiltered['remember_token']);
        if (isset($changes['remember_token']))     unset($changes['remember_token']);
        
        return self::log(
            action:      strtolower($type) . '_updated',
            category:    $category,
            description: $description ?? "Modification de {$type}" . ($modelName ? " « {$modelName} »" : ''),
            model:       $model,
            oldValues:   $oldFiltered,
            newValues:   $changes
        );
    }

    /**
     * Helper : log d'une suppression.
     */
    public static function logDeleted($model, string $category, ?string $description = null): ?AuditLog
    {
        $modelName = self::extractModelName($model);
        $type = class_basename($model);
        
        return self::log(
            action:      strtolower($type) . '_deleted',
            category:    $category,
            description: $description ?? "Suppression de {$type}" . ($modelName ? " « {$modelName} »" : ''),
            model:       $model,
            oldValues:   $model->getAttributes()
        );
    }
}