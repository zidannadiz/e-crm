@extends('layouts.app')

@section('title', 'Detail Faktur - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Faktur</h1>
        <div class="flex gap-2">
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('ecrm.invoices.edit', $invoice) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Ubah
                </a>
                @if($invoice->status === 'draft')
                <form action="{{ route('ecrm.invoices.send', $invoice) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Kirim Invoice
                    </button>
                </form>
                @endif
                <a href="{{ route('ecrm.payments.create', ['invoice_id' => $invoice->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    + Tambah Pembayaran
                </a>
            @else
                <a href="{{ route('ecrm.orders.my') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    ← Kembali ke Pesanan
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $invoice->nomor_invoice }}</h2>
                        <p class="text-gray-600">Tanggal: {{ $invoice->tanggal_invoice->format('d M Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm rounded {{ 
                        $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                        ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                        ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                    }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-sm text-gray-500">Client</label>
                        <p class="font-semibold">{{ $invoice->client->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Order</label>
                        <p class="font-semibold">
                            <a href="{{ route('ecrm.orders.show', $invoice->order) }}" class="text-blue-600 hover:underline">
                                {{ $invoice->order->nomor_order }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Jatuh Tempo</label>
                        <p class="font-semibold {{ $invoice->is_overdue ? 'text-red-600' : '' }}">
                            {{ $invoice->tanggal_jatuh_tempo->format('d M Y') }}
                            @if($invoice->is_overdue)
                                <span class="text-xs">({{ $invoice->tanggal_jatuh_tempo->diffForHumans() }})</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Order Details Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Detail Pesanan
                    </h3>
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-600 uppercase tracking-wide">Jenis Desain</label>
                                <p class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $invoice->order->jenis_desain) }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 uppercase tracking-wide">Status Order</label>
                                <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold {{ 
                                    $invoice->order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($invoice->order->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                    ($invoice->order->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $invoice->order->status)) }}
                                </span>
                            </div>
                        </div>
                        
                        @if($invoice->order->deskripsi)
                        <div class="pt-2 border-t border-gray-200">
                            <label class="text-xs text-gray-600 uppercase tracking-wide block mb-1">Deskripsi Pesanan</label>
                            <p class="text-sm text-gray-800 leading-relaxed">{{ $invoice->order->deskripsi }}</p>
                        </div>
                        @endif
                        
                        @if($invoice->order->kebutuhan)
                        <div class="pt-2 border-t border-gray-200">
                            <label class="text-xs text-gray-600 uppercase tracking-wide block mb-1">Kebutuhan Khusus</label>
                            <p class="text-sm text-gray-800 leading-relaxed">{{ $invoice->order->kebutuhan }}</p>
                        </div>
                        @endif
                        
                        @if($invoice->order->deadline)
                        <div class="pt-2 border-t border-gray-200">
                            <label class="text-xs text-gray-600 uppercase tracking-wide block mb-1">Deadline Pesanan</label>
                            <p class="text-sm text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($invoice->order->deadline)->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->pajak > 0)
                        <div class="flex justify-between">
                            <span>Pajak:</span>
                            <span>Rp {{ number_format($invoice->pajak, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($invoice->diskon > 0)
                        <div class="flex justify-between">
                            <span>Diskon:</span>
                            <span>Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($invoice->deskripsi)
                <div class="mt-4">
                    <label class="text-sm text-gray-500">Deskripsi</label>
                    <p class="mt-1">{{ $invoice->deskripsi }}</p>
                </div>
                @endif

                @if($invoice->catatan)
                <div class="mt-4">
                    <label class="text-sm text-gray-500">Catatan</label>
                    <p class="mt-1">{{ $invoice->catatan }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">History Pembayaran</h2>
                @if($invoice->payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($invoice->payments as $payment)
                            <div class="border-b pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</p>
                                        <p class="text-sm text-gray-600">{{ $payment->tanggal_pembayaran->format('d M Y') }} - {{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}</p>
                                        <p class="text-xs text-gray-500">
                                            Status: 
                                            <span class="px-2 py-1 rounded {{ 
                                                $payment->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                                ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                                            }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <a href="{{ route('ecrm.payments.show', $payment) }}" class="text-blue-600 hover:underline">Lihat</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada pembayaran</p>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Status Pembayaran</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-500">Total Invoice</label>
                        <p class="text-2xl font-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Terbayar</label>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Sisa</label>
                        <p class="text-2xl font-bold {{ $invoice->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="pt-4 border-t">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $percentage = $invoice->total > 0 ? ($invoice->total_paid / $invoice->total) * 100 : 0;
                            @endphp
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ number_format($percentage, 1) }}% Terbayar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

