<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlerteLecture extends Model
{
    protected $table = 'alerte_lectures';

    protected $fillable = ['user_id', 'alerte_id', 'lu_at'];

    protected $casts = [
        'lu_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alerte()
    {
        return $this->belongsTo(Alerte::class);
    }
    public function site() {
        return $this->belongsTo(Site::class);
    }
}