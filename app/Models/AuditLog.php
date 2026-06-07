<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * Pas de updated_at, seulement created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'category',
        'description',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'status',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relation : utilisateur ayant effectué l'action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => '[Utilisateur supprimé]',
            'email' => '—',
        ]);
    }

    /**
     * Badge HTML coloré selon la catégorie.
     */
    public function getCategoryBadgeAttribute(): string
    {
        $colors = [
            'auth'    => 'badge-warning',
            'site'    => 'badge-info',
            'user'    => 'badge-purple',
            'report'  => 'badge-success',
            'profile' => 'badge-secondary',
            'system'  => 'badge-dark',
        ];

        return $colors[$this->category] ?? 'badge-secondary';
    }

    /**
     * Icône Font Awesome selon la catégorie.
     */
    public function getCategoryIconAttribute(): string
    {
        $icons = [
            'auth'    => 'fa-lock',
            'site'    => 'fa-globe',
            'user'    => 'fa-user',
            'report'  => 'fa-file-pdf',
            'profile' => 'fa-user-pen',
            'system'  => 'fa-gear',
        ];

        return $icons[$this->category] ?? 'fa-circle-info';
    }

    /**
     * Couleur du statut.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status === 'success' ? 'text-success' : 'text-danger';
    }
}