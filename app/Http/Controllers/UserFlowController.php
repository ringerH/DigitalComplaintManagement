<?php
namespace App\Http\Controllers;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function selectCollege()
    {
        if (Auth::guard('web')->user()->college_id) {
            return redirect()->route('select-role');
        }
        $colleges = College::all();
        return view('auth.select-college', compact('colleges'));
    }

    public function storeCollege(Request $request)
    {
        $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
        ]);

        Auth::guard('web')->user()->update(['college_id' => $request->college_id]);
        return redirect()->route('select-role');
    }

    public function selectRole()
    {
        if (Auth::guard('web')->user()->usertype) {
            return redirect()->route('home');
        }
        return view('auth.select-role');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'usertype' => ['required', 'in:student,hospital_patient'],
        ]);

        Auth::guard('web')->user()->update(['usertype' => $request->usertype]);
        return redirect()->route('home');
    }
}