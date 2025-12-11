@extends('layouts.app')

@section('title', 'Layanan Pelanggan - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6 animate-fade-in">
            <h1 class="text-3xl font-bold">Layanan Pelanggan</h1>
        <a href="{{ route('ecrm.customer-services.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
            + Tambah Layanan Pelanggan
        </a>
    </div>

    <div class="bg-white rounded-lg shadow mb-4 p-4 animate-slide-up">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email..." class="flex-1 border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">Cari</button>
            @if(request('search'))
            <a href="{{ route('ecrm.customer-services.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition-all duration-200 hover:scale-105 active:scale-95">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customerServices as $index => $cs)
                    <tr class="table-row-hover animate-slide-in-left hover:bg-gray-50 transition-all duration-200" style="animation-delay: {{ $index * 0.05 }}s; opacity: 0; animation-fill-mode: forwards;">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold transition-all duration-200 hover:scale-110">
                                        {{ strtoupper(substr($cs->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cs->name }}</div>
                                    <div class="text-xs text-gray-500">Layanan Pelanggan</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $cs->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $cs->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ecrm.customer-services.show', $cs) }}" class="text-blue-600 hover:text-blue-900 transition-all duration-200 hover:scale-110">Lihat</a>
                                <a href="{{ route('ecrm.customer-services.edit', $cs) }}" class="text-indigo-600 hover:text-indigo-900 transition-all duration-200 hover:scale-110">Ubah</a>
                                @if($cs->id !== Auth::id())
                                <button type="button" 
                                        onclick="openDeleteModal('{{ route('ecrm.customer-services.destroy', $cs) }}', '{{ $cs->name }}')"
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 animate-fade-in">Tidak ada Layanan Pelanggan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customerServices->links() }}
    </div>
</div>
@endsection

