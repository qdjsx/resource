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
        \App\Console\Commands\CouponExcel::class,
        \App\Console\Commands\CatsRedis::class,
        \App\Console\Commands\JingdongcatsRedis::class,
        \App\Console\Commands\CouponOverdue::class,
        \App\Console\Commands\CouponEncrypt::class,
        \App\Console\Commands\ImageDeal::class,
//        \App\Console\Commands\AdminChangeDataScript::class,
        \App\Console\Commands\CouponInfo::class,
        \App\Console\Commands\AdminChangeDateList::class,
        \App\Console\Commands\InitializationData::class,
        \App\Console\Commands\UserExcel::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        ///$schedule->command('deal:coupon_excel')->cron('*/10 * * * *')->withoutOverlapping();
        //$schedule->command('deal:flux_excel')->cron('*/10 * * * *')->runInBackground()->withoutOverlapping();
//        $schedule->command('deal:cats_redis')->cron('* * 1 * *');
//        $schedule->command('deal:jingdongcats_redis')->cron('* * 1 * *');
    //    $schedule->command('deal:coupon_overdue')->cron('1 0 * * *')->withoutOverlapping();
        //$schedule->command('deal:coupon_encrypt_id')->cron('1 0 * * *')->withoutOverlapping();
//        $schedule->command('deal:image_zip')->cron('* * * * *')->withoutOverlapping();
//        $schedule->command('deal:admin_change_data')->cron('* * 1 * *')->withoutOverlapping();
        $schedule->command('deal:admin_change_data_list')->everyMinute()->withoutOverlapping();
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
