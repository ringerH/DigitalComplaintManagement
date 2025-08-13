<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        config(['session.cookie' => 'web_session']);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            if ($user->usertype === 'admin') {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Admin credentials are not valid here. Use /admin/login.',
                ]);
            }
            $request->session()->regenerate();
            return $user->college_id && $user->usertype
                ? redirect()->route('home')
                : redirect()->route('select-college');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        Auth::guard('web')->login($user);
        return redirect()->route('select-college');
    }

    public function logout(Request $request)
    {
        config(['session.cookie' => 'web_session']);
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}