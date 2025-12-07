@extends('layouts.app')

@section('title', 'Edit Pembayaran - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Edit Pembayaran</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.payments.update', $payment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invoice *</label>
                    <select name="invoice_id" required class="w-full border rounded px-4 py-2">
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ old('invoice_id', $payment->invoice_id) == $invoice->id ? 'selected' : '' }}>
                                {{ $invoice->nomor_invoice }} - {{ $invoice->client->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $payment->jumlah) }}" step="0.01" required class="w-full border rounded px-4 py-2">
                    @error('jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran *</label>
                    <input type="date" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', $payment->tanggal_pembayaran->format('Y-m-d')) }}" required class="w-full border rounded px-4 py-2">
                    @error('tanggal_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                    <select name="metode_pembayaran" required class="w-full border rounded px-4 py-2">
                        <option value="transfer" {{ old('metode_pembayaran', $payment->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="cash" {{ old('metode_pembayaran', $payment->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="kartu_kredit" {{ old('metode_pembayaran', $payment->metode_pembayaran) == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="e_wallet" {{ old('metode_pembayaran', $payment->metode_pembayaran) == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="lainnya" {{ old('metode_pembayaran', $payment->metode_pembayaran) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('metode_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                    @if($payment->bukti_pembayaran)
                        <div class="mb-2">
                            <p class="text-sm text-gray-600">File saat ini:</p>
                            @if(str_ends_with($payment->bukti_pembayaran, '.pdf'))
                                <a href="{{ Storage::url($payment->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:underline">Lihat PDF</a>
                            @else
                                <img src="{{ Storage::url($payment->bukti_pembayaran) }}" alt="Bukti" class="max-w-xs rounded shadow">
                            @endif
                        </div>
                    @endif
                    <input type="file" name="bukti_pembayaran" accept="image/*,application/pdf" class="w-full border rounded px-4 py-2">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (Max: 2MB)</p>
                    @error('bukti_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full border rounded px-4 py-2">{{ old('catatan', $payment->catatan) }}</textarea>
                    @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update
                </button>
                <a href="{{ route('ecrm.payments.show', $payment) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

