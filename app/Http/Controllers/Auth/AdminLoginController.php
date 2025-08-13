<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        config(['session.cookie' => 'admin_session']);
        if (Auth::guard('admin')->check()) {
            return redirect($this->redirectTo);
        }
        return view('auth.admin-login');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = Auth::guard('admin')->user();
            if ($user->usertype !== 'admin') {
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'You are not authorized as an admin.',
                ]);
            }
            $request->session()->regenerate();
            return $this->sendLoginResponse($request);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect($this->redirectTo);
    }

    public function logout(Request $request)
    {
        config(['session.cookie' => 'admin_session']);
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}