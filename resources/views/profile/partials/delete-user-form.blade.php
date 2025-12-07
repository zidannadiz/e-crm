<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 mb-2">
            Hapus Akun
        </h2>
        <p class="text-sm text-gray-600">
            Setelah akun Anda dihapus, semua resource dan data akan dihapus secara permanen. Sebelum menghapus akun, silakan download data atau informasi yang ingin Anda simpan.
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
        @csrf
        @method('delete')

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <input
                id="password"
                name="password"
                type="password"
                class="w-full border rounded px-4 py-2"
                placeholder="Masukkan password untuk konfirmasi"
            />

            @error('password', 'userDeletion')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')">
                Hapus Akun
            </button>
        </div>
    </form>
</section>
