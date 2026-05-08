<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model {
    use HasFactory;

    protected $fillable = [
    'site_id', 'checked_at', 'http_code',
    'response_time_ms', 'ssl_valid',
    'ssl_expires_at', 'ssl_days_remaining', 'is_up'
];

    protected $casts = [
        'checked_at' => 'datetime',
        'ssl_expires_at' => 'date',
        'is_up' => 'boolean',
        'ssl_valid' => 'boolean',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function isAnomaly(): bool
{
    $threshold = $this->site->threshold_ms ?? 2000;
    return !$this->is_up || $this->response_time_ms > $threshold;
}

}