<?php
namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use PDF as DPDF;
use PHPExcel;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class SoaController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $masterRepo;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
        $this->masterRepo = $master;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function list(Request $request)
	{	
		$userData = [];
		if($request->has('user_id')){
            $result = $this->getUserLimitDetais($request->user_id);
            $user = $this->userRepo->lmsGetCustomer($request->user_id);
            $maxInterestDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.INTEREST'));
            $maxPrincipalDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
            if($user && $user->app_id){
				$userData['user_id'] = $user->user_id;
				$userData['customer_id'] = $user->customer_id;
				$appDetail = $this->appRepo->getAppDataByAppId($user->app_id);
				if($appDetail){
					$userData['app_id'] = $appDetail->app_id;
					$userData['biz_id'] = $appDetail->biz_id;
				}
			}
		}
		
        return view('lms.soa.list')
        ->with('user',$userData)
        ->with('maxDPD',1)
        ->with('maxPrincipalDPD',$maxPrincipalDPD)
        ->with('maxInterestDPD',$maxInterestDPD)
        ->with(['userInfo' =>  $result['userInfo'],
                'application' => $result['application'],
                'anchors' =>  $result['anchors']]); 
			              
	}
        
         /* use function for the manage sention tabs */ 
    
    public  function  getUserLimitDetais($user_id) 
   {
            try {
                $totalLimit = 0;
                $totalCunsumeLimit = 0;
                $consumeLimit = 0;
                $transactions = 0;
                $userInfo = $this->userRepo->getCustomerDetail($user_id);
                $application = $this->appRepo->getCustomerApplications($user_id);
                $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);

                foreach ($application as $key => $app) {
                    if (isset($app->prgmLimits)) {
                        foreach ($app->prgmLimits as $value) {
                            $totalLimit += $value->limit_amt;
                        }
                    }
                    if (isset($app->acceptedOffers)) {
                        foreach ($app->acceptedOffers as $value) {
                            $totalCunsumeLimit += $value->prgm_limit_amt;
                        }
                    }
                }
                $userInfo->total_limit = number_format($totalLimit);
                $userInfo->consume_limit = number_format($totalCunsumeLimit);
                $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
                
                $data['userInfo'] = $userInfo;
                $data['application'] = $application;
                $data['anchors'] = $anchors;
                return $data;
            } catch (Exception $ex) {
                dd($ex);
            }
    }
    
    public function getDebit($trans){
        if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')])){
            return '';
        }
        elseif($trans->entry_type=='0'){
            return number_format($trans->amount,2);
        }else{
            return '0.00';
        }
    }
    
    public function getCredit($trans){
        if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')])){
            return '';
        }
        elseif($trans->entry_type=='1'){
            return '('.number_format($trans->amount,2).')';
        }else{
            return '(0.00)';
        }
    }
    
    public function getBalance($trans){
        $data = '';
        if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')])){
            $data = '';
        }
        elseif($trans->balance<0){
            $data = number_format(abs($trans->balance), 2);
        }else{
            $data = number_format(abs($trans->balance), 2);
        }
        return $data;
    }
    
    public function prepareDataForRendering($expecteddata){
        
        $preparedData = [];
        
        foreach($expecteddata as $key => $expData){
            foreach ($expData as $k => $data) {
                # code...
                $preparedData[$key][$k]['payment_id'] = $data->payment_id;
                $preparedData[$key][$k]['parent_trans_id'] = $data->parent_trans_id;      
                $preparedData[$key][$k]['customer_id'] = $data->lmsUser->customer_id;
                $preparedData[$key][$k]['trans_date'] = date('d-m-Y',strtotime($data->trans_date));
                $preparedData[$key][$k]['value_date'] = date('d-m-Y',strtotime($data->parenttransdate));
                $preparedData[$key][$k]['trans_type'] = trim($data->transname);
                $preparedData[$key][$k]['batch_no'] = $data->batchNo;
                $preparedData[$key][$k]['invoice_no'] = $data->invoiceno;
                $preparedData[$key][$k]['narration'] = $data->narration;
                $preparedData[$key][$k]['currency'] = trim($data->payment_id && in_array($data->trans_type,[config('lms.TRANS_TYPE.REPAYMENT')]) ? '' : 'INR');
                $preparedData[$key][$k]['debit'] = $this->getDebit($data);
                $preparedData[$key][$k]['credit'] = $this->getCredit($data);
                $preparedData[$key][$k]['balance'] = $this->getBalance($data);
            }
        }
        
        return $preparedData;
        
    }

    public function soaPdfDownload(Request $request){
        try{
            $soaRecord = [];
            if($request->has('user_id')){
                $result = $this->getUserLimitDetais($request->user_id);
                $transactionList = $this->lmsRepo->getSoaList();
                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $transactionList->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('trans_date', [$from_date, $to_date]);
                    });
                }

                if($request->get('customer_id')!= ''){
                    $transactionList->where(function ($query) use ($request) {
                        $customer_id = trim($request->get('customer_id'));
                        $query->where('customer_id', '=', "$customer_id");
                    });
                }

                $soaRecord = $this->prepareDataForRendering($transactionList->get()->chunk(25));
                            
            }
            // // dd($soaRecord);
            // return view('lms.soa.downloadSoaReport')
            // ->with('userInfo',$result['userInfo'])
            // ->with('soaRecord',$soaRecord); 

            DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
            $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                    ->loadView('lms.soa.downloadSoaReport', ['userInfo' => $result['userInfo'], 'soaRecord' => $soaRecord, 'fromdate' => $request->get('from_date'), 'todate' => $request->get('to_date')],[],'UTF-8');
            return $pdf->download('SoaReport.pdf');          
          } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    public function soaExcelDownload(Request $request){
//        return response('Under Development!', 200)
//        ->header('Content-Type', 'text/plain');
//        dd($request->all());
        if($request->has('user_id')){
            $data = $this->getUserLimitDetais($request->user_id);
            $transactionList = $this->lmsRepo->getSoaList();
            if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $transactionList->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                    $query->WhereBetween('trans_date', [$from_date, $to_date]);
                });
            }

            if($request->get('customer_id')!= ''){
                $transactionList->where(function ($query) use ($request) {
                    $customer_id = trim($request->get('customer_id'));
                    $query->where('customer_id', '=', "$customer_id");
                });
            }
                
        }
