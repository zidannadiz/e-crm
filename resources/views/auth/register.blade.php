<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - e-CRM Jasa Desain</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Logo & Header --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-2xl shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Buat Akun Baru
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Daftar untuk mulai order jasa desain profesional
                </p>
            </div>

            {{-- Registration Card --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            required 
                            value="{{ old('name') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="John Doe">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            value="{{ old('email') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="john@example.com">
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Telepon
                        </label>
                        <input 
                            id="telepon" 
                            name="telepon" 
                            type="text" 
                            value="{{ old('telepon') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="08123456789">
                    </div>

                    {{-- Tipe Client --}}
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="tipe" 
                            name="tipe" 
                            required
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="individu" {{ old('tipe') == 'individu' ? 'selected' : '' }}>Individu</option>
                            <option value="perusahaan" {{ old('tipe') == 'perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                        </select>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <textarea 
                            id="alamat" 
                            name="alamat" 
                            rows="2"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Alamat lengkap Anda">{{ old('alamat') }}</textarea>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Minimal 8 karakter">
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Ketik ulang password">
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full flex justify-center items-center py-3 px-4 border-0 rounded-lg shadow-md text-base font-semibold"
                            style="background: linear-gradient(to right, #2563eb, #9333ea); color: white; cursor: pointer;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Sudah punya akun?
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Login Link --}}
                <div class="mt-6">
                    <a href="{{ route('login') }}" 
                       class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-sm font-semibold transition-all"
                       style="background: white; border: 2px solid #e5e7eb; color: #374151; cursor: pointer;"
                       onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db';"
                       onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';">
                        <svg class="w-5 h-5 mr-2" style="color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Masuk ke Akun
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    Dengan mendaftar, Anda menyetujui 
                    <a href="#" class="text-blue-600 hover:text-blue-500">Syarat & Ketentuan</a> 
                    dan 
                    <a href="#" class="text-blue-600 hover:text-blue-500">Kebijakan Privasi</a>
                </p>
            </div>
        </div>
    </div>

    {{-- JavaScript for Button Hover Effect --}}
    <script>
        // Submit button hover effect
        const submitBtn = document.querySelector('button[type="submit"]');
        submitBtn.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(to right, #1d4ed8, #7c3aed)';
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
        submitBtn.addEventListener('mouseleave', function() {
            this.style.background = 'linear-gradient(to right, #2563eb, #9333ea)';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
        });

        // Add transition to button
        submitBtn.style.transition = 'all 0.2s ease';
    </script>
</body>
</html>
