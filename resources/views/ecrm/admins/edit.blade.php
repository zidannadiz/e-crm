@extends('layouts.app')

@section('title', 'Ubah Admin - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6 animate-fade-in">
        <h1 class="text-3xl font-bold">Ubah Admin</h1>
        <a href="{{ route('ecrm.admins.index') }}" class="text-gray-600 hover:text-gray-900 transition-all duration-200 hover:scale-110">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 animate-slide-up">
        <form action="{{ route('ecrm.admins.update', $admin) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                    <input type="text" 
                           id="name"
                           name="name" 
                           value="{{ old('name', $admin->name) }}" 
                           required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105 @error('name') border-red-500 @enderror"
                           placeholder="Nama lengkap Admin">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" 
                           id="email"
                           name="email" 
                           value="{{ old('email', $admin->email) }}" 
                           required 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105 @error('email') border-red-500 @enderror"
                           placeholder="email@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" 
                           id="password"
                           name="password" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105 @error('password') border-red-500 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" 
                           id="password_confirmation"
                           name="password_confirmation" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 focus:scale-105"
                           placeholder="Ulangi password baru">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Catatan:</strong> Jika password dikosongkan, password lama akan tetap digunakan.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 ripple font-medium">
                    Perbarui
                </button>
                <a href="{{ route('ecrm.admins.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

