<?php
namespace App\Http\Controllers\Lms;
use Auth;
use Session;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;

class CibilReportController extends Controller
{
	public function __construct(FileHelper $fileHelper){
		$this->fileHelper = $fileHelper;
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

}