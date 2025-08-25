<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Attendance status today
    $today = now()->toDateString();
    $attendance = \App\Models\Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();
    $status = 'Belum absen';
    if ($attendance) {
        if ($attendance->check_in && !$attendance->check_out) $status = 'Sudah Check-in';
        if ($attendance->check_in && $attendance->check_out) $status = 'Selesai';
    }

    // Pending counts
    // Pending counts (realtime)
    $overtimePendingQuery = \App\Models\OvertimeRequest::query()->where('status','pending');
    $leavePendingQuery = \App\Models\LeaveRequest::query()->where('status','pending');

    if (!$user->hasAnyRole(['Admin','HR'])) {
        $overtimePendingQuery->where('user_id', $user->id);
        $leavePendingQuery->where('user_id', $user->id);
    }

    $overtimePending = $overtimePendingQuery->count();
    $leavePending = $leavePendingQuery->count();

    // Active announcements (visible window or no dates)
    $todayDate = now()->toDateString();
    // Active announcements (latest 5)
    $ann = \App\Models\Announcement::query()
        ->when(true, function($q) use ($todayDate) {
            $q->where(function($q2) use ($todayDate){
                $q2->whereNull('visible_from')->orWhere('visible_from','<=',$todayDate);
            })->where(function($q3) use ($todayDate){
                $q3->whereNull('visible_to')->orWhere('visible_to','>=',$todayDate);
            });
        })
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    return view('dashboard', [
        'dashboard' => [
            'attendance_status' => $status,
            'overtime_pending' => $overtimePending,
            'leave_pending' => $leavePending,
            'announcements' => $ann,
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Attendance / Overtime / Leave (semua user login). Pembatasan ada di FormRequest/Policy
    Route::resource('attendance', AttendanceController::class)->only(['index','store']);
    Route::resource('overtime', OvertimeController::class)->only(['index','create','store','show','update']);
    Route::resource('leave', LeaveController::class)->only(['index','create','store','show','update']);

    // Admin & HR only
    Route::middleware('role:Admin|HR')->group(function () {
        Route::get('overtime/export/csv', [OvertimeController::class, 'exportCsv'])->name('overtime.export.csv');
        Route::get('leave/export/csv', [LeaveController::class, 'exportCsv'])->name('leave.export.csv');
        Route::resource('payroll', PayrollController::class);
        Route::get('payroll/export/csv', [PayrollController::class, 'exportCsv'])->name('payroll.export.csv');
        Route::resource('announcements', AnnouncementController::class);
        Route::resource('users', UserManagementController::class);
        // Attendance admin actions
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
        Route::get('attendance/export/monthly', [AttendanceController::class, 'exportMonthly'])->name('attendance.export.monthly');
    });
});

require __DIR__.'/auth.php';
