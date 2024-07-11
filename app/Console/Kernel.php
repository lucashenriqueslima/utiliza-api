<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\AuvoCustomerUpdateCommand::class,
        \App\Console\Commands\FieldControlCustomerUpdateCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('auvo-customer-update')->dailyAt('02:00');
        $schedule->command('field-control-customer-update')->dailyAt('03:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
