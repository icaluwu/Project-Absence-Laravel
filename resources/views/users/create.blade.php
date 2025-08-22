<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Karyawan</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6">
                <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input name="name" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" class="border rounded w-full p-2" required>
                    </div>
                    <div>
                        <label class="block mb-1">Password (opsional)</label>
                        <input type="password" name="password" class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label class="block mb-1">NIK</label>
                        <input name="nik" class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Departemen</label>
                        <input name="departemen" class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Jabatan</label>
                        <input name="jabatan" class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="border rounded w-full p-2">
                    </div>
                    <div>
                        <label class="block mb-1">Gaji Pokok</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="number" step="0.01" name="gaji_pokok" class="border rounded w-full p-2 pl-10" placeholder="0.00">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-1">Status</label>
                        <input name="status_karyawan" class="border rounded w-full p-2" value="aktif">
                    </div>
                    <div class="md:col-span-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        <a href="{{ route('users.index') }}" class="px-4 py-2 border rounded">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
