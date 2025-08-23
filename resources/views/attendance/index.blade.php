@php
    $checkedIn = !empty($attendance->check_in);
    $checkedOut = !empty($attendance->check_out);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Absensi</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white shadow rounded p-6">
                <h1 class="text-xl font-semibold mb-4">Absensi Hari Ini</h1>
                <p class="text-sm text-gray-600 mb-2">Tanggal (Asia/Jakarta): {{ now()->format('d M Y') }}</p>
                <div class="flex items-center gap-3 mb-6">
                    <span class="px-3 py-1 rounded bg-gray-100">Check-in: {{ $attendance->check_in ? substr($attendance->check_in,0,5) : '-' }}</span>
                    <span class="px-3 py-1 rounded bg-gray-100">Check-out: {{ $attendance->check_out ? substr($attendance->check_out,0,5) : '-' }}</span>
                </div>
                <form method="POST" action="{{ route('attendance.store') }}" class="flex gap-3">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded bg-blue-600 text-white disabled:opacity-50" {{ $checkedIn && $checkedOut ? 'disabled' : '' }}>
                        {{ $checkedIn ? ($checkedOut ? 'Selesai' : 'Check-out') : 'Check-in' }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded border">Kembali</a>
                </form>
            </div>

            @hasanyrole('Admin|HR')
            <div class="bg-white shadow rounded p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Rekap Hari Ini (Asia/Jakarta)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left p-2">Karyawan</th>
                                <th class="text-left p-2">Check-in</th>
                                <th class="text-left p-2">Check-out</th>
                                <th class="text-left p-2">IP</th>
                                <th class="text-left p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse(($todayList ?? collect()) as $row)
                            <tr class="border-t">
                                <td class="p-2">{{ optional($row->user)->name }}</td>
                                <td class="p-2">{{ $row->check_in ? substr($row->check_in,0,5) : '-' }}</td>
                                <td class="p-2">{{ $row->check_out ? substr($row->check_out,0,5) : '-' }}</td>
                                <td class="p-2">{{ $row->ip_address ?? '-' }}</td>
                                <td class="p-2">
                                    <form method="POST" action="{{ route('attendance.destroy', $row) }}" onsubmit="return confirm('Hapus data absensi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-3 text-gray-500">Belum ada aktivitas absensi hari ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endhasanyrole
        </div>
    </div>
</x-app-layout>
