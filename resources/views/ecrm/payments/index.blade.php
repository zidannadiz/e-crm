@extends('layouts.app')

@section('title', 'Pembayaran - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Pembayaran</h1>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('ecrm.payments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Pembayaran
        </a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $payment->invoice->nomor_invoice }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $payment->invoice->client->nama }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $payment->tanggal_pembayaran->format('d M Y') }}</td>
                        <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ 
                                $payment->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                            }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('ecrm.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                @if(Auth::user()->role === 'admin' && $payment->status === 'pending')
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('ecrm.payments.verify', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Verifikasi pembayaran ini?')">
                                        Verify
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data pembayaran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection
