@extends('layouts.app')

@section('title', 'Detail Admin - e-CRM')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Admin</h1>
        <div class="flex gap-2">
            @if($admin->email !== 'admin@ecrm.com')
                <a href="{{ route('ecrm.admins.edit', $admin) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Ubah
                </a>
            @endif
            <a href="{{ route('ecrm.admins.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-red-500 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $admin->name }}</h2>
                    <p class="text-gray-600">
                        @if($admin->email === 'admin@ecrm.com')
                            Super Admin
                        @else
                            Admin
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="font-semibold text-gray-900">{{ $admin->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Role</label>
                    <p class="font-semibold">
                        @if($admin->email === 'admin@ecrm.com')
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-sm">Super Admin</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Admin</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tanggal Dibuat</label>
                    <p class="font-semibold text-gray-900">{{ $admin->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Terakhir Diupdate</label>
                    <p class="font-semibold text-gray-900">{{ $admin->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

