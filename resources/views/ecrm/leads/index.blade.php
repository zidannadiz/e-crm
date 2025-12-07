@extends('layouts.app')

@section('title', 'Leads - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Leads</h1>
        <a href="{{ route('ecrm.leads.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Lead
        </a>
    </div>

    <div class="bg-white rounded-lg shadow mb-4 p-4">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari lead..." class="flex-1 border rounded px-4 py-2">
            <select name="status" class="border rounded px-4 py-2">
                <option value="">Semua Status</option>
                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estimated Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leads as $lead)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $lead->nama }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>{{ $lead->email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $lead->telepon ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $lead->sumber ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ 
                                $lead->status === 'new' ? 'bg-blue-100 text-blue-800' : 
                                ($lead->status === 'converted' ? 'bg-green-100 text-green-800' : 
                                ($lead->status === 'lost' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) 
                            }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $lead->estimated_value ? 'Rp ' . number_format($lead->estimated_value, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('ecrm.leads.show', $lead) }}" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                            <a href="{{ route('ecrm.leads.edit', $lead) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @if($lead->status !== 'converted')
                            <form action="{{ route('ecrm.leads.convert', $lead) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Convert</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data lead</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $leads->links() }}
    </div>
</div>
@endsection

