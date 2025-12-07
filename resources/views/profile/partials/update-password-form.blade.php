<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 mb-2">
            Update Password
        </h2>
        <p class="text-sm text-gray-600">
            Pastikan akun Anda menggunakan password yang panjang dan acak untuk keamanan.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
            <input id="current_password" name="current_password" type="password" class="w-full border rounded px-4 py-2" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
            <input id="password" name="password" type="password" class="w-full border rounded px-4 py-2" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="w-full border rounded px-4 py-2" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Simpan
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600">
                    Password berhasil diupdate.
                </p>
            @endif
        </div>
    </form>
</section>
