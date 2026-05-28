<?php
namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'client') {
            $incidents = Incident::whereHas('site', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with('site')->latest('started_at')->paginate(20);
        } else {
            $incidents = Incident::with('site')->latest('started_at')->paginate(20);
        }

        return view('incidents.index', compact('incidents'));
    }
}