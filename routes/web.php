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
    $attendance = \App\Models\Attendance::where('user_id', $user->id)->where('date', $today)->first();
    $status = 'Belum absen';
    if ($attendance) {
        if ($attendance->check_in && !$attendance->check_out) $status = 'Sudah Check-in';
        if ($attendance->check_in && $attendance->check_out) $status = 'Selesai';
    }

    // Pending counts
    $overtimePending = $user->hasAnyRole(['Admin','HR'])
        ? \App\Models\OvertimeRequest::where('status','pending')->count()
        : \App\Models\OvertimeRequest::where('user_id',$user->id)->where('status','pending')->count();

    $leavePending = $user->hasAnyRole(['Admin','HR'])
        ? \App\Models\LeaveRequest::where('status','pending')->count()
        : \App\Models\LeaveRequest::where('user_id',$user->id)->where('status','pending')->count();

    // Active announcements (visible window or no dates)
    $todayDate = now()->toDateString();
    $ann = \App\Models\Announcement::query()
        ->where(function($q) use ($todayDate){
            $q->whereNull('visible_from')->orWhere('visible_from','<=',$todayDate);
        })
        ->where(function($q) use ($todayDate){
            $q->whereNull('visible_to')->orWhere('visible_to','>=',$todayDate);
        })
        ->latest()->take(5)->get();

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
    });
});

require __DIR__.'/auth.php';
