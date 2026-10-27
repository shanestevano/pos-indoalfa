<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_code';

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => 'Akun Anda dinonaktifkan. Hubungi Admin/Manager.',
                ]);
            }

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'login' => 'Kombinasi ID Kasir/Email dan Password salah.',
        ]);
    }

    public function quickLogin(string $role): RedirectResponse
    {
        $code = match ($role) {
            'admin' => 'ADM001',
            'cashier' => 'KSR001',
            'warehouse' => 'GDG001',
            default => null,
        };

        if (!$code) {
            return redirect()->route('login');
        }

        $user = User::where('employee_code', $code)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isWarehouse()) {
            return redirect()->intended(route('warehouse.index'));
        }

        return redirect()->intended(route('pos.index'));
    }
}
