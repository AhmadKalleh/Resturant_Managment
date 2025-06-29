<?php

namespace App\Console;

use App\Jobs\AutoCancelExpiredReservations;
use App\Jobs\DeleteExpiredOffersJob;
use App\Jobs\ReduceNoShowCounts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ReduceNoShowCounts)->weekly();

        $schedule->job(new AutoCancelExpiredReservations)->everyMinute();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
