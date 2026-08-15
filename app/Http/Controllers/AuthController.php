<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        if (! hash_equals((string) config('app.admin_password'), (string) $request->input('password'))) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput(['password' => '']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->to($request->session()->pull('intended', route('dashboard.exec')));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
