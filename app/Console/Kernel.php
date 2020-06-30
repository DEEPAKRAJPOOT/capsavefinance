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
       // \App\Console\Commands\PaypalRefund::class,
       // \App\Console\Commands\ScoutPayoutDetail::class,
      //  \App\Console\Commands\PaypalScoutRefund::class,
        //:ScoutRefund
        \App\Console\Commands\InterestAccrual::class,
        \App\Console\Commands\RenewApplications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //$schedule->command('PaypalRefund:refund')->twiceDaily(1, 13);
        //$schedule->command('ScoutPayoutDetail:BatchDetail')->twiceDaily(2, 14);
        //$schedule->command('PaypalScoutRefund:ScoutRefund')->twiceDaily(3, 12);
        if(config('lms.LMS_STATUS') && !\Helpers::checkEodProcess()){
            $schedule->command('lms:interestaccrual')->dailyAt(00.01);
        }

        if(config('lms.LMS_STATUS') && !\Helpers::checkEodProcess() && \Helpers::getInterestAccrualCronStatus() && !\Helpers::getEodProcessCronStatus()){
            $schedule->command('lms:eodprocess')->dailyAt(23.50)->emailOutputOnFailure(config('lms.EOD_FAILURE_MAIL'));
        }
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
