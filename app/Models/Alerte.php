<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model {
    use HasFactory;

    protected $fillable = [
        'incident_id', 'sent_at',
        'type', 'email_to'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function incident() {
        return $this->belongsTo(Incident::class);
    }

    public function send(): bool
{
    try {
        \Mail::to($this->sent_to)
             ->send(new \App\Mail\AlerteIncidentMail($this));
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

public function lectures()
{
    return $this->hasMany(AlerteLecture::class);
}

public function lecteurs()
{
    return $this->belongsToMany(User::class, 'alerte_lectures')
        ->withPivot('lu_at')
        ->withTimestamps();
}

public function estLuePar(User $user): bool
{
    return $this->lectures()->where('user_id', $user->id)->exists();
}

/**
 * Scope RBAC : aligne sur la logique existante d'AlerteController.
 * Admin/Agent : tout le parc. Client : ses sites uniquement.
 */
public function scopeVisiblesPour($query, User $user)
{
    if (in_array($user->role, ['admin', 'agent'])) {
        return $query;
    }

    return $query->whereHas('site', fn($q) => $q->where('user_id', $user->id));
}

}