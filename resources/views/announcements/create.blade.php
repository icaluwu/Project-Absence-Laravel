<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Pengumuman</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('announcements.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1">Judul</label>
                        <input type="text" name="title" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label class="block mb-1">Konten</label>
                        <textarea name="content" class="border rounded w-full p-2" rows="6" required></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1">Tampilkan dari</label>
                            <input type="date" name="visible_from" class="border rounded w-full p-2">
                        </div>
                        <div>
                            <label class="block mb-1">Sampai</label>
                            <input type="date" name="visible_to" class="border rounded w-full p-2">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        <a href="{{ route('announcements.index') }}" class="px-4 py-2 border rounded">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
