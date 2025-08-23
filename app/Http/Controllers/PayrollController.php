<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Date;

class PayrollController extends Controller
{
    public function index()
    {
        $items = Payroll::with('user')->latest()->paginate(12);
        // Ambil semua user (Admin, HR, Karyawan) beserta gaji pokok untuk auto-fill di form
        $users = User::query()->orderBy('name')->get(['id','name','gaji_pokok']);
        return view('payroll.index', compact('items','users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'month' => ['required','integer','between:1,12'],
            'year' => ['required','integer','min:2000'],
            'basic_salary' => ['nullable','numeric'],
            'overtime_pay' => ['nullable','numeric'],
            'deductions' => ['nullable','numeric'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $basic = $validated['basic_salary'] ?? (float)($user->gaji_pokok ?? 0);

        // Hitung lembur jika tidak diisi: ambil jam lembur approved bulan tsb dengan rate 1.5% per jam dari gaji pokok
        if (!array_key_exists('overtime_pay', $validated) || $validated['overtime_pay'] === null) {
            $otHours = \App\Models\OvertimeRequest::where('user_id',$user->id)
                ->where('status','approved')
                ->whereMonth('date', $validated['month'])
                ->whereYear('date', $validated['year'])
                ->sum('hours');
            $overtimePay = round(($basic * 0.015) * $otHours, 2);
        } else {
            $overtimePay = (float)$validated['overtime_pay'];
        }

        // Hitung potongan jika tidak diisi: gunakan 0 (atau bisa gunakan rumus payroll:run jika diinginkan)
        if (!array_key_exists('deductions', $validated) || $validated['deductions'] === null) {
            $deductions = 0.0;
        } else {
            $deductions = (float)$validated['deductions'];
        }

        $data = [
            'user_id' => $user->id,
            'month' => (int)$validated['month'],
            'year' => (int)$validated['year'],
            'basic_salary' => $basic,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'net_salary' => $basic + $overtimePay - $deductions,
        ];

        $payroll = Payroll::updateOrCreate(
            ['user_id' => $data['user_id'], 'month' => $data['month'], 'year' => $data['year']],
            $data
        );

        // Generate PDF
        $pdf = Pdf::loadView('payroll.slip', ['payroll'=>$payroll, 'user'=>$user]);
        $filename = 'payslips/'.Date::now()->format('Ymd_His')."_{$user->id}_{$data['month']}_{$data['year']}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        $payroll->update(['pdf_path' => $filename]);

        return redirect()->route('payroll.index')->with('status','Slip gaji dibuat');
    }

    public function show(Payroll $payroll)
    {
        return view('payroll.show', compact('payroll'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        // mark as paid
        $payroll->paid_at = now();
        $payroll->save();
        return back()->with('status','Ditandai sudah dibayar');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return back()->with('status','Data payroll dihapus');
    }

    public function exportCsv()
    {
        $filename = 'payroll_'.now()->format('Ymd_His').'.csv';
        $callback = function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Karyawan','Bulan','Tahun','Gaji Pokok','Lembur','Potongan','Net','Status']);
            $query = \App\Models\Payroll::with('user')->orderByDesc('year')->orderByDesc('month');
            foreach ($query->cursor() as $it) {
                fputcsv($out, [
                    optional($it->user)->name,
                    $it->month,
                    $it->year,
                    number_format($it->basic_salary,2,'.',''),
                    number_format($it->overtime_pay,2,'.',''),
                    number_format($it->deductions,2,'.',''),
                    number_format($it->net_salary,2,'.',''),
                    $it->paid_at ? 'Sudah dibayar' : 'Belum',
                ]);
            }
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
