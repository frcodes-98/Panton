<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function show(): View
    {
        return view('landing');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        if ($data['username'] !== 'admin' || $data['password'] !== 'admin') {
            throw ValidationException::withMessages([
                'username' => 'The username or password is incorrect.',
            ]);
        }

        $request->session()->put('demo_user', 'admin');

        return redirect()->route('desktop');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('demo_user');

        return redirect()->route('landing');
    }
}
