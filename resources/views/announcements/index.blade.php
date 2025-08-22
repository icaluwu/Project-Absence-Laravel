<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengumuman</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded shadow overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b">
                    <h1 class="text-xl font-semibold">Pengumuman</h1>
                    @hasanyrole('Admin|HR')
                        <a href="{{ route('announcements.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Buat</a>
                    @endhasanyrole
                </div>
                <ul>
                    @forelse($items as $it)
                        <li class="border-b p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">{{ $it->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $it->visible_from }} - {{ $it->visible_to }}</div>
                                </div>
                                @hasanyrole('Admin|HR')
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('announcements.edit', $it) }}" class="px-3 py-1 border rounded">Edit</a>
                                    <form method="POST" action="{{ route('announcements.destroy', $it) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                    </form>
                                </div>
                                @endhasanyrole
                            </div>
                            <div class="mt-2 prose max-w-none">{!! nl2br(e($it->content)) !!}</div>
                        </li>
                    @empty
                        <li class="p-4 text-gray-500">Belum ada pengumuman.</li>
                    @endforelse
                </ul>
                <div class="p-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
