@extends('layouts.app')

@section('title', 'Ubah Balasan Cepat - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Ubah Balasan Cepat</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.quick-replies.update', $quickReply) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan *</label>
                    <input type="text" name="pertanyaan" value="{{ old('pertanyaan', $quickReply->pertanyaan) }}" required class="w-full border rounded px-4 py-2">
                    @error('pertanyaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jawaban *</label>
                    <textarea name="jawaban" rows="5" required class="w-full border rounded px-4 py-2">{{ old('jawaban', $quickReply->jawaban) }}</textarea>
                    @error('jawaban') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="kategori" value="{{ old('kategori', $quickReply->kategori) }}" class="w-full border rounded px-4 py-2">
                    @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', $quickReply->order) }}" class="w-full border rounded px-4 py-2">
                        @error('order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="use_ai" value="1" {{ old('use_ai', $quickReply->use_ai) ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Gunakan AI untuk generate jawaban</span>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', $quickReply->aktif) ? 'checked' : '' }} class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Perbarui
                </button>
                <a href="{{ route('ecrm.quick-replies.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

