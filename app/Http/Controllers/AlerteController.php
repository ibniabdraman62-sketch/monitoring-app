<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AlerteController extends Controller {
    public function index() {
        return view('alertes.index');
    }
}