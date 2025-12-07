<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CustomerServiceController extends Controller
{
    /**
     * Display a listing of customer service users.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'cs');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $customerServices = $query->latest()->paginate(15);

        return view('ecrm.customer-services.index', compact('customerServices'));
    }

    /**
     * Show the form for creating a new customer service.
     */
    public function create()
    {
        return view('ecrm.customer-services.create');
    }

    /**
     * Store a newly created customer service in storage.
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
            'role' => 'cs',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('ecrm.customer-services.index')
            ->with('success', 'Customer Service berhasil ditambahkan');
    }

    /**
     * Display the specified customer service.
     */
    public function show(User $customerService)
    {
        // Ensure it's a CS user
        if ($customerService->role !== 'cs') {
            abort(404);
        }

        return view('ecrm.customer-services.show', compact('customerService'));
    }

    /**
     * Show the form for editing the specified customer service.
     */
    public function edit(User $customerService)
    {
        // Ensure it's a CS user
        if ($customerService->role !== 'cs') {
            abort(404);
        }

        return view('ecrm.customer-services.edit', compact('customerService'));
    }

    /**
     * Update the specified customer service in storage.
     */
    public function update(Request $request, User $customerService)
    {
        // Ensure it's a CS user
        if ($customerService->role !== 'cs') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($customerService->id)],
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

        $customerService->update($updateData);

        return redirect()->route('ecrm.customer-services.index')
            ->with('success', 'Customer Service berhasil diperbarui');
    }

    /**
     * Remove the specified customer service from storage.
     */
    public function destroy(User $customerService)
    {
        // Ensure it's a CS user
        if ($customerService->role !== 'cs') {
            abort(404);
        }

        // Prevent deleting yourself
        if ($customerService->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $customerService->delete();

        return redirect()->route('ecrm.customer-services.index')
            ->with('success', 'Customer Service berhasil dihapus');
    }
}

