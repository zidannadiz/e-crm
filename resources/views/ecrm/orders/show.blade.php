@extends('layouts.app')

@section('title', 'Detail Pesanan - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Pesanan</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.chat.index', $order) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                💬 Pesan
                @if($unreadCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded">{{ $unreadCount }}</span>
                @endif
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('ecrm.orders.edit', $order) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            @if($order->status === 'pending')
            <button type="button" 
                    onclick="openApproveModal('{{ route('ecrm.orders.approve', $order) }}', '{{ $order->nomor_order }}')"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Setujui
            </button>
            @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Informasi Pesanan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Nomor Pesanan</label>
                        <p class="font-semibold">{{ $order->nomor_order }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Klien</label>
                        <p class="font-semibold">{{ $order->client->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Jenis Desain</label>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status Order</label>
                        <span class="px-2 py-1 text-xs rounded {{ 
                            $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                            ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                            ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status Produk</label>
                        <div class="mt-1">
                            @php
                                $produkStatus = $order->produk_status ?? 'pending';
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'proses' => 'Proses',
                                    'selesai' => 'Selesai'
                                ];
                                $produkStatusLabel = $statusLabels[$produkStatus] ?? 'Pending';
                            @endphp
                            <span class="px-4 py-2 text-base font-bold rounded-lg {{ 
                                $produkStatus === 'selesai' ? 'bg-green-100 text-green-800 border-2 border-green-300' : 
                                ($produkStatus === 'proses' ? 'bg-blue-100 text-blue-800 border-2 border-blue-300' : 
                                'bg-yellow-100 text-yellow-800 border-2 border-yellow-300') 
                            }}">
                                {{ $produkStatusLabel }}
                            </span>
                            @if(Auth::user()->role === 'client')
                            <p class="text-xs text-gray-500 mt-1">Status ini menunjukkan tahap produk Anda saat ini</p>
                            @endif
                        </div>
                    </div>
                    @if($order->budget)
                    <div>
                        <label class="text-sm text-gray-500">Anggaran</label>
                        <p class="font-semibold text-green-600">Rp {{ number_format($order->budget, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($order->deadline)
                    <div>
                        <label class="text-sm text-gray-500">Batas Waktu</label>
                        <p class="font-semibold">{{ $order->deadline->format('d M Y') }}</p>
                    </div>
                    @endif
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Deskripsi</label>
                        <p class="font-semibold">{{ $order->deskripsi }}</p>
                    </div>
                    @if($order->kebutuhan)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Kebutuhan</label>
                        <p class="font-semibold">{{ $order->kebutuhan }}</p>
                    </div>
                    @endif
                    @if($order->catatan_admin)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Catatan Admin</label>
                        <p class="font-semibold bg-yellow-50 p-3 rounded">{{ $order->catatan_admin }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Update Status Section (CS & Admin) --}}
            @if(Auth::user()->role === 'cs' || Auth::user()->role === 'admin')
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Update Status Pesanan</h2>
                <form action="{{ route('ecrm.orders.update-status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $order->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ $order->status == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        @if(Auth::user()->role === 'admin')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Produk</label>
                            <select name="produk_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" {{ ($order->produk_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ ($order->produk_status ?? 'pending') == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ ($order->produk_status ?? 'pending') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan_admin" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan untuk pesanan ini...">{{ $order->catatan_admin }}</textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Update Status
                    </button>
                </form>
            </div>
            @endif

            {{-- Upload Desain Section (Admin Only) --}}
            @if(Auth::user()->role === 'admin')
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Upload Hasil Desain</h2>
                <form action="{{ route('ecrm.orders.upload-desain', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Desain
                            <span class="text-gray-500 text-xs">(JPG, PNG, PDF, ZIP, RAR - Max 10MB)</span>
                        </label>
                        <input 
                            type="file" 
                            name="desain_file" 
                            accept=".jpg,.jpeg,.png,.pdf,.zip,.rar"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('desain_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload Desain
                    </button>
                </form>
            </div>
            @endif

            {{-- Hasil Desain Section --}}
            <div id="hasil-desain" class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Hasil Desain</h2>
                @if($order->desain_file)
                    <div class="space-y-4">
                        @php
                            $fileExtension = strtolower(pathinfo($order->desain_file, PATHINFO_EXTENSION));
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                            $fileUrl = asset('storage/desain/' . $order->desain_file);
                        @endphp
                        
                        @if($isImage)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <img src="{{ $fileUrl }}" alt="Hasil Desain" class="max-w-full h-auto rounded-lg shadow-md">
                            </div>
                        @else
                            <div class="border rounded-lg p-4 bg-gray-50">
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
                        
                        <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Desain
                        </a>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada hasil desain</p>
                        @if(Auth::user()->role === 'client')
                            <p class="text-sm text-gray-400 mt-2">Admin akan mengupload hasil desain setelah selesai</p>
                        @endif
                    </div>
                @endif
            </div>

            @if($order->invoices->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Faktur</h2>
                <div class="space-y-4">
                    @foreach($order->invoices as $invoice)
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $invoice->nomor_invoice }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">Total: <span class="font-bold text-blue-600">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span></p>
                                    @if($invoice->total_paid > 0)
                                        <p class="text-xs text-gray-500">Terbayar: Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</p>
                                        <p class="text-xs font-semibold text-orange-600">Sisa: Rp {{ number_format($invoice->total - $invoice->total_paid, 0, ',', '.') }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">Tanggal: {{ $invoice->tanggal_invoice->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">Jatuh Tempo: {{ $invoice->tanggal_jatuh_tempo->format('d M Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 text-xs rounded-full font-semibold {{ 
                                        $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                        ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                        ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))
                                    }}">
                                        {{ $invoice->status === 'paid' ? 'Lunas' : 
                                           ($invoice->status === 'sent' ? 'Terkirim' : 
                                           ($invoice->status === 'overdue' ? 'Jatuh Tempo' : 'Draft')) }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('ecrm.invoices.show', $invoice) }}" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Lihat Detail Faktur →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Aksi Cepat</h2>
                <div class="space-y-2">
                    <a href="{{ route('ecrm.chat.index', $order) }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-center">
                        💬 Pesan
                        @if($unreadCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    @if(Auth::user()->role === 'admin' && $order->status === 'approved')
                    <a href="{{ route('ecrm.invoices.create', ['order_id' => $order->id]) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center">
                        Buat Faktur
                    </a>
                    @endif
                    @if($order->desain_file)
                    <a href="#hasil-desain" class="block w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Hasil Desain
                    </a>
                    @endif
                    @if(Auth::user()->role === 'client' && $order->status === 'pending' && $order->user_id === Auth::id())
                    <a href="{{ route('ecrm.orders.edit', $order) }}" class="block w-full bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Pesanan
                    </a>
                    <form action="{{ route('ecrm.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-center flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Pesanan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

