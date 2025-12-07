@extends('layouts.app')

@section('title', 'Detail Kontak - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Kontak</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.contacts.edit', $contact) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Edit
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Informasi Kontak</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-500">Subjek</label>
                <p class="font-semibold">{{ $contact->subjek }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tipe</label>
                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                    {{ ucfirst($contact->tipe) }}
                </span>
            </div>
            @if($contact->client)
            <div>
                <label class="text-sm text-gray-500">Client</label>
                <p class="font-semibold">
                    <a href="{{ route('ecrm.clients.show', $contact->client) }}" class="text-blue-600 hover:underline">
                        {{ $contact->client->nama }}
                    </a>
                </p>
            </div>
            @endif
            @if($contact->project)
            <div>
                <label class="text-sm text-gray-500">Project</label>
                <p class="font-semibold">
                    <a href="{{ route('ecrm.projects.show', $contact->project) }}" class="text-blue-600 hover:underline">
                        {{ $contact->project->nama_proyek }}
                    </a>
                </p>
            </div>
            @endif
            <div>
                <label class="text-sm text-gray-500">Arah</label>
                <span class="px-2 py-1 text-xs rounded {{ $contact->arah === 'inbound' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($contact->arah) }}
                </span>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tanggal Kontak</label>
                <p class="font-semibold">{{ $contact->tanggal_kontak->format('d M Y H:i') }}</p>
            </div>
            @if($contact->user)
            <div>
                <label class="text-sm text-gray-500">User</label>
                <p class="font-semibold">{{ $contact->user->name }}</p>
            </div>
            @endif
            <div class="col-span-2">
                <label class="text-sm text-gray-500">Pesan</label>
                <div class="mt-2 p-4 bg-gray-50 rounded">
                    <p class="whitespace-pre-wrap">{{ $contact->pesan }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('ecrm.contacts.edit', $contact) }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
            Edit Kontak
        </a>
        <a href="{{ route('ecrm.contacts.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
            Kembali
        </a>
    </div>
</div>
@endsection

