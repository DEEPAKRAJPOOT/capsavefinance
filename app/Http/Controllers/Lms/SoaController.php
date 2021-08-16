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
use PHPExcel_Style_Alignment;
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
use App\Inv\Repositories\Models\Lms\TransType;
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
	public function soa_customer_view(Request $request)
	{	
        $userData = [];
        $transTypes = TransType::getTransTypeFilterList();
		if($request->has('user_id')){
            $result = $this->getUserLimitDetais($request->user_id);
            if(isset($result['userInfo'])){
                $result['userInfo']->outstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
                $result['userInfo']->marginOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.MARGIN')] ])->sum('outstanding'),2);
                $result['userInfo']->nonfactoredOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
                $result['userInfo']->unsettledPaymentAmt = number_format($this->lmsRepo->getUnsettledPayments($request->user_id)->sum('amount'),2);
            }
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
        ->with('transTypes', $transTypes)
        ->with('maxDPD',1)
        ->with('maxPrincipalDPD',$maxPrincipalDPD)
        ->with('maxInterestDPD',$maxInterestDPD)
        ->with(['userInfo' =>  $result['userInfo'],
                'application' => $result['application'],
                'anchors' =>  $result['anchors']]); 
                 
    }
        
    public function soa_consolidated_view(Request $request){
        $userData = [
            'user_id'=>null,
            'customer_id'=>null,
            'app_id'=>null,
            'biz_id'=>null
        ];
        $transTypes = TransType::getTransTypeFilterList();
        $maxPrincipalDPD = null;
        $maxInterestDPD = null;
        $result = null;
		if($request->has('user_id') && $request->user_id){
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
        return view('lms.soa.consolidated_soa')
        ->with('user',$userData)
        ->with('transTypes', $transTypes)
        ->with('maxDPD',1)
        ->with('maxPrincipalDPD',$maxPrincipalDPD)
        ->with('maxInterestDPD',$maxInterestDPD)
        ->with(['userInfo' =>  $result['userInfo']??null, 'application' => $result['application']??null, 'anchors' =>  $result['anchors']??null]); 
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
            if($userInfo){
                $userInfo->total_limit = number_format($totalLimit);
                $userInfo->consume_limit = number_format($totalCunsumeLimit);
                $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
            }
            
            $data['userInfo'] = $userInfo;
            $data['application'] = $application;
            $data['anchors'] = $anchors;
            return $data;
        } catch (Exception $ex) {
            dd($ex);
        }
    }
    
    public function getDebit($trans){
        if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
            return '';
        }
        elseif($trans->entry_type=='0'){
            return number_format($trans->amount,2);
        }else{
            return '0.00';
        }
    }
    
    public function getCredit($trans){
        if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
            return '';
        }
        elseif($trans->entry_type=='1'){
            return '('.number_format($trans->amount,2).')';
        }else{
            return '(0.00)';
        }
    }
    
    public function getBalance($trans, $balance = 0.00){

        if($trans->entry_type=='1'){
            $balance = $balance+$trans->amount;
        }
        elseif($trans->entry_type=='0'){
            $balance = $balance-$trans->amount;
        }
        return $balance;
    }
    
    public function prepareDataForRendering($expecteddata){
        $preparedData = [];
        foreach($expecteddata as $key => $expData){
            foreach ($expData as $k => $data) {
                $balance = $this->getBalance($data, $balance??0);
                $preparedData[$key][$k]['payment_id'] = $data->transaction->payment_id;
                $preparedData[$key][$k]['parent_trans_id'] = $data->transaction->parent_trans_id;
                $preparedData[$key][$k]['customer_id'] = $data->lmsUser->customer_id;
                $preparedData[$key][$k]['trans_date'] = date('d-m-Y',strtotime($data->trans_date));
                $preparedData[$key][$k]['value_date'] = date('d-m-Y',strtotime($data->value_date));
                $preparedData[$key][$k]['trans_type'] = trim($data->transaction->transname);
                $preparedData[$key][$k]['batch_no'] = $data->batch_no;
                $preparedData[$key][$k]['invoice_no'] = $data->invoice_no;
                $preparedData[$key][$k]['narration'] = $data->narration;
                $preparedData[$key][$k]['currency'] = trim($data->transaction->payment_id && in_array($data->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')]) ? '' : 'INR');
                $preparedData[$key][$k]['debit'] = $data->debit_amount;
                $preparedData[$key][$k]['credit'] = $data->credit_amount;
                $preparedData[$key][$k]['balance'] = $data->balance_amount;
                $preparedData[$key][$k]['soabackgroundcolor'] = $data->soabackgroundcolor;
            }
        }
        return $preparedData;
    }
    

    public function soaPdfDownload(Request $request){
        try{
            $soaRecord = [];
            $userInfo = null; 
            if($request->has('user_id')){
                if($request->user_id){
                    $result = $this->getUserLimitDetais($request->user_id);
                    $userInfo = $result['userInfo'];
                    $customerId = '';
                }

                if($request->has('soaType')){
                    if($request->soaType == 'consolidatedSoa'){
                        $transactionList = $this->lmsRepo->getConsolidatedSoaList();
                    }
                    elseif($request->soaType == 'customerSoa'){
                        $transactionList = $this->lmsRepo->getSoaList();
                    }
                }
                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $transactionList->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('trans_date', [$from_date, $to_date]);
                    });
                }
                if($request->has('trans_entry_type')){
                    $trans_entry_type = explode('_',$request->trans_entry_type);
                    $trans_type = $trans_entry_type[0];
                    $entry_type = $trans_entry_type[1];
                    if($trans_type){
                        $transactionList->where('trans_type',$trans_type);
                    }
                    if($entry_type){
                        // $transactionList->where('entry_type',$entry_type);
                    }
                }
                $transactionList->whereHas('lmsUser',function ($query) use ($request) {
                    $customer_id = trim($request->get('customer_id'));
                    $query->where('customer_id', '=', "$customer_id");
                });

                $soaRecord = $this->prepareDataForRendering($transactionList->get()->filter(function($item){
                    return $item->transaction->is_transaction;
                })->chunk(25));
            } 
            ini_set('memory_limit', -1);
            DPDF::setOptions(['isHtml5ParserEnabled'=> true]);
            $pdf = DPDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4'])
                    ->loadView('lms.soa.downloadSoaReport', ['userInfo' => $userInfo, 'soaRecord' => $soaRecord, 'fromdate' => $request->get('from_date'), 'todate' => $request->get('to_date'),'customerId' => $customerId],[],'UTF-8');
            return $pdf->download('SoaReport.pdf');          
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    public function soaExcelDownload(Request $request){
        if($request->has('user_id')){
            if($request->user_id){
                $data = $this->getUserLimitDetais($request->user_id);
            }
            if($request->has('soaType')){
                if($request->soaType == 'consolidatedSoa'){
                    $transactionList = $this->lmsRepo->getConsolidatedSoaList();
                }
                elseif($request->soaType == 'customerSoa'){
                    $transactionList = $this->lmsRepo->getSoaList();
                }
            }
            if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $transactionList->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                    $query->WhereBetween('trans_date', [$from_date, $to_date]);
                });
            }
            if($request->has('trans_entry_type')){
                $trans_entry_type = explode('_',$request->trans_entry_type);
                $trans_type = $trans_entry_type[0];
                $entry_type = $trans_entry_type[1];

                if($trans_type){
                    $transactionList->where('trans_type',$trans_type);
                }
                if($entry_type){
                    // $transactionList->where('entry_type',$entry_type);
                }
            }

            $transactionList->whereHas('lmsUser',function ($query) use ($request) {
                $customer_id = trim($request->get('customer_id'));
                $query->where('customer_id', '=', "$customer_id");
            });
        
        }
        $exceldata = $this->prepareDataForRendering($transactionList->get()->filter(function($item){
            return $item->transaction->is_transaction;
        })->chunk(25));
        $sheet =  new PHPExcel();
        $sheet->getActiveSheet()->mergeCells('A2:K2');
        $sheet->getActiveSheet()->mergeCells('A3:K3');
        $sheet->getActiveSheet()
        ->getStyle('A2:K3')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setActiveSheetIndex(0)
            ->setCellValue('A2', 'CAPSAVE FINANCE PRIVATE LIMITED')
            ->setCellValue('A3', 'Statement Of Account');

        if(!empty($data)){
            $sheet->setActiveSheetIndex(0)
            ->setCellValue('A5', 'Business Name')
            ->setCellValue('A6', 'Email')
            ->setCellValue('C5', $data['userInfo']->biz->biz_entity_name)
            ->setCellValue('C6', $data['userInfo']->email)
            ->setCellValue('H5', 'Full Name')
            ->setCellValue('H6', 'Mobile')
            ->setCellValue('J5', $data['userInfo']->f_name." ".$data['userInfo']->m_name." ".$data['userInfo']->l_name)
            ->setCellValueExplicit('J6', $data['userInfo']->mobile_no, PHPExcel_Cell_DataType::TYPE_STRING);
        }
                
        
        $sheet->getActiveSheet()->getStyle('A1:I7')->applyFromArray(['font' => ['bold'  => true]]);
        $sheet->getActiveSheet()->getStyle("D2")
            ->applyFromArray(['font' => ['bold'  => true, 'size' => 15]]);
                
        $rows = 8;
        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A7', 'From Date')
                ->setCellValue('C7', $request->get('from_date'))
                ->setCellValue('H7', 'To Date')
                ->setCellValue('J7', $request->get('to_date'));
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
        
        $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => [ 'rgb' => "CAD7D3" ],
            'font' => [ 'bold'  => true ]
        ));
              
        $sheet->getActiveSheet()
        ->getStyle('I:K')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getActiveSheet()
        ->getStyle('B:C')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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
                if($rowData['soabackgroundcolor']){
                    $color = trim($rowData['soabackgroundcolor'],'#');
                }
                
                $sheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array( 'rgb' => $color)
                ));
                $rows++;
            }
        }
        foreach(range('A','K') as $columnID) {
            $sheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
       
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Soa_Excel.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

}