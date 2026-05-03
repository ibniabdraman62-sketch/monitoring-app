<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AgentController extends Controller {
    public function index() {
        return view('admin.agents');
    }

    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => 'agent',
            'is_active' => true,
        ]);
        return back()->with('success', "Agent {$request->name} créé avec succès !");
    }

    // 
    
    public function toggle(User $user) {
    // Mise à jour directe en SQL — bypass le fillable
    User::where('id', $user->id)->update([
        'is_active' => $user->is_active ? 0 : 1
    ]);

    // Recharge depuis la BDD pour avoir la vraie valeur
    $user->refresh();
    $status = $user->is_active ? 'activé' : 'désactivé';

    return back()->with('success', "Compte de {$user->name} {$status} avec succès !");
}
}