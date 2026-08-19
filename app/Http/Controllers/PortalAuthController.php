<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Sign-in for the employee-facing BLIP portal. Account creation lives in Livewire\Portal\Register. */
class PortalAuthController extends Controller
{
    public function show(): View
    {
        return view('portal.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::guard('employee')->attempt($credentials)) {
            return back()->withErrors(['email' => 'Those details don\'t match a portal account.'])->withInput(['email' => $credentials['email']]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('portal.passport'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
