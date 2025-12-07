@extends('layouts.app')

@section('title', 'Pesan Project - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Pesan Project</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.orders.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Desain *</label>
                    <select name="jenis_desain" required class="w-full border rounded px-4 py-2">
                        <option value="logo">Logo</option>
                        <option value="branding">Branding</option>
                        <option value="web_design">Web Design</option>
                        <option value="ui_ux">UI/UX</option>
                        <option value="print_design">Print Design</option>
                        <option value="packaging">Packaging</option>
                        <option value="social_media">Social Media</option>
                        <option value="seminar">Desain Seminar</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                    @error('jenis_desain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="deskripsi" rows="5" required class="w-full border rounded px-4 py-2" placeholder="Jelaskan kebutuhan desain Anda secara detail..."></textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kebutuhan Tambahan</label>
                    <textarea name="kebutuhan" rows="3" class="w-full border rounded px-4 py-2" placeholder="Tambahkan detail kebutuhan lainnya (opsional)"></textarea>
                    @error('kebutuhan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deadline (Opsional)</label>
                    <input type="date" name="deadline" class="w-full border rounded px-4 py-2">
                    @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                    <p class="text-sm text-blue-800">
                        <strong>Info:</strong> Setelah mengirim pesanan, admin akan meninjau dan menghubungi Anda melalui chat untuk diskusi lebih lanjut.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Kirim Pesanan
                </button>
                <a href="{{ route('ecrm.orders.my') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

