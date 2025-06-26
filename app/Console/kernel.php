<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AutoStopWork::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Production-optimized auto-stop command
        $schedule->command('work:auto-stop')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('logs/auto-stop.log'));

        // Alternative: Run every 5 minutes for less server load
        // $schedule->command('work:auto-stop')
        //          ->everyFiveMinutes()
        //          ->withoutOverlapping()
        //          ->runInBackground()
        //          ->appendOutputTo(storage_path('logs/auto-stop.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}