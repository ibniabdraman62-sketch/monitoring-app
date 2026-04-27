<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'client_name', 'url',
        'frequency_min', 'response_threshold_ms',
        'ssl_check', 'is_active',
        'domain_registrar',
        'domain_expires_at',
        'domain_created_at',
        'whois_checked_at',
        'notify_emails',
        'client_email',
        'whois_check'
    ];
    public function whoisInfo() {
    return $this->hasOne(\App\Models\WhoisInfo::class);
}

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function verifications() {
        return $this->hasMany(Verification::class);
    }
    public function incidents() {
        return $this->hasMany(Incident::class);
    }
    public function rapports() {
        return $this->hasMany(Rapport::class);
    }
}