@extends('layouts.app')

@section('title', 'Buat Invoice - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Buat Invoice</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.invoices.store') }}" method="POST" id="invoiceForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order *</label>
                    <select name="order_id" id="order_id" required class="w-full border rounded px-4 py-2" onchange="updateClient()">
                        <option value="">Pilih Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" data-client="{{ $order->client_id }}" data-budget="{{ $order->budget ?? 0 }}" {{ old('order_id', $orderId) == $order->id ? 'selected' : '' }}>
                                {{ $order->nomor_order }} - {{ $order->client->nama }} ({{ ucfirst(str_replace('_', ' ', $order->jenis_desain)) }})
                            </option>
                        @endforeach
                    </select>
                    @error('order_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                    <select name="client_id" id="client_id" required class="w-full border rounded px-4 py-2">
                        <option value="">Pilih Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->nama }} {{ $client->perusahaan ? '(' . $client->perusahaan . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Invoice *</label>
                    <input type="date" name="tanggal_invoice" value="{{ old('tanggal_invoice', date('Y-m-d')) }}" required class="w-full border rounded px-4 py-2">
                    @error('tanggal_invoice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Jatuh Tempo *</label>
                    <input type="date" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full border rounded px-4 py-2">
                    @error('tanggal_jatuh_tempo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal *</label>
                    <input type="number" name="subtotal" id="subtotal" value="{{ old('subtotal') }}" step="0.01" required class="w-full border rounded px-4 py-2" oninput="calculateTotal()">
                    @error('subtotal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pajak</label>
                    <input type="number" name="pajak" id="pajak" value="{{ old('pajak', 0) }}" step="0.01" class="w-full border rounded px-4 py-2" oninput="calculateTotal()">
                    @error('pajak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Diskon</label>
                    <input type="number" name="diskon" id="diskon" value="{{ old('diskon', 0) }}" step="0.01" class="w-full border rounded px-4 py-2" oninput="calculateTotal()">
                    @error('diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                    <input type="text" id="total_display" value="Rp 0" readonly class="w-full border rounded px-4 py-2 bg-gray-100 font-semibold">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border rounded px-4 py-2">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                <a href="{{ route('ecrm.invoices.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateClient() {
    const orderSelect = document.getElementById('order_id');
    const clientSelect = document.getElementById('client_id');
    const subtotalInput = document.getElementById('subtotal');
    
    const selectedOption = orderSelect.options[orderSelect.selectedIndex];
    const clientId = selectedOption.getAttribute('data-client');
    const budget = selectedOption.getAttribute('data-budget');
    
    if (clientId) {
        clientSelect.value = clientId;
    }
    
    if (budget && !subtotalInput.value) {
        subtotalInput.value = budget;
        calculateTotal();
    }
}

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

