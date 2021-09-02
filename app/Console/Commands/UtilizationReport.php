<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\UtilizationReport as UtilizationReportJob;
use App\Inv\Repositories\Models\Anchor;

class UtilizationReport extends Command
{
    private $needConsolidatedReport;
    private $anchorId;
    private $emailTo;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:utilization {need_consolidated_report=true} {anchor_id=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Utilization Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->emailTo  = config('lms.DAILY_REPORT_MAIL');
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
        }

        $query = Anchor::active()
                        ->whereNotNull('comp_email');

        if ($this->anchorId == 'all') {
            // all anchors report
            $anchorList = $query->get();
            foreach($anchorList as $anchor) {
                $this->generateAnchorReport($anchor);
            }
        } else {
            // single anchor report
            $this->generateSingleAnchorReport($query);
        }
    }

    private function generateSingleAnchorReport($query)
    {
        $this->anchorId = (int) $this->anchorId;

        if (is_numeric($this->anchorId) && $this->anchorId > 0) {
            $anchor     = $query->where('anchor_id', $this->anchorId)->first();
            if ($anchor) {
                $this->generateAnchorReport($anchor);
            }
        }
    }

    private function addToJobQueue($needConsolidatedReport, $anchor = null)
    {
        UtilizationReportJob::dispatch($needConsolidatedReport, $this->emailTo, $anchor)
                            ->delay(now()->addSeconds(10));
    }

    public function generateAnchorReport($anchor)
    {
        $payLoad = ['anchor_id' => $anchor->anchor_id, 'comp_name' => $anchor->comp_name];
        $this->addToJobQueue($needConsolidatedReport = false, $payLoad);
    }
}
