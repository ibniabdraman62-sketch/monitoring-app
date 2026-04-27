<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WhoisInfo extends Model {
    public $timestamps = false;
    protected $fillable = [
        'site_id','registrar','registered_at',
        'expires_at','domain_days_remaining','checked_at'
    ];
    protected $casts = ['checked_at' => 'datetime'];

    public function site() {
        return $this->belongsTo(Site::class);
    }
}