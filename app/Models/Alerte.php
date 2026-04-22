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
}