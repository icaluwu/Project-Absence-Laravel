<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-sm text-gray-500">Status Absensi Hari Ini</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $dashboard['attendance_status'] }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-sm text-gray-500">Pengajuan Lembur Pending</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $dashboard['overtime_pending'] }}</div>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="text-sm text-gray-500">Pengajuan Izin/Cuti/Sakit Pending</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $dashboard['leave_pending'] }}</div>
                </div>
            </div>

            <!-- Announcements -->
            <div class="bg-white overflow-hidden shadow-sm rounded">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Pengumuman</h3>
                    <ul class="divide-y">
                        @forelse($dashboard['announcements'] as $a)
                            <li class="py-3">
                                <div class="font-medium">{{ $a->title }}</div>
                                <div class="text-sm text-gray-600">{{ $a->visible_from }} - {{ $a->visible_to }}</div>
<div class="mt-1 text-gray-800">{!! nl2br(e(\Illuminate\Support\Str::limit($a->content, 200))) !!}</div>
                            </li>
                        @empty
                            <li class="py-3 text-gray-500">Tidak ada pengumuman aktif.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