//        dd($transactionList->get());
        $exceldata = $this->prepareDataForRendering($transactionList->get()->chunk(25));
        dd($exceldata);
        $sheet =  new PHPExcel();
        $sheet->getProperties()
                ->setCreator("Capsave")
                ->setLastModifiedBy("Capsave")
                ->setTitle("Bank Disburse Excel")
                ->setSubject("Bank Disburse Excel")
                ->setDescription("Bank Disburse Excel")
                ->setKeywords("Bank Disburse Excel")
                ->setCategory("Bank Disburse Excel");
    
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Customer ID')
                ->setCellValue('B1', 'Tran Date')
                ->setCellValue('C1', 'Value Date')
                ->setCellValue('D1', 'Tran Type')
                ->setCellValue('E1', 'Batch No')
                ->setCellValue('F1', 'Invoice No')
                ->setCellValue('G1', 'Narration')
                ->setCellValue('H1', 'Currency')
                ->setCellValue('I1', 'Debit')
                ->setCellValue('J1', 'Credit')
                ->setCellValue('K1', 'Balance');
                
        $rows = 2;

        foreach($exceldata as $rowData){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $rowData['customer_id'] ?? 'XYZ')
                ->setCellValue('B' . $rows, $rowData['trans_date'] ?? '')
                ->setCellValue('C' . $rows, $rowData['value_date'] ?? '')
                ->setCellValue('D' . $rows, $rowData['trans_type'] ?? '')
                ->setCellValue('E' . $rows, $rowData['batch_no'] ?? '')
                ->setCellValue('F' . $rows, $rowData['invoice_no'] ?? '')
                ->setCellValue('G' . $rows, $rowData['narration'] ?? '')
                ->setCellValue('H' . $rows, $rowData['currency'] ?? '')
                ->setCellValue('I' . $rows, $rowData['debit'] ?? '')
                ->setCellValue('J' . $rows, $rowData['credit'] ?? '')
                ->setCellValue('K' . $rows, $rowData['balance'] ?? '');
                
            $rows++;
        }
        
//        dd($sheet);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=download_Excel.xlsx');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        
        if (!Storage::exists('/public/docs/bank_excel')) {
            Storage::makeDirectory('/public/docs/bank_excel');
        }
        $storage_path = storage_path('app/public/docs/bank_excel');
        $filePath = $storage_path.'/'.$filename.'.xlsx';

        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        $objWriter->save($filePath);

        return [ 'status' => 1,
                'file_path' => $filePath
                ];
        
        return view('lms.soa.downloadSoaExcel');
    }

}