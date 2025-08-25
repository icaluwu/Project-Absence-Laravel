<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Use app timezone (configured to Asia/Jakarta) so it resets every local day
        $today = Date::now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'ip_address' => request()->ip(),
            ]);
        }

        // If HR/Admin, also fetch all today's attendance for overview table
        $todayList = null;
        if ($user->hasAnyRole(['Admin','HR'])) {
            $todayList = Attendance::with('user')
                ->whereDate('date', $today)
                ->orderBy('check_in')
                ->get();
        }

        return view('attendance.index', [
            'attendance' => $attendance,
            'todayList' => $todayList,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Date::now()->toDateString();
        $now = Date::now()->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
            ]);
        }

        if (is_null($attendance->check_in)) {
            $attendance->check_in = $now;
            $attendance->ip_address = $request->ip();
            $attendance->save();
            return back()->with('status', 'Check-in berhasil');
        }

        if (is_null($attendance->check_out)) {
            $attendance->check_out = $now;
            $attendance->save();
            return back()->with('status', 'Check-out berhasil');
        }

        return back()->with('status', 'Anda sudah check-in dan check-out hari ini');
    }

    public function destroy(Attendance $attendance)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['Admin','HR'])) {
            abort(403);
        }
        $attendance->delete();
        return back()->with('status', 'Data absensi dihapus');
    }

    // Export monthly recap: CSV, PDF, XLSX
    public function exportMonthly(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['Admin','HR'])) {
            abort(403);
        }

        $request->validate([
            'month' => ['required','date_format:Y-m'],
            'format' => ['required','in:csv,pdf,xlsx'],
        ]);

        // Determine month boundaries
        [$year,$month] = explode('-', $request->string('month'));
        $start = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', (int)$year, (int)$month));
        $daysInMonth = (int)$start->daysInMonth;
        $end = $start->copy()->endOfMonth();

        // Prepare dataset: all users ordered by name
        $users = \App\Models\User::orderBy('name')->get(['id','name','departemen','jabatan']);
        $userIds = $users->pluck('id');

        // Fetch attendance map for the month
        $attRows = Attendance::whereIn('user_id', $userIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get(['user_id','date','check_in','check_out']);

        $map = [];
        foreach ($attRows as $row) {
            $d = (int)substr($row->date->format('d'), 0, 2);
            $map[$row->user_id][$d] = [
                'in' => $row->check_in,
                'out' => $row->check_out,
            ];
        }

        // Helper to build header days
        $days = range(1, $daysInMonth);

        $title = 'Rekap Absensi '.$start->translatedFormat('F Y');

        // CSV
        if ($request->format === 'csv') {
            $filename = 'rekap_absen_'.$start->format('Ym').'.csv';
            $callback = function () use ($users, $days, $map) {
                $out = fopen('php://output', 'w');
                // Header
                $header = array_merge(['No','Nama','Departemen','Jabatan'], array_map(fn($d)=> (string)$d, $days));
                fputcsv($out, $header);
                // Rows
                $no = 1;
                foreach ($users as $u) {
                    $row = [$no++, $u->name, $u->departemen, $u->jabatan];
                    foreach ($days as $d) {
                        $cell = '-';
                        if (isset($map[$u->id][$d])) {
                            $ci = $map[$u->id][$d]['in'];
                            $co = $map[$u->id][$d]['out'];
                            if ($ci || $co) {
                                $cell = trim(($ci ? substr($ci,0,5) : '-') . '/' . ($co ? substr($co,0,5) : '-'));
                            }
                        }
                        $row[] = $cell;
                    }
                    fputcsv($out, $row);
                }
                fclose($out);
            };
            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        // PDF via DomPDF
        if ($request->format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('attendance.recap_pdf', [
                'title' => $title,
                'users' => $users,
                'days' => $days,
                'map' => $map,
            ])->setPaper('a4', 'landscape');
            $filename = 'rekap_absen_'.$start->format('Ym').'.pdf';
            return $pdf->download($filename);
        }

        // XLSX via PhpSpreadsheet (optional dependency)
        if ($request->format === 'xlsx') {
            if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                return back()->with('error', 'Format XLSX memerlukan paket phpoffice/phpspreadsheet. Jalankan: composer require phpoffice/phpspreadsheet');
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Rekap');

            // Header
            $col = 1; // 1-based
            $headers = array_merge(['No','Nama','Departemen','Jabatan'], array_map(fn($d)=> (string)$d, $days));
            foreach ($headers as $h) {
                $addr = Coordinate::stringFromColumnIndex($col) . '1';
                $sheet->setCellValue($addr, $h);
                $col++;
            }

            // Rows
            $rowIdx = 2; $no = 1;
            foreach ($users as $u) {
                $col = 1;
                $addr = Coordinate::stringFromColumnIndex($col) . $rowIdx; $sheet->setCellValue($addr, $no++); $col++;
                $addr = Coordinate::stringFromColumnIndex($col) . $rowIdx; $sheet->setCellValue($addr, $u->name); $col++;
                $addr = Coordinate::stringFromColumnIndex($col) . $rowIdx; $sheet->setCellValue($addr, $u->departemen); $col++;
                $addr = Coordinate::stringFromColumnIndex($col) . $rowIdx; $sheet->setCellValue($addr, $u->jabatan); $col++;
                foreach ($days as $d) {
                    $cell = '-';
                    if (isset($map[$u->id][$d])) {
                        $ci = $map[$u->id][$d]['in'];
                        $co = $map[$u->id][$d]['out'];
                        if ($ci || $co) {
                            $cell = trim(($ci ? substr($ci,0,5) : '-') . '/' . ($co ? substr($co,0,5) : '-'));
                        }
                    }
                    $addr = Coordinate::stringFromColumnIndex($col) . $rowIdx;
                    $sheet->setCellValue($addr, $cell);
                    $col++;
                }
                $rowIdx++;
            }

            // Autosize columns
            $highestColumn = $sheet->getHighestColumn();
            foreach (range('A', $highestColumn) as $colName) {
                $sheet->getColumnDimension($colName)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'rekap_absen_'.$start->format('Ym').'.xlsx';
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        return back()->with('error', 'Format tidak didukung');
    }
}
