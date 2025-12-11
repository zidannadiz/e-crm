@extends('layouts.app')

@section('title', 'Edit Pesanan - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6 animate-fade-in">Edit Pesanan</h1>

    <div class="bg-white rounded-lg shadow p-6 animate-slide-up">
        <form action="{{ route('ecrm.orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Desain *</label>
                    <select name="jenis_desain" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                        <option value="logo" {{ $order->jenis_desain == 'logo' ? 'selected' : '' }}>Logo</option>
                        <option value="branding" {{ $order->jenis_desain == 'branding' ? 'selected' : '' }}>Branding</option>
                        <option value="web_design" {{ $order->jenis_desain == 'web_design' ? 'selected' : '' }}>Web Design</option>
                        <option value="ui_ux" {{ $order->jenis_desain == 'ui_ux' ? 'selected' : '' }}>UI/UX</option>
                        <option value="print_design" {{ $order->jenis_desain == 'print_design' ? 'selected' : '' }}>Print Design</option>
                        <option value="packaging" {{ $order->jenis_desain == 'packaging' ? 'selected' : '' }}>Packaging</option>
                        <option value="social_media" {{ $order->jenis_desain == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="seminar" {{ $order->jenis_desain == 'seminar' ? 'selected' : '' }}>Desain Seminar</option>
                        <option value="lainnya" {{ $order->jenis_desain == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_desain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="deskripsi" rows="5" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">{{ old('deskripsi', $order->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kebutuhan Tambahan</label>
                    <textarea name="kebutuhan" rows="3" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">{{ old('kebutuhan', $order->kebutuhan) }}</textarea>
                    @error('kebutuhan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deadline (Opsional)</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $order->deadline ? $order->deadline->format('Y-m-d') : '') }}" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                    @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if(Auth::user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Produk *</label>
                    <select name="produk_status" required class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">
                        <option value="pending" {{ old('produk_status', $order->produk_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="proses" {{ old('produk_status', $order->produk_status ?? 'pending') == 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ old('produk_status', $order->produk_status ?? 'pending') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Status ini akan ditampilkan ke pelanggan untuk memberitahu tahap produk</p>
                    @error('produk_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget (Opsional)</label>
                    <input type="number" name="budget" step="0.01" value="{{ old('budget', $order->budget) }}" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105" placeholder="0.00">
                    @error('budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="3" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105">{{ old('catatan_admin', $order->catatan_admin) }}</textarea>
                    @error('catatan_admin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                @if(Auth::user()->role === 'client')
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Info:</strong> Pesanan hanya dapat diedit jika status masih pending. Setelah admin menyetujui, pesanan tidak dapat diubah lagi.
                    </p>
                </div>
                @endif
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple">
                    Perbarui Pesanan
                </button>
                <a href="{{ route('ecrm.orders.show', $order) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

