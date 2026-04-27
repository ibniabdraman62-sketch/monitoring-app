<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CronLog extends Model {
    public $timestamps = false;
    protected $fillable = ['command','status','duration_ms','sites_checked','errors_count','error_message','executed_at'];
    protected $casts = ['executed_at' => 'datetime'];
}