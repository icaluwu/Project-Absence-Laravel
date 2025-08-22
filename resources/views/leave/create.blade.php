{{-- resources/views/leave/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajukan Izin/Cuti/Sakit
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash status (opsional) --}}
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Global validation summary (opsional) --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow">
                <form method="POST" action="{{ route('leave.store') }}" class="p-6 space-y-5" enctype="multipart/form-data">
                    @csrf

                    {{-- Jenis --}}
                    <div>
                        <label for="type" class="block mb-1 font-medium text-gray-700">Jenis</label>
                        <select id="type" name="type"
                                class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="izin"  {{ old('type') === 'izin'  ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ old('type') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="cuti"  {{ old('type') === 'cuti'  ? 'selected' : '' }}>Cuti</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block mb-1 font-medium text-gray-700">Mulai</label>
                            <input id="start_date" type="date" name="start_date"
                                   value="{{ old('start_date') }}"
                                   class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block mb-1 font-medium text-gray-700">Selesai</label>
                            <input id="end_date" type="date" name="end_date"
                                   value="{{ old('end_date') }}"
                                   class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label for="notes" class="block mb-1 font-medium text-gray-700">Catatan</label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="opsional"
                                  class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lampiran --}}
                    <div>
                        <label for="attachment" class="block mb-1 font-medium text-gray-700">
                            Lampiran (PDF/JPG/PNG, maks 4 MB)
                        </label>
                        <input id="attachment" type="file" name="attachment"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('attachment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Tipe yang diizinkan: PDF, JPG, PNG. Maksimum ukuran 4 MB.</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Kirim
                        </button>
                        <a href="{{ route('leave.index') }}"
                           class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            {{-- Kembali ke daftar (opsional) --}}
            <div class="mt-4">
                <a href="{{ route('leave.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke daftar</a>
            </div>
        </div>
    </div>
</x-app-layout>
