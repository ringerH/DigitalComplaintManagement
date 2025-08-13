<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'non-admin']);
    }

    public function index()
    {
        config(['session.cookie' => 'web_session']);
        $complaints = Auth::guard('web')->user()->complaints()->latest()->get();
        return view('home', compact('complaints'));
    }
}