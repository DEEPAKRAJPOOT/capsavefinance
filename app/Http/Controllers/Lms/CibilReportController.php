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
			/*$appId = 60;
			$filesArr = $this->fileHelper->getLatestFileName($appId, 'banking', 'json');
			$new_json_filename = $filesArr['new_file'];
			$curr_json_filename = $filesArr['curr_file'];
			$new_json_fullpath = $this->fileHelper->getToUploadPath($appId, 'banking'). '/'.$new_json_filename;
			$curr_json_fullpath = $this->fileHelper->getToUploadPath($appId, 'banking'). '/'.$curr_json_filename;
			$fileContents = getFinContent();
			// $isSaved = $this->fileHelper->uploadFileWithContent($new_json_fullpath, $fileContents);
			$isSaved = $this->fileHelper->readFileContent($curr_json_fullpath);
			dd($isSaved);*/
       return view('lms.cibilReport.list');
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
           $toExportData['Cibil Report'] = $cibilArr;
           return $this->fileHelper->array_to_excel($toExportData);
       }
       $pdfArr = ['pdfArr' => $cibilArr];
       $pdf = $this->fileHelper->array_to_pdf($pdfArr);
       return $pdf->download('CibilReport.pdf'); 
    }

}