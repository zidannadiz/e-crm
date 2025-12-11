@extends('layouts.app')

@section('title', 'Invoices Saya - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6 animate-fade-in">
        <div>
            <h1 class="text-3xl font-bold">Invoices Saya</h1>
            <p class="text-gray-600 mt-1">Daftar tagihan untuk pesanan Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow mb-4 p-4 animate-slide-up">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor invoice atau nomor order..." class="flex-1 border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
            <div class="relative inline-block" style="min-width: 180px;">
                <select name="status" class="w-full appearance-none bg-white border-2 border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-all duration-200 cursor-pointer shadow-sm focus:scale-105" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Jatuh Tempo</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">Cari</button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($invoices as $index => $invoice)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-all duration-300 animate-slide-up hover:scale-[1.02]" style="animation-delay: {{ $index * 0.1 }}s; opacity: 0; animation-fill-mode: forwards;">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900">{{ $invoice->nomor_invoice }}</h3>
                                <span class="px-3 py-1 text-sm rounded-full font-semibold {{ 
                                    $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                    ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                    ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                                }}">
                                    {{ $invoice->status === 'paid' ? 'Lunas' : 
                                       ($invoice->status === 'sent' ? 'Terkirim' : 
                                       ($invoice->status === 'overdue' ? 'Jatuh Tempo' : 'Draft')) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">Tanggal Invoice: {{ $invoice->tanggal_invoice->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                            @if($invoice->total_paid > 0)
                                <p class="text-sm text-gray-600">Terbayar: Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</p>
                                <p class="text-sm font-semibold text-orange-600">Sisa: Rp {{ number_format($invoice->total - $invoice->total_paid, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Detail Pesanan -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Detail Pesanan
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Order:</span>
                                <span class="font-medium">{{ $invoice->order->nomor_order }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis Desain:</span>
                                <span class="font-medium capitalize">{{ str_replace('_', ' ', $invoice->order->jenis_desain) }}</span>
                            </div>
                            @if($invoice->order->deskripsi)
                                <div class="pt-2 border-t border-gray-200">
                                    <span class="text-gray-600 block mb-1">Deskripsi Pesanan:</span>
                                    <p class="text-sm text-gray-800">{{ Str::limit($invoice->order->deskripsi, 150) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Detail Faktur -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Jatuh Tempo</p>
                            <p class="font-semibold {{ $invoice->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $invoice->tanggal_jatuh_tempo->format('d M Y') }}
                                @if($invoice->is_overdue)
                                    <span class="text-xs">(Lewat {{ $invoice->tanggal_jatuh_tempo->diffForHumans() }})</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Subtotal</p>
                            <p class="font-semibold text-gray-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</p>
                        </div>
                        @if($invoice->pajak > 0)
                        <div>
                            <p class="text-sm text-gray-600">Pajak</p>
                            <p class="font-semibold text-gray-900">Rp {{ number_format($invoice->pajak, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        @if($invoice->diskon > 0)
                        <div>
                            <p class="text-sm text-gray-600">Diskon</p>
                            <p class="font-semibold text-green-600">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($invoice->deskripsi)
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Catatan:</p>
                        <p class="text-sm text-gray-800">{{ $invoice->deskripsi }}</p>
                    </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            @if($invoice->payments->count() > 0)
                                <span class="text-green-600 font-semibold">✓ {{ $invoice->payments->count() }} Pembayaran</span>
                            @else
                                <span class="text-gray-500">Belum ada pembayaran</span>
                            @endif
                        </div>
                        <a href="{{ route('ecrm.invoices.show', $invoice) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Tidak ada invoice</p>
                <p class="text-gray-400 text-sm mt-2">Invoice akan muncul di sini setelah admin membuat invoice untuk pesanan Anda</p>
            </div>
        @endforelse
    </div>

    @if($invoices->hasPages())
    <div class="mt-6">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection

