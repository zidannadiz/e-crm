@extends('layouts.app')

@section('title', 'Detail Lead - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Lead</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.leads.edit', $lead) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Edit
            </a>
            @if($lead->status !== 'converted')
            <form action="{{ route('ecrm.leads.convert', $lead) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" onclick="return confirm('Konversi lead ini menjadi client?')">
                    Convert to Client
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Informasi Lead</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Nama</label>
                        <p class="font-semibold">{{ $lead->nama }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-semibold">{{ $lead->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Telepon</label>
                        <p class="font-semibold">{{ $lead->telepon ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Sumber</label>
                        <p class="font-semibold">{{ $lead->sumber ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <span class="px-2 py-1 text-xs rounded {{ 
                            $lead->status === 'new' ? 'bg-blue-100 text-blue-800' : 
                            ($lead->status === 'converted' ? 'bg-green-100 text-green-800' : 
                            ($lead->status === 'lost' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) 
                        }}">
                            {{ ucfirst($lead->status) }}
                        </span>
                    </div>
                    @if($lead->estimated_value)
                    <div>
                        <label class="text-sm text-gray-500">Estimated Value</label>
                        <p class="font-semibold text-green-600">Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($lead->assignedUser)
                    <div>
                        <label class="text-sm text-gray-500">Assigned To</label>
                        <p class="font-semibold">{{ $lead->assignedUser->name }}</p>
                    </div>
                    @endif
                    @if($lead->tanggal_kontak_terakhir)
                    <div>
                        <label class="text-sm text-gray-500">Tanggal Kontak Terakhir</label>
                        <p class="font-semibold">{{ $lead->tanggal_kontak_terakhir->format('d M Y H:i') }}</p>
                    </div>
                    @endif
                    @if($lead->kebutuhan)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Kebutuhan</label>
                        <p class="font-semibold">{{ $lead->kebutuhan }}</p>
                    </div>
                    @endif
                    @if($lead->catatan)
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Catatan</label>
                        <p class="font-semibold">{{ $lead->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('ecrm.leads.edit', $lead) }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-center">
                        Edit Lead
                    </a>
                    @if($lead->status !== 'converted')
                    <form action="{{ route('ecrm.leads.convert', $lead) }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" onclick="return confirm('Konversi lead ini menjadi client?')">
                            Convert to Client
                        </button>
                    </form>
                    @else
                    <p class="text-sm text-gray-500 text-center">Lead sudah dikonversi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

