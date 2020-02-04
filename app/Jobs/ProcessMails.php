<?php

namespace App\Jobs;

use App\Inv\Repositories\Models\AppAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $assignmentData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AppAssignment $assignmentData)
    {
        $this->assignmentData = $assignmentData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   

        $assignmentData = $this->assignmentData;        
        $data = '';
        if($assignmentData->count()){
            switch ($assignmentData->assign_type) {
                case '0':
                    $data = '0';
                    break;
                case '1':
                    $data = '1';
                    break;
                case '2':
                    $data = '2';
                    break;
                case '3':
                    $data = '3';
                    break;
            }
        }
    }
}
