<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;

class EodProcessController extends Controller {

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
            $data['status'] = config('lms.EOD_PROCESS_STATUS.EOD_TRANS_MARKED');
            $data['end_datetime'] = \Carbon\Carbon::now();
            $data['created_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $data['updated_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $this->lmsRepo->saveEodProcess($data);
            
            return $result;
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }        
    }
    
    public function viewEodProcess()
    {
        $sys_curr_date = \Carbon\Carbon::now()->toDateTimeString();
        $current_date = \Helpers::convertDateTimeFormat(\Carbon\Carbon::now(), $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s');
        $whereCond=[];        
        //$whereCond['sys_start_date_gte'] = $sys_curr_date;
        
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : '';
        $status = $eodProcess ? config('lms.EOD_PROCESS_STATUS_LIST')[$eodProcess->status] : '';
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        
        $running_hours = round(( abs(strtotime($sys_curr_date) - strtotime($sys_start_date)) )/3600, 1);
        
        
        $whereCond=[];
        $whereCond['eod_process_id'] = $eod_process_id;
        $statusLog = $this->lmsRepo->getEodProcessLog($whereCond);
        
        
        $statusArr = config('lms.EOD_PASS_FAIL_STATUS');
        
        return view('lms.eod.eod_process')
                ->with('current_date', $current_date)
                ->with('sys_start_date', $sys_start_date)        
                ->with('running_hours', $running_hours)
                ->with('status', $status)
                ->with('statusLog', $statusLog)
                ->with('statusArr', $statusArr);
    }
    
    public function saveEodProcess(Request $reques)
    {
        $flag = $request->get('flag');
        
        try {
            
            $data=[];
            $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
            if ($flag == 1) {
                $data['status'] = config('lms.EOD_PROCESS_STATUS.RUNNING');
                $data['sys_start_date'] = $current_datetime;
                $message = 'System is started successfully';
            }
            
            if ($flag == 2) {
                $data['status'] = config('lms.EOD_PROCESS_STATUS.STOPPED');
                $data['sys_end_date'] = $current_datetime;
                $data['eod_process_start'] = $current_datetime;
                $message = 'Eod Process is started successfully';
            }
            
            $data['created_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $data['updated_by'] = \Auth::user() ? \Auth::user()->user_id : 1;
            $this->lmsRepo->saveEodProcess($data);
            
            Session::flash('message', $message);
            return redirect()->route('eod_process');
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

}
