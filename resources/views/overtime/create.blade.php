<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan Lembur</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 rounded shadow">
                <form method="POST" action="{{ route('overtime.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="date" value="Tanggal" class="mb-1" />
                        <input id="date" type="date" name="date" value="{{ old('date') }}" required
                               class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="hours" value="Jumlah Jam" class="mb-1" />
                        <input id="hours" type="number" step="0.5" min="0.5" name="hours" value="{{ old('hours') }}" required
                               class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        <x-input-error :messages="$errors->get('hours')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="reason" value="Alasan" class="mb-1" />
                        <input id="reason" type="text" name="reason" value="{{ old('reason') }}" placeholder="opsional"
                               class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>

                    <div class="flex gap-3">
                        <x-primary-button>Kirim</x-primary-button>
                        <a href="{{ route('overtime.index') }}" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
