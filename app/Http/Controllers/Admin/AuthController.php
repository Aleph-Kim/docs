<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $hash = (string) config('admin.password_hash');

        $valid = hash_equals((string) config('admin.id'), $credentials['id'])
            && $hash !== ''
            && Hash::check($credentials['password'], $hash);

        if (! $valid) {
            return back()
                ->withInput($request->only('id'))
                ->withErrors(['id' => '아이디 또는 비밀번호가 올바르지 않습니다.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->intended(route('admin.visuals.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('visuals.index');
    }
}
