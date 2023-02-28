<?php

namespace App\Console;

require_once base_path('common/functions.php');
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;

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
        \App\Console\Commands\TallyPosting::class,
        \App\Console\Commands\InterestAccrual::class,
        \App\Console\Commands\RenewApplications::class,
        \App\Console\Commands\LenovoNewUser::class,
        \App\Console\Commands\MaturityInvoiceDueAlert::class,
        \App\Console\Commands\MaturityInvoiceOverDueAlert::class,
        \App\Console\Commands\EtlReportSync::class,
        \App\Console\Commands\OverdueReport::class,
        \App\Console\Commands\OverdueReportManual::class,
        \App\Console\Commands\ApproverMailForPendingCases::class,
        \App\Console\Commands\OutstandingReport::class,
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
         
        if(config('lms.LMS_STATUS') && !\Helpers::checkEodProcess() && \Helpers::getInterestAccrualCronStatus() && !\Helpers::getEodProcessCronStatus()){
            $schedule->command('lms:eodprocess')->timezone(config('common.timezone'))->dailyAt('23:00')->emailOutputOnFailure(config('lms.EOD_FAILURE_MAIL'));
        }

        if(config('lms.LMS_STATUS')){
            $schedule->command('lms:interestaccrual')->timezone(config('common.timezone'))->dailyAt('23:05')
            ->onSuccess(function() use($schedule){
                $this->call('note:generateDebitNote');
                $this->call('note:generateCreditNote');
                $this->call('note:generateCreditNoteReversal');
            });
            $schedule->command('lms:interestaccrual')->dailyAt('00:01');
            $schedule->command('finance:tallyposting')->timezone(config('common.timezone'))->dailyAt('00:01') 
            ->onSuccess(function() use($schedule){
                $this->call('eod:check-data');
            });

            $schedule->command('disb_pays:checks')->timezone(config('common.timezone'))->dailyAt('00:11');
        }
        
        if(config('lms.LMS_STATUS') && !empty('lms.DAILY_REPORT_MAIL')){
            // To Generate Account Disbursal Report
            $schedule->command('report:account_disbursal')->timezone(config('common.timezone'))->dailyAt('23:40');
            // To Generate Disbursal Report
            $schedule->command('report:disbursal')->timezone(config('common.timezone'))->dailyAt('23:42');
            // To Generate Maturity Report
            $schedule->command('report:maturity')->timezone(config('common.timezone'))->dailyAt('23:44');
            // To Generate Overdue Report
            $schedule->command('report:overdue')->timezone(config('common.timezone'))->dailyAt('23:46');
            // To Generate Overdue Report Manual
            $schedule->command('report:overdueManual')->timezone(config('common.timezone'))->saturdays()->at('23:30');
            // To Generate Utilization Report
            $schedule->command('report:utilization')->timezone(config('common.timezone'))->dailyAt('23:48');
            // To Generate Margin Report
            $schedule->command('report:margin')->timezone(config('common.timezone'))->dailyAt('23:50');
            // To Generate Receipt Report
            $schedule->command('report:receipt')->timezone(config('common.timezone'))->dailyAt('23:52');
            // To Generate Outstanding Report Manual
            $schedule->command('report:reconReport')->timezone(config('common.timezone'))->dailyAt('23:54');
            $schedule->command('report:outstandingManual')->timezone(config('common.timezone'))->dailyAt('23:55')
            ->onSuccess(function() use($schedule){
                $this->call('etl:report_outstanding');
                if(Carbon::now()->timezone(config('common.timezone'))->format('d') == '01'){
                    $this->call('etl:report_outstanding_monthly');
                }    
            });
        }
        $schedule->command('command:lenovoNewUser')->timezone(config('common.timezone'))->dailyAt('23:00');
        $schedule->command('lms:maturityinvoicedueAlert')->timezone(config('common.timezone'))->dailyAt('21:30');
        $schedule->command('lms:maturityinvoiceoverdueAlert')->timezone(config('common.timezone'))->dailyAt('22:00');
        $schedule->command('lms:cibilReport')->timezone(config('common.timezone'))->monthlyOn(1, '22:00');
        $schedule->command('clear:day_end_active_csv_apportionment')->timezone(config('common.timezone'))->dailyAt('23:00');
        $schedule->command('etl:ReportSync')->timezone(config('common.timezone'))->dailyAt('01:10');
        $schedule->command('etl:report_overdue')->timezone(config('common.timezone'))->dailyAt('01:15');
        $schedule->command('etl:report_maturity')->timezone(config('common.timezone'))->dailyAt('01:20');
        $schedule->command('etl:report_utilization')->timezone(config('common.timezone'))->dailyAt('01:25');
        $schedule->command('etl:report_disbursal')->timezone(config('common.timezone'))->dailyAt('01:30');
        $schedule->command('etl:report_account_disbursal')->timezone(config('common.timezone'))->dailyAt('01:35');
        $schedule->command('lms:disbursalBatchRequest')->timezone(config('common.timezone'))->between('10:00','23:59')->hourlyAt('1');
        $schedule->command('lms:disbursalBatchRequest')->timezone(config('common.timezone'))->dailyAt('23:50');
        $schedule->command('alert:approvalMailForPendingCases')->timezone(config('common.timezone'))->tuesdays()->dailyAt('20:45');
        $schedule->command('alert:app_security_document_renewal')->timezone(config('common.timezone'))->dailyAt('23:00');
    
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
