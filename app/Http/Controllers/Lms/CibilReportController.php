<?php
namespace App\Http\Controllers\Lms;
use Auth;
use Session;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Lms\Transactions;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class CibilReportController extends Controller
{   
    protected $selectedDisbursedData = [];

	  public function __construct(FileHelper $file_helper, InvLmsRepoInterface $lms_repo){
		 $this->fileHelper = $file_helper;
		 $this->lmsRepo = $lms_repo;
	  }

	
	  public  function index(Request $request) {
       return view('lms.cibilReport.list');
    }

    public function uploadFile($appId = 60) {
      $filesArr = $this->fileHelper->getLatestFileName($appId, 'banking', 'json');
      $new_json_filename = $filesArr['new_file'];
      $curr_json_filename = $filesArr['curr_file'];
      $new_json_fullpath = $this->fileHelper->getToUploadPath($appId, 'banking'). '/'.$new_json_filename;
      $curr_json_fullpath = $this->fileHelper->getToUploadPath($appId, 'banking'). '/'.$curr_json_filename;
      $fileContents = getFinContent();
      $uploaded = $this->fileHelper->uploadFileWithContent($new_json_fullpath, $fileContents);
      $getFile = $this->fileHelper->readFileContent($curr_json_fullpath);
      dd($uploaded, $getFile);
    }

    public function downloadCibilReport(Request $request) {
    	$whereRaw = '';
       if(!empty($request->get('from_date')) && !empty($request->get('to_date'))){
            $from_date = $request->get('from_date');
            $to_date = $request->get('to_date');
            $cond[] = " pull_date between '$from_date' AND '$to_date' ";
       }
       if(!empty($request->get('search_keyword'))){
            $search_keyword = $request->get('search_keyword');
            $cond[] = " batch_no like '%$search_keyword%' ";
       }
       if(!empty($request->get('batch_no'))){
            $batch_no = $request->get('batch_no');
            $cond[] = " batch_no = '$batch_no' ";
       }
       if (!empty($cond)) {
           $whereRaw = implode(' AND ', $cond);
       }
       $cibilArr = [];
       $cibilUserData = $this->lmsRepo->getCibilUserData([], $whereRaw);
       foreach ($cibilUserData as $key => $cibilData) {
          $batch_no = $cibilData->batch_no;
          $segment_identifier = strtoupper($cibilData->segment_identifier);
          $segment_data = json_decode($cibilData->segment_data, true);
          // $merge_segment = array_merge(['batch_no' => $batch_no], $segment_data);
          $merge_segment = $segment_data;
          $cibilArr[$segment_identifier][]  = array_merge($merge_segment, ['Final Formula' => implode('|', $merge_segment)]);
        }
       if (strtolower($request->type) == 'excel') {
           return $this->fileHelper->array_to_excel($cibilArr, 'CibilReport.xlsx');
       }
       if (strtolower($request->type) == 'insert') {
          $InsertedData = $this->_getMonthLastDate();
          return $InsertedData;
       }
       $pdfArr = ['pdfArr' => $cibilArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr);
       return $pdf->download('CibilReport.pdf'); 
    }

    public function getCibilData() {
      $userCibilData = [];
      $cibilUserData = $this->lmsRepo->getCibilUserData();
      foreach ($cibilUserData as $key => $cibilData) {
        $batch_no = $cibilData->batch_no;
        $segment_identifier = strtoupper($cibilData->segment_identifier);
        $segment_data = json_decode($cibilData->segment_data, true);
        $merge_segment = array_merge(['batch_no' => $batch_no], $segment_data);
        $userCibilData[$segment_identifier][]  = array_merge($merge_segment, ['Final Formula' => implode('|', $merge_segment)]);
      }
      return $this->fileHelper->array_to_excel($userCibilData);
    }

    public function saveCibilData($date) {
      ini_set("memory_limit", "-1");
      $this->selectedDisbursedData = [];
      $response = array(
        'status' => 'failure',
        'message' => 'Request method not allowed to execute the script.',
      );
      $this->batch_no = _getRand(15);
      $cibilReportData['hd'] = $this->_getHDData();
      $cibilReportData['ts'] = $this->_getTSData();
      $countBucketData = $this->lmsRepo->getOverdueData();

      $this->userWiseData = [];
      foreach ($countBucketData as $key => $bucketData) {
        $this->userWiseData[$bucketData->supplier_id] = $bucketData; 
      };
      // $date = "2021-10-31 23:59:59";
      $whereCond = ['date' => $date, 'status_ids' => [12,13,15]];
      $cibilRecords = $this->lmsRepo->getAllBusinessForSheet($whereCond);

      foreach ($cibilRecords as $key => $cibilRecord) {
          $this->cibilRecord = $cibilRecord;
          $appBusiness = $cibilRecord->business;
          $appId = $appBusiness->app->app_id;
          $userId = $appBusiness->user_id;

          $this->selectedDisbursedData[] = $this->cibilRecord->invoice_disbursed->invoice_disbursed_id;

          $capId = sprintf('%09d', $userId);
				  $customerId = 'CAP'.$capId;
          $this->formatedCustId = $customerId; /* Helper::formatIdWithPrefix($userId, 'CUSTID') */
          $this->business_category = isset($appBusiness->msme_type) && array_search(config('common.MSMETYPE')[$appBusiness->msme_type], config('common.MSMETYPE')) ? $appBusiness->msme_type : NULL;
          $this->constitutionName = !empty($appBusiness->constitution->cibil_lc_code) ? $appBusiness->constitution->cibil_lc_code : ''; //config('common.LEGAL_CONSTITUTION')[$appBusiness->biz_constitution]
          $this->account_status = $this->lmsRepo->getAccountStatus($userId); 

          $cibilReportData['bs'] = $this->_getBSData($appBusiness);
          $cibilReportData['as'] = $this->_getASData($appBusiness);
          $cibilReportData['rs'] = $this->_getRSData($appBusiness);
          $cibilReportData['cr'] = $this->_getCRData($appBusiness);
          $cibilReportData['gs'] = $this->_getGSData($appBusiness);
          $cibilReportData['ss'] = $this->_getSSData($appBusiness);
          $cibilReportData['cd'] = $this->_getCDData($appBusiness);
          foreach ($cibilReportData as $segment => $segmentData) {
            if (empty($segmentData)) {
                continue;
            }
            foreach ($segmentData as $key => $segData) {
              $finalCibilData[] = [
                'batch_no' => $this->batch_no,
                'segment_identifier' => $segment,
                'segment_data' => json_encode($segData),
                'created_at' => Carbon::now(),
                'created_by' => Auth::user()->user_id ?? 1,
              ];
            }
          }
          $cibilReportData = [];
      }
      try {
        if (empty($finalCibilData)) {
           $response['message'] =  'No Records are selected to Post in Cibil.';
           return $response;
        }
        $res = $this->lmsRepo->insertCibilUserData($finalCibilData);
      } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $res = $errorInfo;
      }
      if ($res === true) {
        $totalAppRecords = 0;
        if (!empty($this->selectedDisbursedData)) {
          $totalAppRecords = \DB::update('update rta_invoice_disbursed set is_posted_in_cibil = 1 where invoice_disbursed_id in(' . implode(', ', $this->selectedDisbursedData) . ')');
        }
        $recordsTobeInserted = count($finalCibilData);
        // if (empty($totalAppRecords)) {
        //   $response['message'] =  'Some error occured. No Record can be posted in Cibil.';
        // }else{
          $response['status'] = 'success';
          $batchData = [
            'batch_no' => $this->batch_no,
            'invoice_cnt' => count($this->selectedDisbursedData),
            'record_cnt' => $recordsTobeInserted,
            'created_at' => date($date),
          ];
          $cibil_inst_data = FinanceModel::dataLogger($batchData, 'cibil_report');
          $response['message'] =  ($recordsTobeInserted > 1 ? $recordsTobeInserted .' Records inserted successfully' : '1 Record inserted.');
        // }
    }else{
      $response['message'] =  ($res[2] ?? 'DB error occured.').' No Record can be posted in Cibil.';
    }

      return $response;
    }

    private function _getHDData() {
      $data[] = [
        'Ac No' => NULL,
        'Segment Identifier' => 'HD',
        'Member ID' => config('common.cibil_report.MEMBER_ID'),
        'Previous Member ID' => config('common.cibil_report.PREV_MEMBER_ID'),
        'Date of Creation & Certification of Input File' => Carbon::now()->format('dmY'),
        'Reporting / Cycle Date' => Carbon::now()->format('dmY'),
        'Information Type' => '01',
        'Filler' => NULL,
      ];
      return $data;
    }

    private function _getBSData($appBusiness) {
          $data[] = [
            'Ac No' => $this->formatedCustId,
            'Segment Identifier' => 'BS',
            'Member Branch Code' => config('common.cibil_report.MEMBER_BRANCH_CODE'),
            'Previous Member Branch Code' => config('common.cibil_report.PREV_MEMBER_BRANCH_CODE'),
            'Borrower’s Name' => $appBusiness->biz_entity_name,
            'Borrower Short Name' => explode(' ', $appBusiness->biz_entity_name)[0] ?? NULL,
            'Company Registration Number' => NULL,
            'Date of Incorporation' => $appBusiness->date_of_in_corp,
            'PAN' => $appBusiness->pan->pan_gst_hash ?? NULL,
            'CIN' => $appBusiness->cin->cin ?? NULL,
            'TIN' => NULL,
            'Service Tax #' => NULL,
            'Other ID' => NULL,
            'Borrower’s Legal Constitution' => $this->constitutionName,
            'Business Category' => $this->business_category,
            'Business/ Industry Type' => $appBusiness->industryType->cibil_indus_code ?? NULL,
            'Class of Activity 1' => NULL,
            'Class of Activity 2' => NULL,
            'Class of Activity 3' => NULL,
            'SIC Code' => NULL,
            'Sales Figure' => NULL,
            'Financial Year' => NULL,
            'Number of Employees' => NULL,
            'Credit  Rating' => NULL,
            'Assessment Agency / Authority' => NULL,
            'Credit Rating As On' => NULL,
            'Credit Rating Expiry Date' => NULL,
            'Filler' => NULL,
        ];
        return $data;
    }

    private function _getASData($appBusiness) {
        // $addressType = [
        //       '0' =>'GST Address',
        //       '1' =>'Communication',
        //       '2' =>'Futureuse',
        //       '3' =>'Warehouse',
        //       '4' =>'Factory',
        //       '5' =>'Mgmt Address',
        //       '6' =>'Additional Address',
        // ];
        $addr_data = $appBusiness->registeredAddress;
        $users = $appBusiness->users;
        $fullAddress = NULL;
        if (isset($addr_data->addr_1)) {
          $fullAddress = $addr_data->addr_1 . ' ' . $addr_data->addr_2. ' ' . $addr_data->city_name. ' ' .($addr_data->state->name ?? NULL) . ' ' . $addr_data->pin_code;
        }
        $data[] = [
          'Ac No' => $this->formatedCustId,
          'Segment Identifier' => 'AS',
          'Borrower Office Location Type' => isset($addr_data->getLocationType) ? $addr_data->getLocationType->name : 'Registered Office',
          'Borrower Office DUNS Number' => NULL,
          'Address Line 1' => $fullAddress,
          'Address Line 2' => NULL,
          'Address Line 3' => NULL,
          'City/Town' => $addr_data->city_name ?? NULL,
          'District' => NULL,
          'State/Union Territory' => $addr_data->state->code ?? NULL,
          'Pin Code' => $addr_data->pin_code ?? NULL,
          'Country' => NULL,
          'Mobile Number(s)' => $users->mobile_no ?? NULL,
          'Telephone Area Code' => NULL,
          'Telephone Number(s)' => NULL,
          'Fax Area Code' => NULL,
          'Fax Number(s)' => NULL,
          'Filler' => NULL,
      ];
      return $data;
    }

    private function _getRSData($appBusiness) {
        $users = $appBusiness->users;
        $addr_data = $appBusiness->registeredAddress;
        $fullAddress = NULL;
        if (isset($addr_data->addr_1)) {
           $fullAddress = $addr_data->addr_1 . ' ' . $addr_data->addr_2. ' ' . $addr_data->city_name. ' ' .($addr_data->state->name ?? NULL) . ' ' . $addr_data->pin_code;
        }
        $data[] = [
          'Ac No' => $this->formatedCustId,
          'Segment Identifier' => 'RS',
          'Relationship DUNS Number' => NULL,
          'Related Type' => NULL,
          'Relationship' => $this->constitutionName,
          'Business Entity Name' => $appBusiness->biz_entity_name,
          'Business Category' => $this->business_category,
          'Business / Industry Type' => $appBusiness->industryType->name,
          'Individual Name Prefix' => NULL,
          'Full Name' => $users->f_name . ' '. $users->m_name . ' ' . $users->l_name,
          'Gender' => NULL,
          'Company Registration Number' => NULL,
          'Date of Incorporation' => $appBusiness->date_of_in_corp,
          'Date of Birth' => NULL,
          'PAN' => $appBusiness->pan->pan_gst_hash ?? NULL,
          'Voter ID' => NULL,
          'Passport Number' => NULL,
          'Driving Licence ID' => NULL,
          'UID' => NULL,
          'Ration Card No' => NULL,
          'CIN' => $appBusiness->cin->cin ?? NULL,
          'DIN' => NULL,
          'TIN' => NULL,
          'Service Tax #' => NULL,
          'Other ID' => NULL,
          'Percentage of Control' => NULL,
          'Address Line 1' => $fullAddress,
          'Address Line 2' => NULL,
          'Address Line 3' => NULL,
          'City/Town' => $addr_data->city_name ?? NULL,
          'District' => NULL,
          'State/Union Territory' => $addr_data->state->name ?? NULL,
          'Pin Code' => $addr_data->pin_code ?? NULL,
          'Country' => NULL,
          'Mobile Number(s)' => $users->mobile_no ?? NULL,
          'Telephone Number(s)' => NULL,
          'Telephone Area Code' => NULL,
          'Fax Number(s)' => NULL,
          'Fax Area Code' => NULL,
          'Filler' => NULL,
        ];
        return $data;
    }


    private function _getCRData($appBusiness) {
        $user = $appBusiness->users;
        $outstanding = Transactions::getUserOutstanding($user->user_id);
        $sanctionDate = $appBusiness->sanctionDate->created_at ?? NULL;
        $prgmLimit = $appBusiness->prgmLimit->limit_amt ?? NULL;
        $userData = isset($this->userWiseData[$user->user_id]) ? $this->userWiseData[$user->user_id] : null;
        $od_days = isset($userData) ? (int)$userData->od_days : 0;
        $od_outstanding = isset($userData) ? round($userData->od_outstanding, 2) : 0;
        $data[] = [
            'Ac No' => $this->formatedCustId,
            'Segment Identifier' => 'CR',
            'Account Number' => Helper::formatIdWithPrefix($user->user_id, 'CUSTID'),
            'Previous Account Number' => NULL,
            'Facility / Loan Activation / Sanction Date' => !empty($sanctionDate) ? date('d M Y', strtotime($sanctionDate)) : NULL,
            'Sanctioned Amount/ Notional Amount of Contract' => $prgmLimit,
            'Currency Code' => 'INR',
            'Credit Type' => '0300',
            'Tenure / Weighted Average maturity period of Contracts' => NULL,
            'Repayment Frequency' => '08',
            'Drawing Power' => NULL,
            'Current   Balance / Limit Utilized /Mark to Market' => isset($userData) ? $userData->utilized_amt : 0,
            'Notional Amount of Out-standing Restructured Contracts' => NULL,
            'Loan Expiry / Maturity Date' => NULL,
            'Loan Renewal Date' => NULL,
            'Asset Classification/Days Past Due (DPD)' => $od_days,
            'Asset Classification Date' => NULL,
            'Amount Overdue / Limit Overdue' => $od_outstanding,
            'Overdue Bucket 01 ( 1 – 30 days)' => ($od_days >= 1 && $od_days <= 30 ? $od_days : 0),
            'Overdue Bucket 02 ( 31 – 60 days)' => ($od_days >= 31 && $od_days <= 60 ? $od_days : 0),
            'Overdue Bucket 03 ( 61 – 90 days)' => ($od_days >= 61 && $od_days <= 90 ? $od_days : 0),
            'Overdue Bucket 04 (91 – 180 days)' => ($od_days >= 91 && $od_days <= 180 ? $od_days : 0),
            'Overdue Bucket 05 (Above 180 days)' => ($od_days > 180 ? $od_days : 0),
            'High Credit' => NULL,
            'Installment Amount' => NULL,
            'Last Repaid Amount' => NULL,
            'Account Status' => !empty($this->account_status->status_name) ? '01' : '02',
            'Account Status Date' => !empty($this->account_status->created_at) ? date('d M Y', strtotime($this->account_status->created_at)) : NULL,
            'Written Off Amount' => isset($userData) ? (int)$userData->write_off_amt : 0,
            'Settled Amount' => isset($userData) ? (int)$userData->settled_amt : 0,
            'Major reasons for Restructuring' => NULL,
            'Amount of Contracts Classified as NPA' => NULL,
            'Asset based Security coverage' => NULL,
            'Guarantee Coverage' => NULL,
            'Bank Remark Code' => NULL,
            'Wilful Default Status' => (!empty($outstanding) ? '1' : '0'),
            'Date Classified as Wilful Default' => NULL,
            'Suit Filed Status' => NULL,
            'Suit Reference Number' => NULL,
            'Suit Amount in Rupees' => NULL,
            'Date of Suit' => NULL,
            'Dispute ID No.' => NULL,
            'Transaction Type Code' => NULL,
            'OTHER_BK' => NULL,
            'UFCE (Amount)' => NULL,
            'UFCE Date' => NULL,
        ];
        return $data;
    }

    private function _getGSData($appBusiness) {
        $users = $appBusiness->users;
        $addr_data = $appBusiness->registeredAddress;
        $fullAddress = NULL;
        if (isset($addr_data->addr_1)) {
           $fullAddress = $addr_data->addr_1 . ' ' . $addr_data->addr_2. ' ' . $addr_data->city_name. ' ' .($addr_data->state->name ?? NULL) . ' ' . $addr_data->pin_code;
        }
        $data[] =  [
            'Ac No' => $this->formatedCustId,
            'Segment Identifier' => 'GS',
            'Guarantor DUNS Number' => NULL,
            'Guarantor Type' => (strpos(strtolower($this->constitutionName), 'private') !== false) ? '1' : '2' ,
            'Business Category' => $this->business_category,
            'Business / Industry Type' => isset($appBusiness->industryType->cibil_indus_code) ? $appBusiness->industryType->cibil_indus_code : NULL,
            'Guarantor Entity Name' => $appBusiness->biz_entity_name,
            'Individual Name Prefix' => NULL,
            'Full Name' => $users->f_name . ' '. $users->m_name . ' ' . $users->l_name,
            'Gender' => NULL,
            'Company Registration Number' => NULL,
            'Date of Incorporation' => $appBusiness->date_of_in_corp,
            'Date of Birth' => NULL,
            'PAN' => $appBusiness->pan->pan_gst_hash ?? NULL,
            'Voter ID' => NULL,
            'Passport Number' => NULL,
            'Driving Licence ID' => NULL,
            'UID' => NULL,
            'Ration Card No' => NULL,
            'CIN' => $appBusiness->cin->cin ?? NULL,
            'DIN' => NULL,
            'TIN' => NULL,
            'Service Tax #' => NULL,
            'Other ID' => NULL,
            'Address Line 1' => $fullAddress,
            'Address Line 2' => NULL,
            'Address Line 3' => NULL,
            'City/Town' => $addr_data->city_name ?? NULL,
            'District' => NULL,
            'State/Union Territory' => $addr_data->state->name ?? NULL,
            'Pin Code' => $addr_data->pin_code ?? NULL,
            'Country' => NULL,
            'Mobile Number(s)' => $users->mobile_no ?? NULL,
            'Telephone Area Code' => NULL,
            'Telephone Number(s)' => NULL,
            'Fax Area Code' => NULL,
            'Fax Number(s)' => NULL,
            'Filler' => NULL,
        ];
        return $data;
    }

     private function _getSSData($appBusiness) {
      $primarySecurity = $appBusiness->app->appPrgmOffer->offerPs ?? NULL;
      $data = [];
      if (!empty($primarySecurity) && !$primarySecurity->isEmpty()) {
          foreach ($primarySecurity as $key => $ps) {
            $data[] = [
              'Ac No' => $this->formatedCustId,
              'Segment Identifier' => 'SS',
              'Value of Security' => $ps->ps_desc_of_security,
              'Currency Type' => 'INR',
              'Type of Security' => config('common.ps_security_id')[$ps->ps_security_id] ?? NULL,
              'Security Classification' => config('common.ps_type_of_security_id')[$ps->ps_type_of_security_id] ?? NULL,
              'Date of Valuation' => NULL,
              'Filler' => NULL,
            ];
          } 
       } 
      return $data;
    }
    private function _getCDData($appBusiness) {
      $users = $appBusiness->users;
      $dishonouredData = Transactions::getDishonouredTxn($users->user_id);
      $countOfCheckBounce = $dishonouredData->count();
      if (!empty($dishonouredData) && !$dishonouredData->isEmpty()) {
        $checkbounceData = $dishonouredData[0];
        $rec = [
          'date_of_dishonour' => date('Ymd', strtotime($checkbounceData->sys_created_at)),
          'amount' => $checkbounceData->amount,
          'check_no' => '',
          'issue_date' => '',
        ];
      }
      $data[] = [
            'Ac No' => $this->formatedCustId,
            'Segment Identifier' => 'CD',
            'Date of Dishonour' => $rec['date_of_dishonour'] ?? NULL,
            'Amount' => $rec['amount'] ?? NULL,
            'Instrument / Cheque Number' => $rec['check_no'] ?? NULL,
            'Number of times dishonoured' => $countOfCheckBounce,
            'Cheque Issue Date' => NULL,
            'Reason for Dishonour' => ($countOfCheckBounce > 0 ? 'Insufficient Funds' : NULL),
            'Filler' => NULL,
      ];
      return $data;
    }
    private function _getTSData() {
      $data[] = [
          'Ac No' => NULL,
          'Segment Identifier' => 'TS',
          'Number of Borrower Segments' => NULL,
          'Number of Credit Facility Segments' => NULL,
          'Filler' => NULL,
      ];
      return $data;
    }

    public function _getMonthLastDate() {
      $lastRecord = \DB::select('select * from rta_cibil_report order by cibil_report_id desc limit 1');
      $currentDate = date('Y-m-d H:i:s');
      $monthDiff = 10;
      if(!empty($lastRecord)) {
        $lastPulledDate = $lastRecord[0]->created_at;
        $monthDiff = $this->_monthDifference($currentDate, $lastPulledDate);
      }

      for ($i = $monthDiff - 1; $i > 0; $i--) {
        $date = date('Y-m-t 23:59:59', strtotime(-$i . 'month'));
        $monthArr[] = $date;
        $monthNo = (int)date('m', strtotime($date));
        $response[$monthNo] = $this->saveCibilData($date);
        $response[$monthNo]['monthName'] = date('M Y', strtotime($date));        
      }
      
      if(empty($response)) {
        $response = array(
          'status' => 'failure',
          'message' => 'All Records are already pushed to cibil till last month.',
        );        
      }
      return $response;
    }

    private function _monthDifference($currentDate, $lastDate) {
      $ts1 = strtotime($lastDate);
      $ts2 = strtotime($currentDate);
      $year1 = date('Y', $ts1);
      $year2 = date('Y', $ts2);

      $month1 = date('m', $ts1);
      $month2 = date('m', $ts2);
      $diff = (($year2 - $year1) * 12) + ($month2 - $month1);      
      return $diff;
    }

}
