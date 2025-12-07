@extends('layouts.app')

@section('title', 'Kotak Masuk Pesan - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kotak Masuk Pesan</h1>
            <p class="text-gray-600 mt-1">
                @if(Auth::user()->role === 'admin')
                    Kelola semua percakapan dari pelanggan dan tim
                @else
                    Kelola percakapan dari pelanggan
                @endif
            </p>
        </div>
        <form method="POST" action="{{ route('ecrm.messages.mark-all-read') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Tandai Semua Dibaca
                </span>
            </button>
        </form>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Total Percakapan</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-200 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600">Pesan Belum Dibaca</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['unread'] }}</p>
                </div>
                <div class="bg-red-200 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Pesan Hari Ini</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['today'] }}</p>
                </div>
                <div class="bg-green-200 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari order, client, pesan..." 
                class="col-span-2 border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            
            <select name="status" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200 cursor-pointer shadow-sm">
                <option value="">Semua Status</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
            </select>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Cari
                </button>
                <a href="{{ route('ecrm.messages.inbox') }}" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Conversations List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="divide-y divide-gray-200">
            @forelse($orders as $order)
                @php
                    $hasUnread = $order->unread_count > 0;
                    $lastMessage = $order->last_message;
                @endphp
                <a href="{{ route('ecrm.chat.index', $order) }}" 
                   class="block p-6 hover:bg-gray-50 transition-colors {{ $hasUnread ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @if($hasUnread)
                                    <span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0"></span>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                        {{ $order->client->nama }}
                                        @if($hasUnread)
                                            <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                                                {{ $order->unread_count }} baru
                                            </span>
                                        @endif
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                        <span>Order: <span class="font-medium text-gray-700">{{ $order->nomor_order }}</span></span>
                                        <span>•</span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                            {{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $order->total_messages }} pesan</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($lastMessage)
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <span class="font-medium text-gray-700 flex items-center gap-2">
                                        {{ $lastMessage->user->name }}
                                        @if($lastMessage->user->role === 'admin')
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded">Admin</span>
                                        @elseif($lastMessage->user->role === 'cs')
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">CS</span>
                                        @else
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Client</span>
                                        @endif
                                        :
                                    </span>
                                    <span class="ml-2">{{ Str::limit($lastMessage->pesan, 100) }}</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $lastMessage->created_at->diffForHumans() }} • {{ $lastMessage->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            @else
                            <p class="text-sm text-gray-500 mt-2">Belum ada pesan</p>
                            @endif
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold {{ 
                                $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                            }}">
                                @php
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'in_progress' => 'Sedang Dikerjakan',
                                        'review' => 'Tinjauan',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    echo $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                                @endphp
                            </span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">Tidak ada percakapan ditemukan</p>
                    <p class="text-sm text-gray-400 mt-1">
                        @if(Auth::user()->role === 'admin')
                            Percakapan dari customer dan tim akan muncul di sini
                        @else
                            Percakapan dari customer akan muncul di sini
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
