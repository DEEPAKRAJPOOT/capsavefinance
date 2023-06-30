<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\DisbursalReport as DisbursalReportJob;
use App\Inv\Repositories\Models\Anchor;

class DisbursalReport extends Command
{
    private $needConsolidatedReport;
    private $anchorId;
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:disbursal {need_consolidated_report=true} {anchor_id=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Disbursal Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailTo    = config('lms.DAILY_REPORT_MAIL');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->needConsolidatedReport = filter_var($this->argument('need_consolidated_report'), FILTER_VALIDATE_BOOLEAN);
        $this->anchorId               = $this->argument('anchor_id');
        if(empty($this->emailTo)){
            dd('DAILY_REPORT_MAIL is missing');
        }

        // consolidated anchors report
        if ($this->needConsolidatedReport) {
            $this->addToJobQueue($needConsolidatedReport = true);
        }else{
            if ($this->anchorId == 'all') {
                // all anchors report
                $anchorList = Anchor::active()->whereNotNull('comp_email')->get();
                foreach($anchorList as $anchor) {
                    $this->generateAnchorReport($anchor);
                }
            } else {
                // single anchor report
                $this->generateSingleAnchorReport();
            }
        }
    }

    private function generateSingleAnchorReport()
    {
        $this->anchorId = (int) $this->anchorId;

        if (is_numeric($this->anchorId) && $this->anchorId > 0) {
            $anchor     = Anchor::active()
            ->whereNotNull('comp_email')
            ->where('anchor_id', $this->anchorId)
            ->first();
            if ($anchor) {
                $this->generateAnchorReport($anchor);
            }
        }
    }

    private function addToJobQueue($needConsolidatedReport, $anchor = null, $anchorEmail = null)
    {
        $emailTo = $this->emailTo;
        if(filter_var($anchorEmail, FILTER_VALIDATE_EMAIL)){
            $emailTo = $anchorEmail;
        }
        DisbursalReportJob::dispatch($needConsolidatedReport, $emailTo, $anchor)
                        ->delay(now()->addSeconds(10));
    }

    public function generateAnchorReport($anchor)
    {
        $payLoad = ['anchor_id' => $anchor->anchor_id, 'comp_name' => $anchor->comp_name];
        $email = $anchor->comp_email??$this->emailTo;
        $this->addToJobQueue(false, $payLoad, $email);
    }
}
