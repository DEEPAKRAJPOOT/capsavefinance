<?php
namespace App\Http\Controllers\Lms;
use Auth;
use Session;
use Helpers;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class CibilReportController extends Controller
{
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
      $businessData = $this->lmsRepo->getAllBusinessData();
      $hd = $this->_getHDData($businessData);
      $bs = $this->_getBSData($businessData);
      $as = $this->_getASData($businessData);
      $rs = $this->_getRSData($businessData);
      $cr = $this->_getCRData($businessData);
      $gs = $this->_getGSData($businessData);
      $ss = $this->_getSSData($businessData);
      $cd = $this->_getCDData($businessData);
      $ts = $this->_getTSData($businessData);
      $insertArr = array_merge($hd,$bs,$as,$rs,$cr,$gs,$ss,$cd,$ts);
      $isInserted = $this->lmsRepo->insertCibilUserData($insertArr);
      return $insertArr;
    }

    private function _getHDData($businessData) {
      $data = [
        'Segment Identifier' => 'HD',
        'Member ID' => NULL,
        'Previous Member ID' => NULL,
        'Date of Creation & Certification of Input File' => NULL,
        'Reporting / Cycle Date' => NULL,
        'Information Type' => '01',
        'Filler' => NULL,
      ];
      $hd[] = [
        'ac_no' => '',
        'segment_identifier' => $data['Segment Identifier'],
        'segment_data' => json_encode($data),
        'created_at' => Carbon::now(),
        'created_by' => Auth::user()->user_id,
      ];
      return $hd;
    }

    private function _getBSData($businessData) {
      foreach ($businessData as $bs_val) {
          $data = [
            'Segment Identifier' => 'BS',
            'Member Branch Code' => NULL,
            'Previous Member Branch Code' => NULL,
            'Borrower’s Name' => $bs_val->biz_entity_name,
            'Borrower Short Name' => explode(' ', $bs_val->biz_entity_name)[0] ?? NULL,
            'Company Registration Number' => NULL,
            'Date of Incorporation' => $bs_val->date_of_in_corp,
            'PAN' => $bs_val->pan->pan_gst_hash ?? NULL,
            'CIN' => $bs_val->cin->cin ?? NULL,
            'TIN' => NULL,
            'Service Tax #' => NULL,
            'Other ID' => NULL,
            'Borrower’s Legal Constitution' => $bs_val->constitution->name,
            'Business Category' => NULL,
            'Business/ Industry Type' => $bs_val->industryType->name,
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
        $bs[] = [
          'ac_no' => '',
          'segment_identifier' => $data['Segment Identifier'],
          'segment_data' => json_encode($data),
          'created_at' => Carbon::now(),
          'created_by' => Auth::user()->user_id,
        ];
      }
      return $bs;
    }

    private function _getASData($businessData) {
        $addressType = [
              '0' =>'GST Address',
              '1' =>'Communication',
              '2' =>'Futureuse',
              '3' =>'Warehouse',
              '4' =>'Factory',
              '5' =>'Mgmt Address',
              '6' =>'Additional Address',
        ];
        foreach ($businessData as $as_val) {
            $addr_data = $as_val->registeredAddress;
            $users = $as_val->users;
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
          $as[] = [
            'ac_no' => '',
            'segment_identifier' => $data['Segment Identifier'],
            'segment_data' => json_encode($data),
            'created_at' => Carbon::now(),
            'created_by' => Auth::user()->user_id,
          ];
        }
        return $as;
    }

    private function _getRSData($businessData) {
      foreach ($businessData as $rs_val) {
        $users = $rs_val->users;
        $addr_data = $rs_val->registeredAddress;
        $fullAddress = NULL;
        if (isset($addr_data->addr_1)) {
           $fullAddress = $addr_data->addr_1 . ' ' . $addr_data->addr_2. ' ' . $addr_data->city_name. ' ' .($addr_data->state->name ?? NULL) . ' ' . $addr_data->pin_code;
        }
        $data = [
          'Segment Identifier' => 'RS',
          'Relationship DUNS Number' => '999999999',
          'Related Type' => NULL,
          'Relationship' => NULL,
          'Business Entity Name' => $rs_val->biz_entity_name,
          'Business Category' => NULL,
          'Business / Industry Type' => $rs_val->industryType->name,
          'Individual Name Prefix' => NULL,
          'Full Name' => $users->f_name . ' '. $users->m_name . ' ' . $users->l_name,
          'Gender' => NULL,
          'Company Registration Number' => NULL,
          'Date of Incorporation' => $rs_val->date_of_in_corp,
          'Date of Birth' => NULL,
          'PAN' => $rs_val->pan->pan_gst_hash ?? NULL,
          'Voter ID' => NULL,
          'Passport Number' => NULL,
          'Driving Licence ID' => NULL,
          'UID' => NULL,
          'Ration Card No' => NULL,
          'CIN' => $rs_val->cin->cin ?? NULL,
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
        $rs[] = [
          'ac_no' => '',
          'segment_identifier' => $data['Segment Identifier'],
          'segment_data' => json_encode($data),
          'created_at' => Carbon::now(),
          'created_by' => Auth::user()->user_id,
        ];
      }
      return $rs;
    }


    private function _getCRData($businessData) {
      foreach ($businessData as $cr_val) { 
        $data = [
            'Segment Identifier' => 'CR',
            'Account Number' => NULL,
            'Previous Account Number' => NULL,
            'Facility / Loan Activation / Sanction Date' => NULL,
            'Sanctioned Amount/ Notional Amount of Contract' => NULL,
            'Currency Code' => 'INR',
            'Credit Type' => NULL,
            'Tenure / Weighted Average maturity period of Contracts' => NULL,
            'Repayment Frequency' => NULL,
            'Drawing Power' => NULL,
            'Current   Balance / Limit Utilized /Mark to Market' => NULL,
            'Notional Amount of Out-standing Restructured Contracts' => NULL,
            'Loan Expiry / Maturity Date' => NULL,
            'Loan Renewal Date' => NULL,
            'Asset Classification/Days Past Due (DPD)' => NULL,
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
            'Wilful Default Status' => NULL,
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
        $cr[] = [
          'ac_no' => '',
          'segment_identifier' => $data['Segment Identifier'],
          'segment_data' => json_encode($data),
          'created_at' => Carbon::now(),
          'created_by' => Auth::user()->user_id,
        ];
      }
      return $cr;
    }

    private function _getGSData($businessData) {
      foreach ($businessData as $gs_val) {
        $data =  [
            'Segment Identifier' => 'GS',
            'Guarantor DUNS Number' => '999999999',
            'Guarantor Type' => NULL,
            'Business Category' => NULL,
            'Business / Industry Type' => NULL,
            'Guarantor Entity Name' => NULL,
            'Individual Name Prefix' => NULL,
            'Full Name' => NULL,
            'Gender' => NULL,
            'Company Registration Number' => NULL,
            'Date of Incorporation' => NULL,
            'Date of Birth' => NULL,
            'PAN' => NULL,
            'Voter ID' => NULL,
            'Passport Number' => NULL,
            'Driving Licence ID' => NULL,
            'UID' => NULL,
            'Ration Card No' => NULL,
            'CIN' => NULL,
            'DIN' => NULL,
            'TIN' => NULL,
            'Service Tax #' => NULL,
            'Other ID' => NULL,
            'Address Line 1' => NULL,
            'Address Line 2' => NULL,
            'Address Line 3' => NULL,
            'City/Town' => NULL,
            'District' => NULL,
            'State/Union Territory' => NULL,
            'Pin Code' => NULL,
            'Country' => NULL,
            'Mobile Number(s)' => NULL,
            'Telephone Area Code' => NULL,
            'Telephone Number(s)' => NULL,
            'Fax Area Code' => NULL,
            'Fax Number(s)' => NULL,
            'Filler' => NULL,
        ];
        $gs[] = [
          'ac_no' => '',
          'segment_identifier' => $data['Segment Identifier'],
          'segment_data' => json_encode($data),
          'created_at' => Carbon::now(),
          'created_by' => Auth::user()->user_id,
        ];
      }
      return $gs;
    }

     private function _getSSData($businessData) {    
      $data = [
          'Segment Identifier' => 'SS',
          'Value of Security' => NULL,
          'Currency Type' => 'INR',
          'Type of Security' => NULL,
          'Security Classification' => NULL,
          'Date of Valuation' => NULL,
          'Filler' => NULL,
      ];
      $ss[] = [
        'ac_no' => '',
        'segment_identifier' => $data['Segment Identifier'],
        'segment_data' => json_encode($data),
        'created_at' => Carbon::now(),
        'created_by' => Auth::user()->user_id,
      ];
      return $ss;
    }
    private function _getCDData($businessData) {
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
      $cd[] = [
        'ac_no' => '',
        'segment_identifier' => $data['Segment Identifier'],
        'segment_data' => json_encode($data),
        'created_at' => Carbon::now(),
        'created_by' => Auth::user()->user_id,
      ];
      return $cd;
    }
    private function _getTSData($businessData) {
      $data = [
          'Segment Identifier' => 'TS',
          'Number of Borrower Segments' => NULL,
          'Number of Credit Facility Segments' => NULL,
          'Filler' => NULL,
      ];
      $ts[] = [
        'ac_no' => '',
        'segment_identifier' => $data['Segment Identifier'],
        'segment_data' => json_encode($data),
        'created_at' => Carbon::now(),
        'created_by' => Auth::user()->user_id,
      ];
      return $ts;
    }

}