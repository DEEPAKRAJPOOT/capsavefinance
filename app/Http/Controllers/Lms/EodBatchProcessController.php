<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;

class EodBatchProcessController extends Controller {

    use LmsTrait;

    protected $lmsRepo;

    public function __construct(InvLmsRepoInterface $lmsRepo) 
    {
        $this->lmsRepo = $lmsRepo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {
        try {
            $result = 'Process Done!';
            
            $data=[];
            $data['status'] = config('lms.EOD_BATCH_PROCESS_STATUS.EOD_TRANS_MARKED');
            $data['end_datetime'] = \Carbon\Carbon::now();
            $data['created_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $data['updated_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $this->lmsRepo->saveEodBatchProcess($data);
            
            return $result;
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }        
    }

}
