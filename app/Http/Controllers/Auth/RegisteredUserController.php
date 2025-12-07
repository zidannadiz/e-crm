<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ecrm\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'telepon' => ['nullable', 'string', 'max:20'],
            'tipe' => ['required', 'in:individu,perusahaan'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Default role untuk register adalah client
        ]);

        // Create client profile automatically
        $client = Client::create([
            'nama' => $request->name,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'tipe' => $request->tipe,
            'status' => 'aktif', // Auto-activate new clients
        ]);

        // Link user to client profile
        $user->update(['client_id' => $client->id]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('ecrm.dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di e-CRM.');
    }
}
