<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payroll</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
            @endif
            <div class="bg-white rounded shadow p-4 mb-6">
                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('payroll.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <select id="user_id" name="user_id" class="border rounded p-2" required>
                        <option value="">Pilih Karyawan/Admin/HR</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" data-salary="{{ (float)($u->gaji_pokok ?? 0) }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="month" min="1" max="12" class="border rounded p-2" placeholder="Bulan" value="{{ old('month', now()->format('n')) }}" required>
                    <input type="number" name="year" min="2000" class="border rounded p-2" placeholder="Tahun" value="{{ old('year', now()->format('Y')) }}" required>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                        <input id="basic_salary" type="number" step="0.01" name="basic_salary" class="border rounded p-2 pl-10 w-full" placeholder="Gaji Pokok" required>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                        <input type="number" step="0.01" name="overtime_pay" class="border rounded p-2 pl-10 w-full" placeholder="Lembur" required>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                        <input type="number" step="0.01" name="deductions" class="border rounded p-2 pl-10 w-full" placeholder="Potongan" required>
                    </div>
                    <div class="md:col-span-3">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Generate Slip</button>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left p-3">User</th>
                            <th class="text-left p-3">Periode</th>
                            <th class="text-left p-3">Net</th>
                            <th class="text-left p-3">PDF</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $it)
                            <tr class="border-t">
                                <td class="p-3">{{ $it->user->name }}</td>
                                <td class="p-3">{{ str_pad($it->month,2,'0',STR_PAD_LEFT) }}/{{ $it->year }}</td>
                                <td class="p-3">Rp {{ number_format($it->net_salary,2,',','.') }}</td>
                                <td class="p-3">
                                    @if($it->pdf_path)
                                        <a class="text-blue-600 underline" href="{{ asset('storage/'.$it->pdf_path) }}" target="_blank">Unduh</a>
                                    @else - @endif
                                </td>
                                <td class="p-3">{{ $it->paid_at ? 'Sudah dibayar' : 'Belum' }}</td>
                                <td class="p-3">
                                    @if(!$it->paid_at)
                                    <form method="POST" action="{{ route('payroll.update', $it) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="px-3 py-1 bg-green-600 text-white rounded">Tandai Dibayar</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('user_id');
            const input = document.getElementById('basic_salary');
            if (!select || !input) return;

            function updateSalary() {
                const opt = select.options[select.selectedIndex];
                const salary = opt && opt.dataset ? opt.dataset.salary : '';
                if (salary !== undefined) {
                    input.value = salary || '';
                }
            }

            select.addEventListener('change', updateSalary);
            updateSalary();
        });
    </script>
</x-app-layout>
