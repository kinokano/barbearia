<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Processa o login e redireciona com base no role.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Credenciais inválidas.']);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    /**
     * Encerra a sessão do usuário.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redireciona o usuário para a rota correta com base no seu role_id.
     */
    private function redirectByRole($user): RedirectResponse
    {
        return match ($user->role_id) {
            1       => redirect()->route('admin.dashboard'),
            2       => redirect()->route('professional.agenda'),
            default => redirect()->route('client.booking'),
        };
    }
}
