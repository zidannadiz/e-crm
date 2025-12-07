<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_proyek', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('jenis_desain')) {
            $query->where('jenis_desain', $request->jenis_desain);
        }

        $projects = $query->latest()->paginate(15);
        $clients = Client::where('status', 'aktif')->get();

        return view('ecrm.projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('ecrm.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:ecrm_clients,id',
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_desain' => 'required|in:logo,branding,web_design,ui_ux,print_design,packaging,social_media,lainnya',
            'status' => 'required|in:quotation,approved,in_progress,review,revision,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('ecrm.projects.index')
            ->with('success', 'Project berhasil ditambahkan');
    }

    public function show(Project $project)
    {
        $project->load(['client', 'contacts.user']);
        return view('ecrm.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('ecrm.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:ecrm_clients,id',
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_desain' => 'required|in:logo,branding,web_design,ui_ux,print_design,packaging,social_media,lainnya',
            'status' => 'required|in:quotation,approved,in_progress,review,revision,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('ecrm.projects.index')
            ->with('success', 'Project berhasil diperbarui');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('ecrm.projects.index')
            ->with('success', 'Project berhasil dihapus');
    }
}

