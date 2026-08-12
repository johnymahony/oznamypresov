<?php

namespace App\Console;

use App\Console\Commands\CronTest;
use App\Console\Commands\Objednavky;
use App\Console\Commands\Ocr;
use App\Console\Commands\Scrapper;
use App\Console\Commands\ScrapperFaktury;
use App\Console\Commands\ScrapperObjednavky;
use App\Console\Commands\ScrapperZmluvy;
use App\Console\Commands\SyncStreets;
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
        Scrapper::class,
        ScrapperZmluvy::class,
        ScrapperObjednavky::class,
        ScrapperFaktury::class,
        Objednavky::class,
        CronTest::class,
        SyncStreets::class,
        Ocr::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        city()->scheduleCrons($schedule);
        $schedule->command('CruncherDownload')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
