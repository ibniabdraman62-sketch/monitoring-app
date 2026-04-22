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
}