@extends('layouts.app')

@section('title', 'Detail Klien - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6 animate-fade-in">
        <h1 class="text-3xl font-bold">Detail Klien</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.clients.edit', $client) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                Ubah
            </a>
            <a href="{{ route('ecrm.contacts.create', ['client_id' => $client->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                + Tambah Kontak
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6 animate-slide-up hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-bold mb-4">Informasi Client</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Nama</label>
                        <p class="font-semibold">{{ $client->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-semibold">{{ $client->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Telepon</label>
                        <p class="font-semibold">{{ $client->telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Perusahaan</label>
                        <p class="font-semibold">{{ $client->perusahaan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Tipe</label>
                        <p class="font-semibold">{{ ucfirst($client->tipe) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <span class="px-2 py-1 text-xs rounded transition-all duration-200 hover:scale-110 {{ 
                            $client->status === 'aktif' ? 'bg-green-100 text-green-800' : 
                            ($client->status === 'prospek' ? 'bg-yellow-100 text-yellow-800' : 
                            ($client->status === 'blacklist' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) 
                        }}">
                            {{ ucfirst($client->status) }}
                        </span>
                    </div>
                    @if($client->alamat)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Alamat</label>
                        <p class="font-semibold">{{ $client->alamat }}</p>
                    </div>
                    @endif
                    @if($client->catatan)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Catatan</label>
                        <p class="font-semibold">{{ $client->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 animate-fade-in hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-bold mb-4">Projects ({{ $client->projects->count() }})</h2>
                @if($client->projects->count() > 0)
                    <div class="space-y-4">
                        @foreach($client->projects as $index => $project)
                            <div class="border-b pb-4 animate-slide-in-left hover:bg-gray-50 transition-colors duration-200 rounded px-2 py-1" style="animation-delay: {{ $index * 0.1 }}s;">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold">{{ $project->nama_proyek }}</h3>
                                        <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $project->jenis_desain)) }}</p>
                                        @if($project->budget)
                                            <p class="text-sm text-gray-600">Budget: Rp {{ number_format($project->budget, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 transition-all duration-200 hover:scale-110">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 animate-fade-in">Belum ada project</p>
                @endif
                <a href="{{ route('ecrm.projects.create', ['client_id' => $client->id]) }}" class="mt-4 text-blue-600 hover:underline transition-all duration-200 hover:text-blue-800 inline-block">+ Tambah Project</a>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6 animate-slide-up delay-100 hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-bold mb-4">Statistik</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500">Total Projects</label>
                        <p class="text-2xl font-bold">{{ $client->total_projects }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Revenue</label>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($client->total_revenue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 animate-slide-up delay-200 hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-bold mb-4">History Kontak</h2>
                @if($client->contacts->count() > 0)
                    <div class="space-y-3">
                        @foreach($client->contacts->take(5) as $index => $contact)
                            <div class="border-b pb-3 animate-slide-in-left hover:bg-gray-50 transition-colors duration-200 rounded px-2 py-1" style="animation-delay: {{ $index * 0.1 }}s;">
                                <p class="font-semibold text-sm">{{ $contact->subjek }}</p>
                                <p class="text-xs text-gray-500">{{ $contact->tipe }} - {{ $contact->tanggal_kontak->format('d M Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm animate-fade-in">Belum ada kontak</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

