@extends('layouts.app')

@section('title', 'Detail Project - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Project</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.projects.edit', $project) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Edit
            </a>
            <a href="{{ route('ecrm.contacts.create', ['project_id' => $project->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Tambah Kontak
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Informasi Project</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Nama Project</label>
                        <p class="font-semibold">{{ $project->nama_proyek }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Client</label>
                        <p class="font-semibold">
                            <a href="{{ route('ecrm.clients.show', $project->client) }}" class="text-blue-600 hover:underline">
                                {{ $project->client->nama }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Jenis Desain</label>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $project->jenis_desain)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <span class="px-2 py-1 text-xs rounded {{ 
                            $project->status === 'completed' ? 'bg-green-100 text-green-800' : 
                            ($project->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                            ($project->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    @if($project->budget)
                    <div>
                        <label class="text-sm text-gray-500">Budget</label>
                        <p class="font-semibold text-green-600">Rp {{ number_format($project->budget, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($project->deadline)
                    <div>
                        <label class="text-sm text-gray-500">Deadline</label>
                        <p class="font-semibold">{{ $project->deadline->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($project->tanggal_mulai)
                    <div>
                        <label class="text-sm text-gray-500">Tanggal Mulai</label>
                        <p class="font-semibold">{{ $project->tanggal_mulai->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($project->tanggal_selesai)
                    <div>
                        <label class="text-sm text-gray-500">Tanggal Selesai</label>
                        <p class="font-semibold">{{ $project->tanggal_selesai->format('d M Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm text-gray-500">Revision Count</label>
                        <p class="font-semibold">{{ $project->revision_count }}</p>
                    </div>
                    @if($project->deskripsi)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Deskripsi</label>
                        <p class="font-semibold">{{ $project->deskripsi }}</p>
                    </div>
                    @endif
                    @if($project->catatan)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Catatan</label>
                        <p class="font-semibold">{{ $project->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">History Kontak ({{ $project->contacts->count() }})</h2>
                @if($project->contacts->count() > 0)
                    <div class="space-y-4">
                        @foreach($project->contacts as $contact)
                            <div class="border-b pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold">{{ $contact->subjek }}</h3>
                                        <p class="text-sm text-gray-600">{{ Str::limit($contact->pesan, 100) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $contact->tipe }} - {{ $contact->tanggal_kontak->format('d M Y H:i') }}
                                            @if($contact->user)
                                                - {{ $contact->user->name }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada kontak</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Progress</h2>
                <div class="mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Progress</span>
                        <span class="text-sm font-semibold">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('ecrm.projects.edit', $project) }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-center">
                        Edit Project
                    </a>
                    <a href="{{ route('ecrm.contacts.create', ['project_id' => $project->id]) }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                        Tambah Kontak
                    </a>
                    <a href="{{ route('ecrm.clients.show', $project->client) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
                        Lihat Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

