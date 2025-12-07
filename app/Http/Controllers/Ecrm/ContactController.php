<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Contact;
use App\Models\Ecrm\Project;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['client', 'project', 'user']);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $contacts = $query->latest('tanggal_kontak')->paginate(15);
        $clients = Client::all();
        $projects = Project::all();

        return view('ecrm.contacts.index', compact('contacts', 'clients', 'projects'));
    }

    public function create(Request $request)
    {
        $clients = Client::all();
        $projects = Project::all();
        $clientId = $request->client_id;
        $projectId = $request->project_id;

        return view('ecrm.contacts.create', compact('clients', 'projects', 'clientId', 'projectId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:ecrm_clients,id',
            'project_id' => 'nullable|exists:ecrm_projects,id',
            'tipe' => 'required|in:call,email,meeting,whatsapp,lainnya',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string',
            'tanggal_kontak' => 'required|date',
            'arah' => 'required|in:inbound,outbound',
        ]);

        $validated['user_id'] = auth()->id();

        Contact::create($validated);

        // Redirect berdasarkan context
        if ($request->client_id) {
            return redirect()->route('ecrm.clients.show', $request->client_id)
                ->with('success', 'Kontak berhasil ditambahkan');
        } elseif ($request->project_id) {
            return redirect()->route('ecrm.projects.show', $request->project_id)
                ->with('success', 'Kontak berhasil ditambahkan');
        }

        return redirect()->route('ecrm.contacts.index')
            ->with('success', 'Kontak berhasil ditambahkan');
    }

    public function show(Contact $contact)
    {
        $contact->load(['client', 'project', 'user']);
        return view('ecrm.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $clients = Client::all();
        $projects = Project::all();
        return view('ecrm.contacts.edit', compact('contact', 'clients', 'projects'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:ecrm_clients,id',
            'project_id' => 'nullable|exists:ecrm_projects,id',
            'tipe' => 'required|in:call,email,meeting,whatsapp,lainnya',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string',
            'tanggal_kontak' => 'required|date',
            'arah' => 'required|in:inbound,outbound',
        ]);

        $contact->update($validated);

        return redirect()->route('ecrm.contacts.index')
            ->with('success', 'Kontak berhasil diperbarui');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('ecrm.contacts.index')
            ->with('success', 'Kontak berhasil dihapus');
    }
}

