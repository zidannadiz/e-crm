@extends('layouts.app')

@section('title', 'Dashboard Client - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6 animate-fade-in">Dashboard Saya</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 animate-slide-up delay-100 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
            <h3 class="text-gray-600 mb-2">Total Pesanan</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 animate-slide-up delay-200 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
            <h3 class="text-gray-600 mb-2">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 animate-slide-up delay-300 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
            <h3 class="text-gray-600 mb-2">Sedang Dikerjakan</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['active_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 animate-slide-up delay-400 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
            <h3 class="text-gray-600 mb-2">Selesai</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['completed_orders'] }}</p>
        </div>
    </div>

    @if($unread_messages > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 animate-bounce-in">
        <p class="text-yellow-800">
            <strong>Anda memiliki {{ $unread_messages }} pesan belum dibaca.</strong>
            <a href="{{ route('ecrm.orders.my') }}" class="underline ml-2 transition-all duration-200 hover:text-yellow-900">Lihat pesanan saya</a>
        </p>
    </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6 animate-fade-in hover:shadow-lg transition-all duration-300">
            <h2 class="text-xl font-bold mb-4">Pesanan Terbaru</h2>
            @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $index => $order)
                        <div class="border-b pb-4 animate-slide-in-left hover:bg-gray-50 transition-colors duration-200 rounded px-2 py-1" style="animation-delay: {{ $index * 0.1 }}s;">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold">{{ $order->nomor_order }}</h3>
                                    <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-2 py-1 text-xs rounded transition-all duration-200 hover:scale-110 {{ 
                                        $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                    <a href="{{ route('ecrm.chat.index', $order) }}" class="text-green-600 hover:underline transition-all duration-200 hover:scale-110">Chat</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 animate-fade-in">Belum ada pesanan</p>
                <a href="{{ route('ecrm.orders.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                    Pesan Project Pertama
                </a>
            @endif
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 animate-fade-in hover:shadow-lg transition-all duration-300">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('ecrm.orders.create') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                    + Pesan Project Baru
                </a>
                <a href="{{ route('ecrm.orders.my') }}" class="block w-full bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-center transition-all duration-200 hover:scale-105 active:scale-95">
                    Lihat Semua Pesanan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

