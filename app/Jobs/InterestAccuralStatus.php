<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\ManualApportionmentHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;
use Throwable;

class InterestAccuralStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invDisbIds = [];
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invDisbIds)
    {
        $this->invDisbIds = $invDisbIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set("memory_limit", "-1");
        $controller = \App::make('App\Http\Controllers\Lms\ApportionmentController');
        $Obj = new ManualApportionmentHelper($controller->lmsRepo);
       
        foreach ($this->invDisbIds as $invId) {
            $Obj->intAccrual($invId, NULL);
            $Obj->transactionPostingAdjustment($invId, NULL);
            $job_id = $this->job->getJobId();
        }
        
    }
    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
