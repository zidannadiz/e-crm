@extends('layouts.app')

@section('title', 'Tambah Lead - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Tambah Lead</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.leads.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border rounded px-4 py-2">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-4 py-2">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" class="w-full border rounded px-4 py-2">
                    @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sumber</label>
                    <input type="text" name="sumber" value="{{ old('sumber') }}" placeholder="Website, Referral, dll" class="w-full border rounded px-4 py-2">
                    @error('sumber') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full border rounded px-4 py-2">
                        <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                        <option value="qualified" {{ old('status') == 'qualified' ? 'selected' : '' }}>Terkualifikasi</option>
                        <option value="quotation" {{ old('status') == 'quotation' ? 'selected' : '' }}>Penawaran</option>
                        <option value="converted" {{ old('status') == 'converted' ? 'selected' : '' }}>Terkonversi</option>
                        <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Hilang</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                    <select name="assigned_to" class="w-full border rounded px-4 py-2">
                        <option value="">Tidak ada</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Perkiraan</label>
                    <input type="number" name="estimated_value" value="{{ old('estimated_value') }}" step="0.01" class="w-full border rounded px-4 py-2">
                    @error('estimated_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kebutuhan</label>
                    <textarea name="kebutuhan" rows="3" class="w-full border rounded px-4 py-2">{{ old('kebutuhan') }}</textarea>
                    @error('kebutuhan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                <a href="{{ route('ecrm.leads.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

