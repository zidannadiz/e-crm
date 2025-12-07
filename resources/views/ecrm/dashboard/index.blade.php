@extends('layouts.app')

@section('title', 'Dasbor - e-CRM Jasa Desain Mandiri')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Dasbor</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 mb-2">Total Klien</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_clients'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 mb-2">Pesanan Menunggu</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 mb-2">Pesanan Aktif</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['active_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 mb-2">Total Pendapatan</h3>
            <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Pesanan Terbaru</h2>
            @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $order)
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold">{{ $order->nomor_order }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->client->nama }} - {{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Belum ada order</p>
            @endif
            <a href="{{ route('ecrm.orders.index') }}" class="mt-4 text-blue-600 hover:underline">Lihat semua →</a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Faktur Terbaru</h2>
            @if($recent_invoices->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_invoices as $invoice)
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold">{{ $invoice->nomor_invoice }}</h3>
                                    <p class="text-sm text-gray-600">{{ $invoice->client->nama }}</p>
                                    <p class="text-xs text-gray-500">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Belum ada invoice</p>
            @endif
            <a href="{{ route('ecrm.invoices.index') }}" class="mt-4 text-blue-600 hover:underline">Lihat semua →</a>
        </div>
    </div>
</div>
@endsection

