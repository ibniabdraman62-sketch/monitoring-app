<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model {
    protected $fillable = [
        'site_id','period_start','period_end',
        'uptime_pct','incidents_count','avg_response_ms',
        'pdf_path','sent_to','generated_at'
    ];
    protected $casts = ['generated_at' => 'datetime'];

    public function site() {
        return $this->belongsTo(Site::class);
    }
}