<div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Detail Payroll</h1>
    <div class="bg-white rounded shadow p-4 space-y-2">
        <div><strong>Karyawan:</strong> {{ $payroll->user->name }}</div>
        <div><strong>Periode:</strong> {{ str_pad($payroll->month,2,'0',STR_PAD_LEFT) }}/{{ $payroll->year }}</div>
                <div><strong>Net:</strong> Rp {{ number_format($payroll->net_salary,2,',','.') }}</div>
        <div><strong>Status:</strong> {{ $payroll->paid_at ? 'Sudah dibayar' : 'Belum' }}</div>
        <div>
            @if($payroll->pdf_path)
                <a class="text-blue-600 underline" href="{{ asset('storage/'.$payroll->pdf_path) }}" target="_blank">Unduh PDF</a>
            @endif
        </div>
    </div>
</div>
