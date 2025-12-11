@extends('layouts.app')

@section('title', 'Edit Invoice - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6 animate-fade-in">Edit Invoice</h1>

    <div class="bg-white rounded-lg shadow p-6 animate-slide-up">
        <form action="{{ route('ecrm.invoices.update', $invoice) }}" method="POST" id="invoiceForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order *</label>
                    <select name="order_id" id="order_id" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" {{ old('order_id', $invoice->order_id) == $order->id ? 'selected' : '' }}>
                                {{ $order->nomor_order }} - {{ $order->client->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                    <select name="client_id" id="client_id" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->nama }} {{ $client->perusahaan ? '(' . $client->perusahaan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Invoice *</label>
                    <input type="date" name="tanggal_invoice" value="{{ old('tanggal_invoice', $invoice->tanggal_invoice->format('Y-m-d')) }}" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                    @error('tanggal_invoice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jatuh Tempo *</label>
                    <input type="date" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $invoice->tanggal_jatuh_tempo->format('Y-m-d')) }}" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                    @error('tanggal_jatuh_tempo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal *</label>
                    <input type="number" name="subtotal" id="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}" step="0.01" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105" oninput="calculateTotal()">
                    @error('subtotal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pajak</label>
                    <input type="number" name="pajak" id="pajak" value="{{ old('pajak', $invoice->pajak) }}" step="0.01" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105" oninput="calculateTotal()">
                    @error('pajak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Diskon</label>
                    <input type="number" name="diskon" id="diskon" value="{{ old('diskon', $invoice->diskon) }}" step="0.01" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105" oninput="calculateTotal()">
                    @error('diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                        <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ old('status', $invoice->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                    <input type="text" id="total_display" value="Rp 0" readonly class="w-full border rounded px-4 py-2 bg-gray-100 font-semibold">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">{{ old('deskripsi', $invoice->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">{{ old('catatan', $invoice->catatan) }}</textarea>
                    @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                    Perbarui
                </button>
                <a href="{{ route('ecrm.invoices.show', $invoice) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function calculateTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
    const pajak = parseFloat(document.getElementById('pajak').value) || 0;
    const diskon = parseFloat(document.getElementById('diskon').value) || 0;
    
    const total = (subtotal + pajak) - diskon;
    document.getElementById('total_display').value = 'Rp ' + total.toLocaleString('id-ID');
}

// Initialize
calculateTotal();
</script>
@endsection

