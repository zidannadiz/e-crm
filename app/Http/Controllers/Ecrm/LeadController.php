<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with('assignedUser');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telepon', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $leads = $query->latest()->paginate(15);
        $users = User::all();

        return view('ecrm.leads.index', compact('leads', 'users'));
    }

    public function create()
    {
        $users = User::all();
        return view('ecrm.leads.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'sumber' => 'nullable|string|max:255',
            'kebutuhan' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,quotation,converted,lost',
            'estimated_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'catatan' => 'nullable|string',
        ]);

        Lead::create($validated);

        return redirect()->route('ecrm.leads.index')
            ->with('success', 'Lead berhasil ditambahkan');
    }

    public function show(Lead $lead)
    {
        $lead->load('assignedUser');
        return view('ecrm.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $users = User::all();
        return view('ecrm.leads.edit', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'sumber' => 'nullable|string|max:255',
            'kebutuhan' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,quotation,converted,lost',
            'estimated_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'catatan' => 'nullable|string',
        ]);

        $lead->update($validated);

        return redirect()->route('ecrm.leads.index')
            ->with('success', 'Lead berhasil diperbarui');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('ecrm.leads.index')
            ->with('success', 'Lead berhasil dihapus');
    }

    public function convert(Lead $lead)
    {
        // Convert lead to client
        $client = Client::create([
            'nama' => $lead->nama,
            'email' => $lead->email ?? 'noemail@example.com',
            'telepon' => $lead->telepon,
            'tipe' => 'individu',
            'status' => 'prospek',
            'catatan' => 'Dikonversi dari Lead: ' . $lead->kebutuhan,
        ]);

        $lead->update(['status' => 'converted']);

        return redirect()->route('ecrm.clients.show', $client)
            ->with('success', 'Lead berhasil dikonversi menjadi Client');
    }
}

