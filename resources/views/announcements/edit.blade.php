<div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Pengumuman</h1>
    <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block mb-1">Judul</label>
            <input type="text" name="title" value="{{ $announcement->title }}" class="border rounded w-full p-2" required>
        </div>
        <div>
            <label class="block mb-1">Konten</label>
            <textarea name="content" class="border rounded w-full p-2" rows="6" required>{{ $announcement->content }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1">Tampilkan dari</label>
                <input type="date" name="visible_from" value="{{ $announcement->visible_from }}" class="border rounded w-full p-2">
            </div>
            <div>
                <label class="block mb-1">Sampai</label>
                <input type="date" name="visible_to" value="{{ $announcement->visible_to }}" class="border rounded w-full p-2">
            </div>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
