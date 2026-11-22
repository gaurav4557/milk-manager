<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],

            'mobile' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
                'unique:users,mobile',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],

            'registration_code' => ['required', 'string'],
        ]);

        $correctCode = (string) config(
            'milkmanager.farmer_registration_code'
        );

        if (
            $correctCode === '' ||
            ! hash_equals($correctCode, $validated['registration_code'])
        ) {
            return back()
                ->withErrors([
                    'registration_code' => 'Invalid registration code.',
                ])
                ->withInput($request->except(
                    'password',
                    'password_confirmation'
                ));
        }

        $farmer = User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($farmer);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Your account has been created successfully!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'mobile' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()
            ->withErrors([
                'mobile' => 'Invalid mobile number or password.',
            ])
            ->onlyInput('mobile');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have logged out successfully.');
    }
}
