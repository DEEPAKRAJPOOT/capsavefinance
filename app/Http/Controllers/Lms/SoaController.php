<?php
namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use PDF as DPDF;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;
use PHPExcel_Cell_DataType;
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

            DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
            $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                    ->loadView('lms.soa.downloadSoaReport', ['userInfo' => $result['userInfo'], 'soaRecord' => $soaRecord, 'fromdate' => $request->get('from_date'), 'todate' => $request->get('to_date')],[],'UTF-8');
            return $pdf->download('SoaReport.pdf');          
          } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    public function soaExcelDownload(Request $request){
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
//        dd($exceldata);
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
                ->setCellValue('D2', 'CAPSAVE FINANCE PRIVATE LIMITED')
                ->setCellValue('E3', 'Statement Of Account')
                ->setCellValue('A5', 'Business Name')
                ->setCellValue('C5', $data['userInfo']->biz->biz_entity_name)
                ->setCellValue('G5', 'Full Name')
                ->setCellValue('I5', $data['userInfo']->f_name." ".$data['userInfo']->m_name." ".$data['userInfo']->l_name)
                ->setCellValue('A6', 'Email')
                ->setCellValue('C6', $data['userInfo']->email)
                ->setCellValue('G6', 'Mobile')
                ->setCellValueExplicit('I6', $data['userInfo']->mobile_no, PHPExcel_Cell_DataType::TYPE_STRING);
        
        $rows = 8;
        
        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A7', 'From Date')
                ->setCellValue('C7', $request->get('from_date'))
                ->setCellValue('G7', 'To Date')
                ->setCellValue('I7', $request->get('to_date'));
            
            $rows++;
        }
                
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$rows, 'Customer ID')
                ->setCellValue('B'.$rows, 'Tran Date')
                ->setCellValue('C'.$rows, 'Value Date')
                ->setCellValue('D'.$rows, 'Tran Type')
                ->setCellValue('E'.$rows, 'Batch No')
                ->setCellValue('F'.$rows, 'Invoice No')
                ->setCellValue('G'.$rows, 'Narration')
                ->setCellValue('H'.$rows, 'Currency')
                ->setCellValue('I'.$rows, 'Debit')
                ->setCellValue('J'.$rows, 'Credit')
                ->setCellValue('K'.$rows, 'Balance');
        
        for($i=65; $i<=75; $i++){
            $sheet->getActiveSheet()->getStyle(chr($i).$rows)->getFill()->applyFromArray(array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
//                    'rgb' => "F28A8C",
                    'rgb' => "FF0000"
                )
            ));
        }
               
        $rows++;

        foreach($exceldata as $data){
            foreach ($data as $rowData){
                $sheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $rows, $rowData['customer_id'] ?: '')
                    ->setCellValue('B' . $rows, $rowData['trans_date'] ?: '')
                    ->setCellValue('C' . $rows, $rowData['value_date'] ?: '')
                    ->setCellValue('D' . $rows, $rowData['trans_type'] ?: '')
                    ->setCellValue('E' . $rows, $rowData['batch_no'] ?: '')
                    ->setCellValue('F' . $rows, $rowData['invoice_no'] ?: '')
                    ->setCellValue('G' . $rows, $rowData['narration'] ?: '')
                    ->setCellValue('H' . $rows, $rowData['currency'] ?: '')
                    ->setCellValue('I' . $rows, $rowData['debit'] ?: '')
                    ->setCellValue('J' . $rows, $rowData['credit'] ?: '')
                    ->setCellValue('K' . $rows, $rowData['balance'] ?: '');
                
                $color = 'FFFFFF';
                if(strtolower($rowData['trans_type']) === 'repayment'){
                    $color = "F3C714";
                }elseif($rowData['payment_id']){
                    $color = "FED8B1";
                }
                
                for($i=65; $i<=75; $i++){
                    $sheet->getActiveSheet()->getStyle(chr($i).$rows)->getFill()->applyFromArray(array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => array(
                            'rgb' => $color
                        )
                    ));
                }
                
                $rows++;
            }
        }
        
//        dd($sheet);
//        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="download_Excel.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        $objWriter->save('php://output');
        
    }

}