@extends('layouts.app')

@section('title', 'Pesanan - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Pesanan</h1>
        @if(Auth::user()->role === 'client')
        <a href="{{ route('ecrm.orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Pesan Project
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow mb-4 p-4">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor order, deskripsi..." class="flex-1 border rounded px-4 py-2">
            <div class="relative inline-block" style="min-width: 180px;">
                <select name="status" class="w-full appearance-none bg-white border-2 border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200 cursor-pointer shadow-sm" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Desain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $order->nomor_order }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $order->client->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @php
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'in_progress' => 'Sedang Dikerjakan',
                                        'review' => 'Tinjauan',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                                @endphp
                                <span class="inline-flex px-5 py-1 text-xs font-semibold rounded-full {{ 
                                    $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                    ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ $statusLabel }}
                                </span>
                                @php
                                    $produkStatus = $order->produk_status ?? 'pending';
                                    $produkStatusLabels = [
                                        'pending' => 'Pending',
                                        'proses' => 'Proses',
                                        'selesai' => 'Selesai'
                                    ];
                                    $produkStatusLabel = $produkStatusLabels[$produkStatus] ?? 'Pending';
                                @endphp
                                <div>
                                    <span class="inline-flex px-5 py-1 text-xs font-semibold rounded-full {{ 
                                        $produkStatus === 'selesai' ? 'bg-green-100 text-green-800' : 
                                        ($produkStatus === 'proses' ? 'bg-blue-100 text-blue-800' : 
                                        'bg-yellow-100 text-yellow-800') 
                                    }}">
                                        Produk: {{ $produkStatusLabel }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->budget ? 'Rp ' . number_format($order->budget, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ecrm.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'cs')
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('ecrm.chat.index', $order) }}" class="text-green-600 hover:text-green-900">Pesan</a>
                                @endif
                                @if(Auth::user()->role === 'admin')
                                <span class="text-gray-300">|</span>
                                <button type="button" 
                                        onclick="openDeleteModal('{{ route('ecrm.orders.destroy', $order) }}', '{{ $order->nomor_order }}')"
                                        class="text-red-600 hover:text-red-900 focus:outline-none">
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data order</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

@endsection

