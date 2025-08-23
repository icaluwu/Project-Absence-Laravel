<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Lembur</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white rounded shadow overflow-x-auto">
                <div class="p-4">
                    @hasanyrole('Admin|HR|Karyawan')
                        <a href="{{ route('overtime.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Ajukan Lembur</a>
                    @endhasanyrole
                </div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-3">Tanggal</th>
                            <th class="text-left p-3">Jam</th>
                            <th class="text-left p-3">Alasan</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr class="border-t">
                                <td class="p-3">{{ \Illuminate\Support\Carbon::parse($it->date)->format('d M Y') }}</td>
                                <td class="p-3">{{ number_format($it->hours, 2) }}</td>
                                <td class="p-3">{{ $it->reason }}</td>
                                <td class="p-3">{{ strtoupper($it->status) }}</td>
                                <td class="p-3">
                                    @hasanyrole('Admin|HR')
                                    <form method="POST" action="{{ route('overtime.update', $it) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="border rounded p-1">
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <button class="ml-2 px-3 py-1 bg-green-600 text-white rounded">Update</button>
                                    </form>
                                    @endhasanyrole
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
