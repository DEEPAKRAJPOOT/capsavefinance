<?php
namespace App\Http\Controllers\Lms;
use Auth;
use Session;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\FinanceModel;
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
            $cond[] = " search_keyword like '%$search_keyword%' ";
       }
       if (!empty($cond)) {
           $whereRaw = implode(' AND ', $cond);
       }
       $cibilReports = $this->lmsRepo->getCibilReports([], $whereRaw);
       $cibilRecords = $cibilReports->get();
       $cibilArr = [];
       $cibilStatus = ['Pending', 'Fail', 'Success'];
       foreach ($cibilRecords as $cibil) {
         $cibilArr[] = [
            'Customer Name' => $cibil->username, 
            'Business Name' => $cibil->biz_name, 
            'Pull Date' =>  (Carbon::createFromFormat('Y-m-d', $cibil->pull_date)->format('d/m/Y')), 
            'Pull Status' => $cibilStatus[$cibil->pull_status] ?? 'No Status', 
            'Pull By' => ($cibil->users->f_name . ' '. $cibil->users->m_name), 
         ];
       }
       if (strtolower($request->type) == 'excel') {
           $this->getCibilData();
           /*$toExportData['Cibil Report'] = $cibilArr;
           return $this->fileHelper->array_to_excel($toExportData);*/
       }
       if (strtolower($request->type) == 'insert') {
          $InsertedData = $this->saveCibilData();
          dd($InsertedData);
       }
       if (empty($cibilArr)) {
          $cibilArr[][''] = 'No record found'; 
       }
       $pdfArr = ['pdfArr' => $cibilArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr);
       return $pdf->download('CibilReport.pdf'); 
    }

    public function getCibilData() {
      $userCibilData = [];
      $cibilUserData = $this->lmsRepo->getCibilUserData();
      foreach ($cibilUserData as $key => $cibilData) {
        $ac_no = $cibilData->ac_no;
        $segment_identifier = strtoupper($cibilData->segment_identifier);
        $segment_data = json_decode($cibilData->segment_data, true);
        $merge_segment = array_merge(['ac_no' => $ac_no], $segment_data);
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
      $batch_no = _getRand(15);
      $cibilReportData['hd'] = $this->_getHDData();
      $cibilReportData['ts'] = $this->_getTSData();
      foreach ($businessData as $key => $appBusiness) {
          $appId = $appBusiness->app->app_id;
          $this->selectedAppData[] = $appId;
          $cibilReportData['bs'] = $this->_getBSData($appBusiness);
          $cibilReportData['as'] = $this->_getASData($appBusiness);
          $cibilReportData['rs'] = $this->_getRSData($appBusiness);
          $cibilReportData['cr'] = $this->_getCRData($appBusiness);
          $cibilReportData['gs'] = $this->_getGSData($appBusiness);
          $cibilReportData['ss'] = $this->_getSSData($appBusiness);
          $cibilReportData['cd'] = $this->_getCDData($appBusiness);
          dd($cibilReportData);
          foreach ($cibilReportData as $segment => $segmentData) {
            $finalCibilData[] = [
              'ac_no' => $batch_no,
              'segment_identifier' => $segment,
              'segment_data' => json_encode($segmentData),
              'created_at' => Carbon::now(),
              'created_by' => Auth::user()->user_id,
            ];
          }
          $cibilReportData = [];
      }
      try {
        if (empty($finalCibilData)) {
           $response['message'] =  'No Records are selected to Post in tally.';
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
            'batch_no' => $batch_no,
            'record_cnt' => $recordsTobeInserted,
            'created_at' => date('Y-m-d H:i:s'),
          ];
          $tally_inst_data = FinanceModel::dataLogger($batchData, 'tally');
          $response['message'] =  ($recordsTobeInserted > 1 ? $recordsTobeInserted .' Records inserted successfully' : '1 Record inserted.');
        }
    }else{
      $response['message'] =  ($res[2] ?? 'DB error occured.').' No Record can be posted in Cibil.';
    }

      return $response;
    }

    private function _getHDData() {
      $data = [
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
          $data = [
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
            'Borrower’s Legal Constitution' => $appBusiness->constitution->name,
            'Business Category' => config('common.MSMETYPE')[$appBusiness->msme_type],
            'Business/ Industry Type' => $appBusiness->industryType->name,
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
        $data = [
          'Segment Identifier' => 'AS',
          'Borrower Office Location Type' => $addressType[$addr_data->address_type ?? 0] ?? NULL,
          'Borrower Office DUNS Number' => '999999999',
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
        $data = [
          'Segment Identifier' => 'RS',
          'Relationship DUNS Number' => '999999999',
          'Related Type' => NULL,
          'Relationship' => $appBusiness->constitution->name,
          'Business Entity Name' => $appBusiness->biz_entity_name,
          'Business Category' => $appBusiness->msme_type,//config('common.MSMETYPE')[$appBusiness->msme_type],
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
        $data = [
            'Segment Identifier' => 'CR',
            'Account Number' => Helper::formatIdWithPrefix($user->user_id, 'CUSTID'),
            'Previous Account Number' => NULL,
            'Facility / Loan Activation / Sanction Date' => NULL,
            'Sanctioned Amount/ Notional Amount of Contract' => NULL,
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
            'Wilful Default Status' => 'Calculate',
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

        $data =  [
            'Segment Identifier' => 'GS',
            'Guarantor DUNS Number' => '999999999',
            'Guarantor Type' => 'Calculate',
            'Business Category' => $appBusiness->msme_type,
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
      $primarySecurity = $appBusiness->app->appPrgmOffer->offerPs;   
      $data = [
          'Segment Identifier' => 'SS',
          'Value of Security' => 'Calculate',
          'Currency Type' => 'INR',
          'Type of Security' => NULL,
          'Security Classification' => NULL,
          'Date of Valuation' => NULL,
          'Filler' => NULL,
      ];
      return $data;
    }
    private function _getCDData($appBusiness) {
      $data = [
          'Segment Identifier' => 'CD',
          'Date of Dishonour' => NULL,
          'Amount' => NULL,
          'Instrument / Cheque Number' => NULL,
          'Number of times dishonoured' => NULL,
          'Cheque Issue Date' => NULL,
          'Reason for Dishonour' => NULL,
          'Filler' => NULL,
      ];
      return $data;
    }
    private function _getTSData() {
      $data = [
          'Segment Identifier' => 'TS',
          'Number of Borrower Segments' => NULL,
          'Number of Credit Facility Segments' => NULL,
          'Filler' => NULL,
      ];
      return $data;
    }

}