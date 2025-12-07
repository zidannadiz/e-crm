<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'admin');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $admins = $query->latest()->paginate(15);

        return view('ecrm.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('ecrm.admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('ecrm.admins.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    /**
     * Display the specified admin.
     */
    public function show(User $admin)
    {
        // Ensure it's an admin user
        if ($admin->role !== 'admin') {
            abort(404);
        }

        return view('ecrm.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin)
    {
        // Ensure it's an admin user
        if ($admin->role !== 'admin') {
            abort(404);
        }

        // Prevent editing super admin
        if ($admin->email === 'admin@ecrm.com') {
            return redirect()->route('ecrm.admins.index')
                ->with('error', 'Tidak dapat mengubah Super Admin');
        }

        return view('ecrm.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $admin)
    {
        // Ensure it's an admin user
        if ($admin->role !== 'admin') {
            abort(404);
        }

        // Prevent editing super admin
        if ($admin->email === 'admin@ecrm.com') {
            return redirect()->route('ecrm.admins.index')
                ->with('error', 'Tidak dapat mengubah Super Admin');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        return redirect()->route('ecrm.admins.index')
            ->with('success', 'Admin berhasil diperbarui');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin)
    {
        // Ensure it's an admin user
        if ($admin->role !== 'admin') {
            abort(404);
        }

        // Prevent deleting super admin
        if ($admin->email === 'admin@ecrm.com') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus Super Admin');
        }

        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $admin->delete();

        return redirect()->route('ecrm.admins.index')
            ->with('success', 'Admin berhasil dihapus');
    }
}

