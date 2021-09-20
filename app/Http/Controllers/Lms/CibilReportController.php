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
    protected $selectedAppData = [];

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
          $InsertedData = $this->saveCibilData();
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

    public function saveCibilData() {
      $response = array(
        'status' => 'failure',
        'message' => 'Request method not allowed to execute the script.',
      );
      $whereCond = ['status' => 2, 'is_posted_in_cibil' => 0];
      $businessData = $this->lmsRepo->getAllBusinessData($whereCond);
      $this->batch_no = _getRand(15);
      $cibilReportData['hd'] = $this->_getHDData();
      $cibilReportData['ts'] = $this->_getTSData();
      foreach ($businessData as $key => $appBusiness) {
          $appId = $appBusiness->app->app_id;
          $userId = $appBusiness->user_id;
          $this->selectedAppData[] = $appId;
          $this->formatedCustId = Helper::formatIdWithPrefix($userId, 'CUSTID');
          $this->business_category = array_search(config('common.MSMETYPE')[$appBusiness->msme_type], config('common.MSMETYPE')) ? $appBusiness->msme_type : NULL;
          $this->constitutionName = !empty($appBusiness->constitution->cibil_lc_code) ? $appBusiness->constitution->cibil_lc_code : ''; //config('common.LEGAL_CONSTITUTION')[$appBusiness->biz_constitution]


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
                'created_by' => Auth::user()->user_id,
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
        if (!empty($this->selectedAppData)) {
          $totalAppRecords = \DB::update('update rta_app set is_posted_in_cibil = 1 where app_id in(' . implode(', ', $this->selectedAppData) . ')');
        }
        $recordsTobeInserted = count($finalCibilData);
        if (empty($totalAppRecords)) {
          $response['message'] =  'Some error occured. No Record can be posted in Cibil.';
        }else{
          $response['status'] = 'success';
          $batchData = [
            'batch_no' => $this->batch_no,
            'app_cnt' => count($this->selectedAppData),
            'record_cnt' => $recordsTobeInserted,
            'created_at' => date('Y-m-d H:i:s'),
          ];
          $cibil_inst_data = FinanceModel::dataLogger($batchData, 'cibil_report');
          $response['message'] =  ($recordsTobeInserted > 1 ? $recordsTobeInserted .' Records inserted successfully' : '1 Record inserted.');
        }
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
            'Business/ Industry Type' => $appBusiness->industryType->name ?? NULL,
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
        $addressType = [
              '0' =>'GST Address',
              '1' =>'Communication',
              '2' =>'Futureuse',
              '3' =>'Warehouse',
              '4' =>'Factory',
              '5' =>'Mgmt Address',
              '6' =>'Additional Address',
        ];
        $addr_data = $appBusiness->registeredAddress;
        $users = $appBusiness->users;
        $fullAddress = NULL;
        if (isset($addr_data->addr_1)) {
          $fullAddress = $addr_data->addr_1 . ' ' . $addr_data->addr_2. ' ' . $addr_data->city_name. ' ' .($addr_data->state->name ?? NULL) . ' ' . $addr_data->pin_code;
        }
        $data[] = [
          'Ac No' => $this->formatedCustId,
          'Segment Identifier' => 'AS',
          'Borrower Office Location Type' => $addressType[$addr_data->address_type ?? 0] ?? NULL,
          'Borrower Office DUNS Number' => NULL,
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
        $sanctionDate = $appBusiness->app->sanctionDate->created_at ?? NULL;
        $prgmLimit = $appBusiness->app->prgmLimit->limit_amt ?? NULL;
        $data[] = [
            'Ac No' => $this->formatedCustId,
            'Segment Identifier' => 'CR',
            'Account Number' => Helper::formatIdWithPrefix($user->user_id, 'CUSTID'),
            'Previous Account Number' => NULL,
            'Facility / Loan Activation / Sanction Date' => !empty($sanctionDate) ? date('d M Y', strtotime($sanctionDate)) : NULL,
            'Sanctioned Amount/ Notional Amount of Contract' => $prgmLimit,
            'Currency Code' => 'INR',
            'Credit Type' => '0100',
            'Tenure / Weighted Average maturity period of Contracts' => NULL,
            'Repayment Frequency' => '08',
            'Drawing Power' => NULL,
            'Current   Balance / Limit Utilized /Mark to Market' => NULL,
            'Notional Amount of Out-standing Restructured Contracts' => NULL,
            'Loan Expiry / Maturity Date' => NULL,
            'Loan Renewal Date' => NULL,
            'Asset Classification/Days Past Due (DPD)' => 'Calculate',
            'Asset Classification Date' => NULL,
            'Amount Overdue / Limit Overdue' => NULL,
            'Overdue Bucket 01 ( 1 – 30 days)' => NULL,
            'Overdue Bucket 02 ( 31 – 60 days)' => NULL,
            'Overdue Bucket 03 ( 61 – 90 days)' => NULL,
            'Overdue Bucket 04 (91 – 180 days)' => NULL,
            'Overdue Bucket 05 (Above 180 days)' => NULL,
            'High Credit' => NULL,
            'Installment Amount' => NULL,
            'Last Repaid Amount' => NULL,
            'Account Status' => NULL,
            'Account Status Date' => NULL,
            'Written Off Amount' => NULL,
            'Settled Amount' => NULL,
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
            'Business / Industry Type' => $appBusiness->industryType->name,
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

}
