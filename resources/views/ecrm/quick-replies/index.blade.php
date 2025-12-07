@extends('layouts.app')

@section('title', 'Balasan Cepat - e-CRM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Balasan Cepat</h1>
        <a href="{{ route('ecrm.quick-replies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Balasan Cepat
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pertanyaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jawaban</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($quickReplies as $quickReply)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $quickReply->pertanyaan }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ Str::limit($quickReply->jawaban, 100) }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $quickReply->kategori ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ $quickReply->aktif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $quickReply->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            @if($quickReply->use_ai)
                            <span class="ml-2 px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">AI</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('ecrm.quick-replies.edit', $quickReply) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ubah</a>
                            <form action="{{ route('ecrm.quick-replies.destroy', $quickReply) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data quick reply</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $quickReplies->links() }}
    </div>
</div>
@endsection

