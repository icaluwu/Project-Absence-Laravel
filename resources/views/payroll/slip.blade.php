<div style="font-family: DejaVu Sans; font-size: 12px;">
    <h2>Slip Gaji</h2>
    <p>Nama: {{ $user->name }}</p>
    <p>Bulan/Tahun: {{ str_pad($payroll->month,2,'0',STR_PAD_LEFT) }}/{{ $payroll->year }}</p>
    <hr>
    <table width="100%" cellspacing="0" cellpadding="4" border="1">
        <tr><td>Gaji Pokok</td><td align="right">Rp {{ number_format($payroll->basic_salary,2,',','.') }}</td></tr>
        <tr><td>Uang Lembur</td><td align="right">Rp {{ number_format($payroll->overtime_pay,2,',','.') }}</td></tr>
        <tr><td>Potongan</td><td align="right">Rp {{ number_format($payroll->deductions,2,',','.') }}</td></tr>
        <tr><td><strong>Total Dibayarkan</strong></td><td align="right"><strong>Rp {{ number_format($payroll->net_salary,2,',','.') }}</strong></td></tr>
    </table>
    <p style="margin-top:10px;">Tanggal cetak: {{ now()->format('d/m/Y H:i') }}</p>
</div>
