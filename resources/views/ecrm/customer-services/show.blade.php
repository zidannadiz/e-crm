@extends('layouts.app')

@section('title', 'Detail Customer Service - e-CRM')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Customer Service</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.customer-services.edit', $customerService) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Ubah
            </a>
            <a href="{{ route('ecrm.customer-services.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($customerService->name, 0, 1)) }}
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $customerService->name }}</h2>
                    <p class="text-gray-600">Customer Service</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="font-semibold text-gray-900">{{ $customerService->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Role</label>
                    <p class="font-semibold">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Customer Service</span>
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tanggal Dibuat</label>
                    <p class="font-semibold text-gray-900">{{ $customerService->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Terakhir Diupdate</label>
                    <p class="font-semibold text-gray-900">{{ $customerService->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

