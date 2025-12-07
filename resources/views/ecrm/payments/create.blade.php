@extends('layouts.app')

@section('title', 'Tambah Pembayaran - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Tambah Pembayaran</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.payments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Invoice *</label>
                    <select name="invoice_id" id="invoice_id" required class="w-full border rounded px-4 py-2" onchange="updateInvoiceInfo()">
                        <option value="">Pilih Invoice</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" 
                                data-total="{{ $invoice->total }}" 
                                data-paid="{{ $invoice->total_paid }}"
                                data-remaining="{{ $invoice->remaining_amount }}"
                                {{ old('invoice_id', $invoiceId) == $invoice->id ? 'selected' : '' }}>
                                {{ $invoice->nomor_invoice }} - {{ $invoice->client->nama }} (Sisa: Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div id="invoice_info" class="mt-2 text-sm text-gray-600"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" step="0.01" required class="w-full border rounded px-4 py-2">
                    @error('jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran *</label>
                    <input type="date" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" required class="w-full border rounded px-4 py-2">
                    @error('tanggal_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran *</label>
                    <select name="metode_pembayaran" required class="w-full border rounded px-4 py-2">
                        <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="kartu_kredit" {{ old('metode_pembayaran') == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="e_wallet" {{ old('metode_pembayaran') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="lainnya" {{ old('metode_pembayaran') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('metode_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" accept="image/*,application/pdf" class="w-full border rounded px-4 py-2">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (Max: 2MB)</p>
                    @error('bukti_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full border rounded px-4 py-2">{{ old('catatan') }}</textarea>
                    @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ route('ecrm.payments.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateInvoiceInfo() {
    const select = document.getElementById('invoice_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('invoice_info');
    
    if (selectedOption.value) {
        const total = parseFloat(selectedOption.getAttribute('data-total')) || 0;
        const paid = parseFloat(selectedOption.getAttribute('data-paid')) || 0;
        const remaining = parseFloat(selectedOption.getAttribute('data-remaining')) || 0;
        
        infoDiv.innerHTML = `
            <p>Total Invoice: Rp ${total.toLocaleString('id-ID')}</p>
            <p>Terbayar: Rp ${paid.toLocaleString('id-ID')}</p>
            <p class="font-semibold">Sisa: Rp ${remaining.toLocaleString('id-ID')}</p>
        `;
    } else {
        infoDiv.innerHTML = '';
    }
}

// Initialize
updateInvoiceInfo();
</script>
@endsection

