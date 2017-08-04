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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\SubirBaseEnvios::class,
        \App\Console\Commands\GetProducts::class,
        \App\Console\Commands\GetOrders::class,
        \App\Console\Commands\GetCustomers::class,
        \App\Console\Commands\GetFathers::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();

        $schedule->command('subirbd')
                 ->everyFiveMinutes();

        $schedule->command('get:products')
                 ->everyFiveMinutes();

        $schedule->command('get:customers')
                 ->everyFiveMinutes();
        
        $schedule->command('get:fathers')
                 ->everyFiveMinutes();
        
        $schedule->command('get:orders')
                 ->twiceDaily(5, 22);
    }
}
