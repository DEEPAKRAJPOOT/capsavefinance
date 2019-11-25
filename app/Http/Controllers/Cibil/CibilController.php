<?php
 
namespace App\Http\Controllers\Cibil;

use App\Http\Controllers\Controller;
use App\Libraries\Ui\CibilApi;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Models\BizOwner;
use Session;
use File;
 
class CibilController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, CibilApi $CibilApi){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
     }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromoterCibilRequest(CibilApi $CibilApi, Request $request)
    {      
        $biz_owner_id = 1;
        $arrOwnerData = BizOwner::getBizOwnerDataByOwnerId($biz_owner_id);
        $arrOwnerData->date_of_birth = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        $responce =  $CibilApi->getPromoterCibilRequest($arrOwnerData);
        
        $file_name = 'cibil.txt';
        File::put(storage_path('app/public/cibil').'/'.$file_name, $responce);
        //$jsonData = json_encode($responce); 
 
        $new = simplexml_load_string($responce); 
        // Convert into json 
        $con = json_encode($new); 
        // Convert into associative array 
        $newArr = json_decode($con, true); 
        $creditScore = '0';
        if(!empty($newArr['INDV-REPORTS']['INDV-REPORT']['SCORES']))
        {
            $creditScore = $newArr['INDV-REPORTS']['INDV-REPORT']['SCORES']['SCORE'][0]['SCORE-VALUE'];
        }

        $req =   json_encode(array('dl' => $result['dl_no'],'dob' => $result['dob']));
           $createApiLog = BizApiLog::create(['req_file' =>$requestPan['epic_no'], 'res_file' => json_encode($result['response']->result),'status' => 0]);
          if ($createApiLog) {
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1, 'value' => $createApiLog['biz_api_log_id']]);
            } else {
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }




        return $creditScore;
    }


    
}