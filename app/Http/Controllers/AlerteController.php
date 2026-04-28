<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AlerteController extends Controller {
    // public function index() {
    //     return view('alertes.index');
    // }

    public function index() {
    $query = \App\Models\Alerte::with(['incident.site'])
        ->orderBy('sent_at','desc');

    if(request('site_id'))
        $query->whereHas('incident', fn($q) => $q->where('site_id', request('site_id')));
    if(request('type'))
        $query->where('type', request('type'));
    if(request('date_from'))
        $query->where('sent_at', '>=', request('date_from'));
    if(request('date_to'))
        $query->where('sent_at', '<=', request('date_to').' 23:59:59');
    if(request('search')) {
    $query->whereHas('incident.site', fn($q) =>
        $q->where('client_name', 'LIKE', '%'.request('search').'%')
          ->orWhere('url', 'LIKE', '%'.request('search').'%')
    );
}

    $alertes = $query->paginate(20);
    $sites = \App\Models\Site::where('user_id', auth()->id())->get();
    $stats = [
        'total'    => \App\Models\Alerte::count(),
        'down'     => \App\Models\Alerte::where('type','down')->count(),
        'slow'     => \App\Models\Alerte::where('type','slow')->count(),
        'resolved' => \App\Models\Alerte::where('type','resolved')->count(),
    ];
    return view('alertes.index', compact('alertes','sites','stats'));
}
}