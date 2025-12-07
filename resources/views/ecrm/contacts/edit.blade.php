@extends('layouts.app')

@section('title', 'Edit Kontak - e-CRM')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Edit Kontak</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('ecrm.contacts.update', $contact) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                    <select name="client_id" class="w-full border rounded px-4 py-2">
                        <option value="">Pilih Client (Opsional)</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $contact->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->nama }} {{ $client->perusahaan ? '(' . $client->perusahaan . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id" class="w-full border rounded px-4 py-2">
                        <option value="">Pilih Project (Opsional)</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $contact->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->nama_proyek }} - {{ $project->client->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe *</label>
                    <select name="tipe" required class="w-full border rounded px-4 py-2">
                        <option value="call" {{ old('tipe', $contact->tipe) == 'call' ? 'selected' : '' }}>Call</option>
                        <option value="email" {{ old('tipe', $contact->tipe) == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="meeting" {{ old('tipe', $contact->tipe) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="whatsapp" {{ old('tipe', $contact->tipe) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="lainnya" {{ old('tipe', $contact->tipe) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arah *</label>
                    <select name="arah" required class="w-full border rounded px-4 py-2">
                        <option value="inbound" {{ old('arah', $contact->arah) == 'inbound' ? 'selected' : '' }}>Inbound</option>
                        <option value="outbound" {{ old('arah', $contact->arah) == 'outbound' ? 'selected' : '' }}>Outbound</option>
                    </select>
                    @error('arah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kontak *</label>
                    <input type="datetime-local" name="tanggal_kontak" value="{{ old('tanggal_kontak', $contact->tanggal_kontak->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-4 py-2">
                    @error('tanggal_kontak') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subjek *</label>
                    <input type="text" name="subjek" value="{{ old('subjek', $contact->subjek) }}" required class="w-full border rounded px-4 py-2">
                    @error('subjek') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesan *</label>
                    <textarea name="pesan" rows="5" required class="w-full border rounded px-4 py-2">{{ old('pesan', $contact->pesan) }}</textarea>
                    @error('pesan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Update
                </button>
                <a href="{{ route('ecrm.contacts.show', $contact) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

