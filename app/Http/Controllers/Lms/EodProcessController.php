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
            $message = 'System is not started yet.';
            $today = \Carbon\Carbon::now();
            $sys_curr_date = $today->format('Y-m-d H:i:s');
            $sys_start_date_eq = $today->format('Y-m-d');
        
            $whereCond=[];
            //$whereCond['status'] = 0;
            //$whereCond['sys_start_date_eq'] = $sys_start_date_eq;
            $whereCond['sys_start_date_tz_eq'] = $sys_start_date_eq;
            $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
            $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : '';
            if(!$eod_process_id) return $message;
            
            $this->startEodProcess($eod_process_id);
            $message = 'Eod Process is started successfully';
            
            return $message;
                                    
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    public function viewEodProcess()
    {
        $today = \Carbon\Carbon::now();
        $sys_curr_date = $today->format('Y-m-d H:i:s');
        $sys_start_date_eq = $today->format('Y-m-d');

        $current_date = \Helpers::convertDateTimeFormat($sys_curr_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s');
        $whereCond=[];
        //$whereCond['status'] = [config('lms.EOD_PROCESS_STATUS.RUNNING'), config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.FAILED')];
        //$whereCond['sys_start_date_eq'] = $sys_start_date_eq;        
        $whereCond['sys_start_date_tz_eq'] = $sys_start_date_eq;        
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);        
        $eod_process_id = $eodProcess ? $eodProcess->eod_process_id : '';
        $status = $eodProcess ? $eodProcess->status : '';
        $disp_status = $eodProcess ? config('lms.EOD_PROCESS_STATUS_LIST')[$eodProcess->status] : '';
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $total_hours = $eodProcess ? $eodProcess->total_hours : '';
        $sys_end_date = $eodProcess ? $eodProcess->sys_end_date : '';
        
        if ($eod_process_id) {
            $running_hours = round(abs(strtotime($sys_curr_date) - strtotime($sys_start_date))/3600, 1);                        
        } else {
            $running_hours = '';
        }
        
        $whereCond=[];
        $whereCond['eod_process_id'] = $eod_process_id;
        $statusLog = $this->lmsRepo->getEodProcessLog($whereCond);
        
        
        $statusArr = config('lms.EOD_PASS_FAIL_STATUS');
        
        $enable_sys_start = $eod_process_id && $status != 1 ? 0 : 1;
        
        $enable_process_start = isset($eodProcess->status) && in_array($eodProcess->status,[config('lms.EOD_PROCESS_STATUS.STOPPED'), config('lms.EOD_PROCESS_STATUS.COMPLETED'), config('lms.EOD_PROCESS_STATUS.FAILED')]) ? 0 : 1;
        
        return view('lms.eod.eod_process')
                ->with('current_date', $current_date)
                ->with('sys_start_date', $sys_start_date)
                ->with('sys_end_date', $sys_end_date)
                ->with('running_hours', $running_hours)
                ->with('status', $disp_status)
                ->with('eodData', $eodProcess)
                ->with('statusLog', $statusLog)
                ->with('statusArr', $statusArr)
                ->with('eod_process_id', $eod_process_id)
                ->with('total_hours', $total_hours)
                ->with('enable_sys_start', $enable_sys_start)
                ->with('enable_process_start', $enable_process_start);
                
                
    }
    
    public function saveEodProcess(Request $request)
    {
        $flag = $request->get('flag');
        $eod_process_id = $request->get('eod_process_id');
        
        try {
                        
            $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
            $current_user_id = \Auth::user() ? \Auth::user()->user_id : 1;
            
            if ($flag == 1) {                                              
                $this->lmsRepo->updateEodProcess(['is_active' => 0], ['is_active' => 1]);                
                $data=[];
                $data['status'] = config('lms.EOD_PROCESS_STATUS.RUNNING');
                $data['sys_start_date'] = $current_datetime;
                $data['is_active'] = 1;                
                $data['created_by'] = $current_user_id;
                $data['updated_by'] = $current_user_id;
                $eodProcess = $this->lmsRepo->saveEodProcess($data);
                if ($eodProcess) {
                    $eod_process_id = $eodProcess->eod_process_id;
                    
                    $logData=[];
                    $logData['eod_process_id'] = $eod_process_id;
                    $logData['created_by'] = $current_user_id;
                    $logData['updated_by'] = $current_user_id;
                    $this->lmsRepo->saveEodProcessLog($logData);                    
                }
                $message = 'System is started successfully';
            }
            
            if ($flag == 2) {
                $this->startEodProcess($eod_process_id);
                $message = 'Eod Process is started successfully';
            }
                        
            Session::flash('message', $message);
            return redirect()->route('eod_process');
            
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    protected function startEodProcess($eod_process_id)
    {
        $current_datetime = \Carbon\Carbon::now()->toDateTimeString();
        $current_user_id = \Auth::user() ? \Auth::user()->user_id : 1;

        $whereCond=[];        
        $whereCond['eod_process_id'] = $eod_process_id;
        $eodProcess = $this->lmsRepo->getEodProcess($whereCond);
        $sys_start_date = $eodProcess ? $eodProcess->sys_start_date : '';
        $running_hours = round(( abs(strtotime($current_datetime) - strtotime($sys_start_date)) )/3600, 1);

        $data=[];
        $data['status'] = config('lms.EOD_PROCESS_STATUS.STOPPED');
        $data['sys_end_date'] = $current_datetime;
        $data['eod_process_start'] = $current_datetime;
        $data['total_hours'] = $running_hours;
        $data['is_active'] = 1;
        //$data['created_by'] = $current_user_id;
        $data['updated_by'] = $current_user_id;
        $this->lmsRepo->saveEodProcess($data, $eod_process_id);        
    }

}
