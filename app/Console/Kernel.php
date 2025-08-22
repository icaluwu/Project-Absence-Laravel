<?php

namespace App\Console;

use App\Console\Commands\PayrollRun;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan tiap tanggal 1 jam 01:00
        $schedule->command('payroll:run')->monthlyOn(1, '01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

