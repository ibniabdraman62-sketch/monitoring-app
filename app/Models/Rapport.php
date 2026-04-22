<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model {
    use HasFactory;

    protected $fillable = [
        'site_id', 'period_start', 'period_end',
        'uptime_pct', 'generated_at', 'pdf_path'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'generated_at' => 'datetime',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }
}