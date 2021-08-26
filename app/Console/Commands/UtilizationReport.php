<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\UtilizationReport as UtilizationReportJob;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Anchor;

class UtilizationReport extends Command
{
    private $needConsolidatedReport;
    private $anchorId;
    private $emailTo;
    private $sendMail;
    private $reportsRepo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utilization:report {need_consolidated_report=true} {anchor_id=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Utilization Report Based On Parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailTo    = config('lms.DAILY_REPORT_MAIL');
        $this->sendMail   = false;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ReportInterface $reportsRepo)
    {
        $this->reportsRepo            = $reportsRepo;
        $this->needConsolidatedReport = filter_var($this->argument('need_consolidated_report'), FILTER_VALIDATE_BOOLEAN);
        $this->anchorId               = $this->argument('anchor_id');

        if(empty($this->emailTo)){
            dd('DAILY_REPORT_MAIL is missing');
        }

        $data = $this->reportsRepo->getUtilizationReport([], $this->sendMail);

        if($this->sendMail){
            // consolidated anchors report
            if ($this->needConsolidatedReport) {
                UtilizationReportJob::dispatch($data, $this->emailTo)
                        ->onConnection('database')
                        ->delay(now()->addSeconds(10));
            }

            $query = Anchor::active()
                           ->whereNotNull('comp_email');

            // all anchors report
            if ($this->anchorId == 'all') {
                $anchorList = $query->get();
                foreach($anchorList as $anchor){
                    $this->generateAnchorReport($anchor);
                }
            } else {
                // single anchor report
                $this->anchorId = (int) $this->anchorId;

                if (is_numeric($this->anchorId) && $this->anchorId > 0) {
                    $anchor     = $query->where('anchor_id', $this->anchorId)->first();
                    if ($anchor) {
                        $this->generateAnchorReport($anchor);
                    }
                }
            }
        }
    }

    private function generateAnchorReport($anchor)
    {
        $this->sendMail = false;
        $data           = $this->reportsRepo->getUtilizationReport(['anchor_id' => $anchor->anchor_id], $this->sendMail);

        if ($this->sendMail) {
            UtilizationReportJob::dispatch($data, $this->emailTo, $anchor)
                ->onConnection('database')
                ->delay(now()->addSeconds(10));
        }
    }
}
