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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
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
}