@extends('layouts.app')

@section('title', 'Detail Pembayaran - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detail Pembayaran</h1>
        <div class="flex gap-2">
            <a href="{{ route('ecrm.payments.edit', $payment) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Edit
            </a>
            @if($payment->status === 'pending')
            <form action="{{ route('ecrm.payments.verify', $payment) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" onclick="return confirm('Verifikasi pembayaran ini?')">
                    Verify
                </button>
            </form>
            <form action="{{ route('ecrm.payments.reject', $payment) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Tolak pembayaran ini?')">
                    Reject
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Informasi Pembayaran</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-500">Invoice</label>
                <p class="font-semibold">
                    <a href="{{ route('ecrm.invoices.show', $payment->invoice) }}" class="text-blue-600 hover:underline">
                        {{ $payment->invoice->nomor_invoice }}
                    </a>
                </p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Client</label>
                <p class="font-semibold">{{ $payment->invoice->client->nama }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Jumlah</label>
                <p class="font-semibold text-green-600 text-lg">Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tanggal Pembayaran</label>
                <p class="font-semibold">{{ $payment->tanggal_pembayaran->format('d M Y') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Metode Pembayaran</label>
                <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Status</label>
                <span class="px-2 py-1 text-xs rounded {{ 
                    $payment->status === 'verified' ? 'bg-green-100 text-green-800' : 
                    ($payment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
            @if($payment->verifiedBy)
            <div>
                <label class="text-sm text-gray-500">Diverifikasi Oleh</label>
                <p class="font-semibold">{{ $payment->verifiedBy->name }}</p>
            </div>
            @endif
            @if($payment->verified_at)
            <div>
                <label class="text-sm text-gray-500">Tanggal Verifikasi</label>
                <p class="font-semibold">{{ $payment->verified_at->format('d M Y H:i') }}</p>
            </div>
            @endif
            @if($payment->catatan)
            <div class="col-span-2">
                <label class="text-sm text-gray-500">Catatan</label>
                <p class="font-semibold">{{ $payment->catatan }}</p>
            </div>
            @endif
            @if($payment->bukti_pembayaran)
            <div class="col-span-2">
                <label class="text-sm text-gray-500">Bukti Pembayaran</label>
                <div class="mt-2">
                    @if(str_ends_with($payment->bukti_pembayaran, '.pdf'))
                        <a href="{{ Storage::url($payment->bukti_pembayaran) }}" target="_blank" class="text-blue-600 hover:underline">
                            Lihat PDF
                        </a>
                    @else
                        <img src="{{ Storage::url($payment->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-w-md rounded shadow">
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('ecrm.payments.edit', $payment) }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
            Edit
        </a>
        <a href="{{ route('ecrm.payments.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
            Kembali
        </a>
    </div>
</div>
@endsection

