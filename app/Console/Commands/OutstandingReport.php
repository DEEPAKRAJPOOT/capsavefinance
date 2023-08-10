<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use DateTime;
use Illuminate\Console\Command;
use App\Jobs\OutstandingReportManual as OutstandingReportManualJob;
use InvalidArgumentException;

class OutstandingReport extends Command
{
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'report:outstandingManual
    {date=now : Date of Outstanding Report(YYYY/MM/DD)}
    {user=all : The ID of the user}
    {logId=NULL : The ID of the OverdueReportLog}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Back Dated Outstanding Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailTo = config('lms.DAILY_REPORT_MAIL');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            ini_set("memory_limit", "-1");
            ini_set('max_execution_time', 10000);
            if (empty($this->emailTo)) {
                dd('DAILY_REPORT_MAIL is missing');
            }

            $userId = $this->argument('user');
            $toDate = $this->argument('date');
            $logId = $this->argument('logId');

            if(trim(strtolower($toDate)) == 'now'){
                $toDate = (string) Helper::getSysStartDate();
            }
            else{
                $dateTime = DateTime::createFromFormat('Y/m/d', $toDate);
                if($dateTime && $dateTime->format('Y/m/d') === $toDate){
                    $toDate = $dateTime->format('Y-m-d');
                }else{
                    throw new InvalidArgumentException('Invalid date of outstanding report(YYYY/MM/DD):'. $toDate);
                }
            }

            if(trim(strtolower($userId)) == 'all'){
                $userId = NULL;
            }

            if(trim(strtolower($logId)) == 'null'){
                if(!is_numeric($userId) && !is_null($userId)) {
                    throw new InvalidArgumentException('User id must be numeric value');
                }

                $odReportLog = OutstandingReportLog::create([ 'user_id' => $userId, 'to_date' => $toDate]);
                $logId = $odReportLog->id;
            }

            OutstandingReportManualJob::dispatchSync($this->emailTo, $userId, $toDate, $logId);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
