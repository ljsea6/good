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
        \App\Console\Commands\MercadoPago::class,
        \App\Console\Commands\Metafields::class,
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
                 ->twiceDaily(1, 20);

        $schedule->command('get:customers')
                 ->twiceDaily(2, 21);
        
        $schedule->command('get:fathers')
                 ->twiceDaily(3, 22);
        
        $schedule->command('get:orders')
                 ->twiceDaily(14, 23);

        $schedule->command('get:mercadopago')
            ->dailyAt('22:30');

        $schedule->command('get:metafields')
            ->dailyAt('23:30');
    }
}
