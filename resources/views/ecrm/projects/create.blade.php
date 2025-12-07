@extends('layouts.app')

@section('title', 'Tambah Project - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Tambah Project</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.projects.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                    <select name="client_id" required class="w-full border rounded px-4 py-2">
                        <option value="">Pilih Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->nama }} {{ $client->perusahaan ? '(' . $client->perusahaan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Project *</label>
                    <input type="text" name="nama_proyek" value="{{ old('nama_proyek') }}" required class="w-full border rounded px-4 py-2">
                    @error('nama_proyek') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Desain *</label>
                    <select name="jenis_desain" required class="w-full border rounded px-4 py-2">
                        <option value="logo" {{ old('jenis_desain') == 'logo' ? 'selected' : '' }}>Logo</option>
                        <option value="branding" {{ old('jenis_desain') == 'branding' ? 'selected' : '' }}>Branding</option>
                        <option value="web_design" {{ old('jenis_desain') == 'web_design' ? 'selected' : '' }}>Web Design</option>
                        <option value="ui_ux" {{ old('jenis_desain') == 'ui_ux' ? 'selected' : '' }}>UI/UX</option>
                        <option value="print_design" {{ old('jenis_desain') == 'print_design' ? 'selected' : '' }}>Print Design</option>
                        <option value="packaging" {{ old('jenis_desain') == 'packaging' ? 'selected' : '' }}>Packaging</option>
                        <option value="social_media" {{ old('jenis_desain') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="lainnya" {{ old('jenis_desain') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_desain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full border rounded px-4 py-2">
                        <option value="quotation" {{ old('status') == 'quotation' ? 'selected' : '' }}>Quotation</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>Review</option>
                        <option value="revision" {{ old('status') == 'revision' ? 'selected' : '' }}>Revision</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget</label>
                    <input type="number" name="budget" value="{{ old('budget') }}" step="0.01" class="w-full border rounded px-4 py-2">
                    @error('budget') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" class="w-full border rounded px-4 py-2">
                    @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border rounded px-4 py-2">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="catatan" rows="3" class="w-full border rounded px-4 py-2">{{ old('catatan') }}</textarea>
                    @error('catatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ route('ecrm.projects.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

