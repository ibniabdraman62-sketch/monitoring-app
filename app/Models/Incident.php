<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model {
    use HasFactory;

    protected $fillable = [
        'site_id', 'started_at', 'resolved_at',
        'type', 'duration_min'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }
    public function alertes() {
        return $this->hasMany(Alerte::class);
    }

    public function resolve(): void
{
    $this->update([
        'resolved_at' => now(),
        'is_resolved'  => true,
        'duration_min' => $this->started_at
            ? (int) $this->started_at->diffInMinutes(now())
            : 0,
    ]);
}

public function getDuration(): int
{
    if ($this->resolved_at && $this->started_at)
        return (int) $this->started_at->diffInMinutes($this->resolved_at);
    return $this->started_at
        ? (int) $this->started_at->diffInMinutes(now())
        : 0;
}
}