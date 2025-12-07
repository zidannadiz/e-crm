@extends('layouts.app')

@section('title', 'Pesanan Saya - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Pesanan Saya</h1>
        <a href="{{ route('ecrm.orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Pesan Project Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($orders as $order)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">{{ $order->nomor_order }}</h3>
                        <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        @php
                            $produkStatus = $order->produk_status ?? 'pending';
                            $produkStatusLabels = [
                                'pending' => 'Pending',
                                'proses' => 'Proses',
                                'selesai' => 'Selesai'
                            ];
                            $produkStatusLabel = $produkStatusLabels[$produkStatus] ?? 'Pending';
                        @endphp
                        <span class="inline-flex px-5 py-1 text-sm font-semibold rounded-full {{ 
                            $produkStatus === 'selesai' ? 'bg-green-100 text-green-800' : 
                            ($produkStatus === 'proses' ? 'bg-blue-100 text-blue-800' : 
                            'bg-yellow-100 text-yellow-800') 
                        }}">
                            {{ $produkStatusLabel }}
                        </span>
                        <span class="inline-flex px-5 py-1 text-xs font-semibold rounded-full {{ 
                            $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                            ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                            ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
                
                <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ Str::limit($order->deskripsi, 100) }}</p>
                
                @if($order->budget)
                <p class="text-sm font-semibold text-green-600 mb-4">Budget: Rp {{ number_format($order->budget, 0, ',', '.') }}</p>
                @endif

                {{-- Hasil Desain Preview --}}
                @if($order->desain_file)
                <div class="mb-4 border rounded-lg p-4 bg-gray-50">
                    <p class="text-sm font-semibold text-gray-900 mb-3">Hasil Desain Tersedia</p>
                    @php
                        $fileExtension = strtolower(pathinfo($order->desain_file, PATHINFO_EXTENSION));
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                        $fileUrl = asset('storage/desain/' . $order->desain_file);
                    @endphp
                    @if($isImage)
                        <div class="mb-3">
                            <img src="{{ $fileUrl }}" alt="Hasil Desain" class="w-full h-auto rounded-lg shadow-md">
                        </div>
                    @else
                        <div class="mb-3 border rounded-lg p-4 bg-white">
                            <div class="flex items-center gap-4">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $order->desain_file }}</p>
                                    <p class="text-sm text-gray-500 mt-1">File: {{ strtoupper($fileExtension) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Desain
                        </a>
                    </div>
                </div>
                @endif

                <div class="flex gap-2">
                    <a href="{{ route('ecrm.orders.show', $order) }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm">
                        Lihat Detail
                    </a>
                    <a href="{{ route('ecrm.chat.index', $order) }}" class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center text-sm">
                        Chat
                    </a>
                </div>
                
                @if($order->status === 'pending')
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('ecrm.orders.edit', $order) }}" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 text-center text-sm flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('ecrm.orders.destroy', $order) }}" method="POST" class="flex-1 delete-form" data-message="Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-center text-sm flex items-center justify-center gap-1 delete-btn" style="cursor: pointer;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
        @empty
            <div class="col-span-3 flex items-center justify-center py-12">
                <p class="text-gray-500 text-lg">Anda belum memiliki pesanan</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection

