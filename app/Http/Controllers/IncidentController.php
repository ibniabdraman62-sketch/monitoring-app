<?php
namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::whereHas('site', function($q) {
            $q->where('user_id', Auth::id());
        })->with('site')->latest('started_at')->paginate(20);

        return view('incidents.index', compact('incidents'));
    }
}