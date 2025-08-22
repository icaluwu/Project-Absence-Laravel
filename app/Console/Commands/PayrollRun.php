<?php

namespace App\Console\Commands;

use App\Models\OvertimeRequest;
use App\Models\Payroll;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class PayrollRun extends Command
{
    protected $signature = 'payroll:run {--month=} {--year=}';

    protected $description = 'Generate monthly payroll for all employees';

    public function handle()
    {
        $month = (int)($this->option('month') ?: now()->month);
        $year = (int)($this->option('year') ?: now()->year);

        $this->info("Running payroll for $month/$year...");

        $users = User::role('Karyawan')->get();
        foreach ($users as $user) {
            $basic = (float)($user->gaji_pokok ?? 0);

            // Sum approved overtime hours this month
            $otHours = OvertimeRequest::where('user_id',$user->id)
                ->where('status','approved')
                ->whereMonth('date',$month)
                ->whereYear('date',$year)
                ->sum('hours');
            // Example overtime rate: 1.5% of basic per hour
            $overtimePay = round(($basic * 0.015) * $otHours, 2);

            // Deductions: telat & alpha (hari kerja)
            $deductions = 0;
            // Hitung jumlah hari kerja dalam bulan (Senin-Jumat) dan telat/alpha
            $start = Date::create($year, $month, 1);
            $end = $start->copy()->endOfMonth();

            // Ambil leave approved dalam rentang
            $leaves = \App\Models\LeaveRequest::where('user_id',$user->id)
                    ->where('status','approved')
                    ->whereDate('end_date','>=',$start->toDateString())
                    ->whereDate('start_date','<=',$end->toDateString())
                    ->get();

            // Ambil attendance dalam rentang
            $attendances = \App\Models\Attendance::where('user_id',$user->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get()
                ->keyBy('date');

            $workdays = 0;
            $lateMinutesTotal = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                // Skip weekend
                if (in_array($d->dayOfWeekIso, [6,7])) continue; // 6=Sabtu,7=Minggu
                $workdays++;

                $dateStr = $d->toDateString();
                // Cek apakah tanggal ini termasuk approved leave
                $onLeave = $leaves->first(function($lv) use ($dateStr){
                    return $dateStr >= $lv->start_date && $dateStr <= $lv->end_date;
                });
                if ($onLeave) continue; // tidak dihitung telat/alpha

                $att = $attendances->get($dateStr);
                if (!$att || !$att->check_in) {
                    // Alpha hari ini → potongan 1 hari kerja
                    $dailyRate = $workdays > 0 ? ($basic / max($workdays,1)) : 0;
                    $deductions += $dailyRate;
                    continue;
                }

                // Telat jika check_in > 08:00
                $scheduled = Date::create($year, $month, (int)$d->format('d'))->setTime(8,0,0);
                $checkIn = Date::parse($att->date.' '.$att->check_in);
                if ($checkIn->gt($scheduled)) {
                    $minutesLate = $scheduled->diffInMinutes($checkIn);
                    $lateMinutesTotal += $minutesLate;
                }
            }

            // Potongan telat per menit (gaji pokok / (workdays*8 jam*60))
            $perMinuteRate = $workdays > 0 ? ($basic / ($workdays * 8 * 60)) : 0;
            $deductions += round($perMinuteRate * $lateMinutesTotal, 2);

            $data = [
                'user_id' => $user->id,
                'month' => $month,
                'year' => $year,
                'basic_salary' => $basic,
                'overtime_pay' => $overtimePay,
                'deductions' => $deductions,
                'net_salary' => $basic + $overtimePay - $deductions,
            ];

            $payroll = Payroll::updateOrCreate(
                ['user_id'=>$user->id,'month'=>$month,'year'=>$year],
                $data
            );

            // Generate PDF slip
            $pdf = Pdf::loadView('payroll.slip', ['payroll'=>$payroll,'user'=>$user]);
            $filename = 'payslips/'.Date::now()->format('Ymd_His')."_{$user->id}_{$month}_{$year}.pdf";
            Storage::disk('public')->put($filename, $pdf->output());
            $payroll->update(['pdf_path' => $filename]);

            $this->line("- {$user->name}: net ".number_format($payroll->net_salary,2));
        }

        $this->info('Payroll completed.');
        return self::SUCCESS;
    }
}
