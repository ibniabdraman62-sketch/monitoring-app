<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // Relations
    public function sites() {
        return $this->hasMany(\App\Models\Site::class);
    }

    // Helpers rôles
    public function isSuperAdmin(): bool {
        return $this->role === 'super_admin';
    }

    public function isAgent(): bool {
        return $this->role === 'agent';
    }

//     public function sites(){
//     return $this->hasMany(Site::class);
// }

public function alerteLectures()
{
    return $this->hasMany(AlerteLecture::class);
}

public function alertesLues()
{
    return $this->belongsToMany(\App\Models\Alerte::class, 'alerte_lectures')
        ->withPivot('lu_at')
        ->withTimestamps();
}

public function notificationsNonLues()
{
    return \App\Models\Alerte::visiblesPour($this)
        ->whereNotIn('id', $this->alertesLues()->pluck('alertes.id'));
}

public function compterNotificationsNonLues(): int
{
    return $this->notificationsNonLues()->count();
}


}