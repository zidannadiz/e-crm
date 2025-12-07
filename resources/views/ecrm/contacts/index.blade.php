@extends('layouts.app')

@section('title', 'Contacts - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">History Kontak</h1>
        <a href="{{ route('ecrm.contacts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Kontak
        </a>
    </div>

    <div class="bg-white rounded-lg shadow mb-4 p-4">
        <form method="GET" class="flex gap-4">
            <select name="client_id" class="border rounded px-4 py-2">
                <option value="">Semua Client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nama }}
                    </option>
                @endforeach
            </select>
            <select name="project_id" class="border rounded px-4 py-2">
                <option value="">Semua Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->nama_proyek }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subjek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client/Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contacts as $contact)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $contact->tanggal_kontak->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $contact->subjek }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($contact->pesan, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($contact->client)
                                <div class="text-sm">{{ $contact->client->nama }}</div>
                            @endif
                            @if($contact->project)
                                <div class="text-xs text-gray-500">{{ $contact->project->nama_proyek }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                {{ ucfirst($contact->tipe) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $contact->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('ecrm.contacts.show', $contact) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data kontak</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>
@endsection

