@extends('layouts.app')

@section('title', 'Klien - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6 animate-fade-in">
        <h1 class="text-3xl font-bold">Klien</h1>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('ecrm.clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
            + Tambah Klien
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow mb-4 p-4 animate-slide-up">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, perusahaan..." class="flex-1 border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
            <select name="status" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-all duration-200 cursor-pointer shadow-sm focus:scale-105">
                <option value="">Semua Status</option>
                <option value="prospek" {{ request('status') == 'prospek' ? 'selected' : '' }}>Prospek</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="blacklist" {{ request('status') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clients as $index => $client)
                    <tr class="table-row-hover animate-slide-in-left hover:bg-gray-50 transition-all duration-200" style="animation-delay: {{ $index * 0.05 }}s; opacity: 0; animation-fill-mode: forwards;">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium">{{ $client->nama }}</div>
                            @if($client->perusahaan)
                                <div class="text-sm text-gray-500">{{ $client->perusahaan }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $client->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $client->telepon ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded transition-all duration-200 hover:scale-110 {{ 
                                $client->status === 'aktif' ? 'bg-green-100 text-green-800' : 
                                ($client->status === 'prospek' ? 'bg-yellow-100 text-yellow-800' : 
                                ($client->status === 'blacklist' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) 
                            }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $client->projects_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ecrm.clients.show', $client) }}" class="text-blue-600 hover:text-blue-900 transition-all duration-200 hover:scale-110">Lihat</a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('ecrm.clients.edit', $client) }}" class="text-indigo-600 hover:text-indigo-900 transition-all duration-200 hover:scale-110">Ubah</a>
                                <button type="button" 
                                        onclick="openDeleteModal('{{ route('ecrm.clients.destroy', $client) }}', '{{ $client->nama }}')"
                                        class="text-red-600 hover:text-red-900 focus:outline-none transition-all duration-200 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 animate-fade-in">Tidak ada data client</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</div>
@endsection

