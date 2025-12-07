@extends('layouts.app')

@section('title', 'Faktur - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Faktur</h1>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('ecrm.invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Buat Faktur
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Faktur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium">{{ $invoice->nomor_invoice }}</div>
                            <div class="text-sm text-gray-500">{{ $invoice->tanggal_invoice->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $invoice->client->nama }}</td>
                        <td class="px-6 py-4">{{ $invoice->order->nomor_order }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                            @if($invoice->total_paid > 0)
                                <div class="text-xs text-gray-500">
                                    Terbayar: Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ 
                                $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                            }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="{{ $invoice->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                {{ $invoice->tanggal_jatuh_tempo->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('ecrm.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @if(Auth::user()->role === 'admin')
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('ecrm.invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                                @if((Auth::user()->role === 'cs' || Auth::user()->role === 'admin') && $invoice->status !== 'paid')
                                <span class="text-gray-300">|</span>
                                <button type="button" 
                                        onclick="openRemindModal('{{ route('ecrm.invoices.remind', $invoice) }}', '{{ $invoice->nomor_invoice }}')"
                                        class="text-orange-600 hover:text-orange-900">
                                    Kirim Reminder
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data invoice</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
