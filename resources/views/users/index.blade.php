<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Karyawan</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white rounded shadow p-4 mb-4">
                <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Tambah Karyawan</a>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-3">Nama</th>
                            <th class="text-left p-3">Email</th>
                            <th class="text-left p-3">NIK</th>
                            <th class="text-left p-3">Departemen</th>
                            <th class="text-left p-3">Jabatan</th>
                            <th class="text-left p-3">Gaji Pokok</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr class="border-t">
                                <td class="p-3">{{ $u->name }}</td>
                                <td class="p-3">{{ $u->email }}</td>
                                <td class="p-3">{{ $u->nik }}</td>
                                <td class="p-3">{{ $u->departemen }}</td>
                                <td class="p-3">{{ $u->jabatan }}</td>
                                <td class="p-3">{{ number_format($u->gaji_pokok,2,',','.') }}</td>
                                <td class="p-3">{{ $u->status_karyawan }}</td>
                                <td class="p-3">
                                    <a href="{{ route('users.edit', $u) }}" class="px-3 py-1 border rounded">Edit</a>
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
