<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('orders');

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

        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $clients = $query->latest()->paginate(15);

        // For CS, add statistics and use CS view
        if (auth()->user()->role === 'cs') {
            $totalClients = Client::count();
            $activeClients = Client::where('status', 'aktif')->count();
            $companyClients = Client::where('tipe', 'perusahaan')->count();
            
            return view('ecrm.clients.cs-index', compact('clients', 'totalClients', 'activeClients', 'companyClients'));
        }

        return view('ecrm.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('ecrm.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:ecrm_clients,email',
            'telepon' => 'nullable|string|max:20',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tipe' => 'required|in:individu,perusahaan',
            'status' => 'required|in:prospek,aktif,nonaktif,blacklist',
            'catatan' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('ecrm.clients.index')
            ->with('success', 'Client berhasil ditambahkan');
    }

    public function show(Client $client)
    {
        $client->load(['projects', 'contacts.user']);
        return view('ecrm.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('ecrm.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('ecrm_clients')->ignore($client->id)],
            'telepon' => 'nullable|string|max:20',
            'perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tipe' => 'required|in:individu,perusahaan',
            'status' => 'required|in:prospek,aktif,nonaktif,blacklist',
            'catatan' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('ecrm.clients.index')
            ->with('success', 'Client berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('ecrm.clients.index')
            ->with('success', 'Client berhasil dihapus');
    }
}

