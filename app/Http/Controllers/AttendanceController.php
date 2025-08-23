<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

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
}
