<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Mail;
use Helpers;
use Session;
use Storage;
use PDF as DPDF;
use PHPExcel;
use PHPExcel_IOFactory;
use Carbon\Carbon;
use App\Mail\ReviewerSummary;
use App\Libraries\Pdf;
use App\Libraries\Perfios_lib;
use App\Libraries\Bsa_lib;
use App\Libraries\MobileAuth_lib;
use App\Libraries\Gupshup_lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AnchorInfoRequest;
use App\Http\Requests\FinanceInformationRequest as FinanceRequest;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\CamHygiene;
use App\Inv\Repositories\Models\AppBizFinDetail;
use App\Inv\Repositories\Models\CamReviewerSummary;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\GroupCompanyExposure;
use App\Inv\Repositories\Models\Master\Group;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\OfferPTPQ;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\UserAppDoc;
use App\Inv\Repositories\Models\CamReviewSummPrePost;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\FinanceInterface as InvFinanceRepoInterface;
use App\Inv\Repositories\Contracts\Traits\CamTrait;
use App\Inv\Repositories\Contracts\Traits\CommonTrait;
use App\Inv\Repositories\Models\CamReviewSummRiskCmnt;
use App\Inv\Repositories\Models\Master\GroupCompany;
//use App\Inv\Repositories\Models\BankWorkCapitalFacility;
//use App\Inv\Repositories\Models\BankTermBusiLoan;
//use App\Inv\Repositories\Models\BankAnalysis;
//date_default_timezone_set('Asia/Kolkata');
use Validator;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Models\Anchor;

class CamController extends Controller
{
    use ApplicationTrait;
    use CamTrait;
    use CommonTrait;
    use ActivityLogTrait;
    protected $download_xlsx = TRUE;
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $pdf;
    protected $genBlankfinJSON = TRUE;
    protected $financeRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, Pdf $pdf, InvMasterRepoInterface $mstRepo, InvFinanceRepoInterface $finance_repo){
        $this->appRepo = $app_repo;
        $this->application = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->pdf = $pdf;
        $this->mstRepo = $mstRepo;
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->financeRepo = $finance_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        try{
            $arrRequest['biz_id'] = $request->get('biz_id');
            $arrRequest['app_id'] = $request->get('app_id');
            $arrBizData = Business::getApplicationById($arrRequest['biz_id']);
            $arrOwnerData = BizOwner::getCompanyOwnerByBizId($arrRequest['biz_id']);
            foreach ($arrOwnerData as $key => $arr) {
                  $arrOwner[$key] =  $arr['first_name'];
            }
            $arrEntityData = Business::getEntityByBizId($arrRequest['biz_id']);
            if(isset($arrEntityData['industryType'])){
                  $arrBizData['industryType'] = $arrEntityData['industryType'];
            }
            if(isset($arrEntityData['name'])){
                  $arrBizData['legalConstitution'] = $arrEntityData['name'];
            }
            $userData = $this->userRepo->getUserDetail($arrBizData['user_id']);
            //dd($userData);
            $whereCondition = [];
            $whereCondition['anchor_id'] = $userData['anchor_id'];
            $prgmData = $this->appRepo->getProgramData($whereCondition);
            $limitData = $this->appRepo->getAppLimit($arrRequest['app_id']);
            if(!empty($prgmData))
            {
               $arrBizData['prgm_name'] = $prgmData['prgm_name'];
            }
            $arrBizData['email']  = $arrEntityData['email'];
            $arrBizData['mobile_no']  = $arrEntityData['mobile_no'];
            $arrCamData = Cam::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();

            if(isset($arrCamData['t_o_f_security_check'])){
                $arrCamData['t_o_f_security_check'] = explode(',', $arrCamData['t_o_f_security_check']);
            }
            $app_data = $this->appRepo->getAppDataByBizId($arrRequest['biz_id']);
            $product_ids=[];
            foreach($app_data->products as $product){
              array_push($product_ids, $product->pivot->product_id);
            }
            $checkLeaseProduct = in_array(3, $product_ids); // check lease product only
            if( $checkLeaseProduct){
              $checkDisburseBtn='showDisburseBtn';
            }else{
              $checkDisburseBtn='';
            }
            $arrGroupCompany = array();
            if(isset($arrCamData['group_company']) && is_numeric($arrCamData['group_company'])){
              $arrGroupCompany = GroupCompanyExposure::where(['group_Id'=>$arrCamData['group_company'], 'app_id'=>$arrRequest['app_id'], 'is_active'=>1] )->get()->toArray();
              $arrMstGroup =  Group::where('id', (int)$arrCamData['group_company'])->first()->toArray();
             if(!empty($arrMstGroup)){
               $arrCamData['group_company'] = $arrMstGroup['name'];
             }
            }


            if(!empty($arrGroupCompany)){
                $temp = array();
                $arrUserData = $this->userRepo->find($arrGroupCompany['0']['updated_by'], '');
                $arrCamData->By_updated = "$arrUserData->f_name $arrUserData->l_name";
                $total = 0;
                foreach ($arrGroupCompany as $key => $value) {
                  $total = $total + $value['proposed_exposure'] + $value['outstanding_exposure'];
                  if($arrBizData->biz_entity_name == $value['group_company_name']){
                      $temp[] = $value;
                      unset($arrGroupCompany[$key]);
                  }
                }
                if(!empty($temp)){
                  $arrGroupCompany = array_merge($temp, $arrGroupCompany);
                }
                $arrCamData['total_exposure_amount'] = round($total,2);
            }
            $getAppDetails = $this->appRepo->getAppData($arrRequest['app_id']);
            $current_status=($getAppDetails)?$getAppDetails['curr_status_id']:'';
            $activeGroup =  $this->mstRepo->getAllActiveGroup();
            $productType = [1 => 'Supply Chain', 2 => 'Term Loan', 3=> 'Leasing'];
            return view('backend.cam.overview')->with([
                'arrCamData' =>$arrCamData ,
                'arrRequest' =>$arrRequest,
                'arrBizData' => $arrBizData,
                'arrOwner' =>$arrOwner,
                'limitData' =>$limitData,
                'current_status_id'=>$current_status,
                'checkDisburseBtn'=>$checkDisburseBtn,
                'arrGroupCompany'=>$arrGroupCompany,
                'activeGroup' => $activeGroup,
                'productType' => $productType
                ]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }

    }

    public function camInformationSave(Request $request){
      try{

           $arrCamData = $request->all();

           $userId = Auth::user()->user_id;

           if(!isset($arrCamData['t_o_f_takeout'])){
             $arrCamData['t_o_f_takeout'] = NULL; 
           }
           if(!isset($arrCamData['rating_no'])){
                   $arrCamData['rating_no'] = NULL;
           }
           if(!isset($arrCamData['t_o_f_security_check'])){
                   $arrCamData['t_o_f_security_check'] = NULL;
           }else{
                 $arrCamData['t_o_f_security_check'] = implode(',', $arrCamData['t_o_f_security_check']);
           }
           if(!isset($arrCamData['debt_on'])){
                   $arrCamData['debt_on'] = NULL;
           }else{
                    $arrCamData['debt_on'] =  Carbon::createFromFormat('d/m/Y', request()->get('debt_on'))->format('Y-m-d');
           }

           if(isset($arrCamData['group_company'])){
               $masterGroupData= array(
                   'name'=> $arrCamData['group_company'],
                   'is_active' => '1',
                   'created_by'=>Auth::user()->user_id
               );
                  
               $arrMstGroup = Group::updateOrcreate($masterGroupData)->toArray();
               $arrCamData['group_company'] = $arrMstGroup['id'];

               
               
               // dd($arrCamData);
               if(isset($arrCamData['group_company_name']))
               {

                 //GroupCompanyExposure::where('group_Id', $arrMstGroup['id'])->delete();
                   foreach($arrCamData['group_company_name'] as $key => $groupCompanyName) {
                      $inputArr= array(
                         'biz_id'=> $arrCamData['biz_id'] ,
                         'app_id'=> $arrCamData['app_id'],
                         'group_Id'=> $arrMstGroup['id'],
                         'group_company_name'=> $groupCompanyName ?? null,
                         'sanction_limit'=> isset($arrCamData['sanction_limit'][$key]) ? $arrCamData['sanction_limit'][$key] : null ,
                         'outstanding_exposure'=> isset($arrCamData['outstanding_exposure'][$key]) ? $arrCamData['outstanding_exposure'][$key] : null,
                         
                         'created_by'=>$userId
                     );  
                       if(isset($arrCamData['proposed_exposure'][$key])){
                          $inputArr['proposed_exposure'] = $arrCamData['proposed_exposure'][$key];
                       }
                       if(isset($arrCamData['group_company_expo_id'][$key])){
                          $group_company_expo_id = $arrCamData['group_company_expo_id'][$key];
                       }else{
                          $group_company_expo_id = null;
                       }
                      GroupCompanyExposure::updateOrcreate(['group_company_expo_id' => $group_company_expo_id], $inputArr);
                   }
               }
           }

           $whereActivi['activity_code'] = 'cam_information_save';
           $activity = $this->mstRepo->getActivity($whereActivi);
           if(!empty($activity)) {
               $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
               $activity_desc = 'Cam Inforamtion Save (Overview). AppID '. $arrCamData['app_id'];
               $arrActivity['app_id'] = $arrCamData['app_id'];
               $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrCamData), $arrActivity);
           }             
           
           $arrCamData['proposed_exposure'] = $arrCamData['proposed_exposure']['0'] ?? '';
           if($arrCamData['cam_report_id'] != ''){
                $updateCamData = Cam::updateCamData($arrCamData, $userId);
                if($updateCamData){
                       Session::flash('message',trans('CAM information updated successfully'));
                }else{
                      Session::flash('message',trans('CAM information not updated successfully'));
                }
           }else{
               $saveCamData = Cam::creates($arrCamData, $userId);
               if($saveCamData){
                       Session::flash('message',trans('CAM information saved successfully'));
                }else{
                      Session::flash('message',trans('CAM information not saved successfully'));
                }
           }    
           return redirect()->route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
       } catch (Exception $ex) {
           return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
    }

    public function showCibilForm(Request $request){
        try{
            $arrRequest['biz_id'] = $biz_id = $request->get('biz_id');
            $arrRequest['app_id'] = $app_id = $request->get('app_id');
            $arrHygieneData = CamHygiene::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
            if(!empty($arrHygieneData)){
                  $arrHygieneData['remarks'] = json_decode($arrHygieneData['remarks'], true);

            }
            $individualOwnerId = [];
            $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id);
            $arrCompanyOwnersData = BizOwner::getCompanyOwnerByBizId($biz_id);
            foreach ($arrCompanyOwnersData as $key => $value) {
              $individualOwnerId[] = $value->biz_owner_id;
            }
            $crifDataforcompanyandpromoter = [
              'app_id' => $app_id,
              'biz_id' => $biz_id,
              'commercial' => array($biz_id),
              'individual' => $individualOwnerId,
            ];
            $crifData = _encrypt(json_encode($crifDataforcompanyandpromoter));
            return view('backend.cam.cibil', compact('arrCompanyDetail', 'arrCompanyOwnersData', 'arrRequest', 'arrHygieneData', 'crifData'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


     public function saveFinanceDetail(Request $request) {
      $appId = $request->get('app_id');
      $NameOfTheBorrower = $request->get('borrower_name');
      $json_files = $this->getLatestFileName($appId,'finance', 'json');
      $active_json_filename = $json_files['curr_file'];
       if (!empty($active_json_filename) && file_exists($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)) {
            $contents = json_decode(base64_decode(file_get_contents($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)),true);
            $contents = array_replace_recursive(json_decode(base64_decode(getFinContent()),true), $contents);
        }else{
          if ($this->genBlankfinJSON) {
            $active_json_filename = $this->getCommonFilePath('common_finance.json');
            $contents = json_decode(base64_decode(file_get_contents($active_json_filename)),true);
          }
        }
        if (!empty($contents)) {
          $contents['FinancialStatement']['NameOfTheBorrower'] = $NameOfTheBorrower;
          $fy = $contents['FinancialStatement']['FY'] ?? array();
          $financeData = [];
          $curr_fin_year = ((date('m') > 3) ? date('Y') : (date('Y') - 1));
          if (!empty($fy)) {
            foreach ($fy as $k => $v) {
              if (!empty($v['year']) && $k == 0) {
                $curr_fin_year = $v['year'];
              }
              if ($this->genBlankfinJSON) {
                $v['year'] = empty($v['year']) ? $curr_fin_year : $v['year'];
                $curr_fin_year--;
              }
              $vyear = $v['year'];
              $request_year = $request->get('year');
              // dd($request_year, $vyear);
              // if(!isset($request_year[$vyear])){
              //   Session::flash('error',trans('Something went wrong with financial year'));
              //   return redirect()->back();
              // }
              if(isset($request_year[$vyear])){
                $financeData[$k] = array_replace_recursive($v, $request_year[$vyear]);
              }
            }
          }
          $financeData = arrayValuesToInt($financeData);
          $json_files = $this->getLatestFileName($appId,'finance', 'json');
          $contents['FinancialStatement']['FY'] = $financeData;
          $new_file_name = $json_files['new_file'];
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$new_file_name, base64_encode(json_encode($contents)));
        }
      try {
            $userId = Auth::user()->user_id;
            $arrData = $request->all();
            if(isset($arrData['fin_detail_id']) && $arrData['fin_detail_id']){
                  $result = AppBizFinDetail::updateHygieneData($arrData, $userId);
                  if($result){
                        Session::flash('message',trans('Finance detail updated successfully'));
                  }else{
                        Session::flash('error',trans('Finance detail not updated'));
                  }
            }else{
                $result = AppBizFinDetail::creates($arrData, $userId);
                if($result){
                        Session::flash('message',trans('Finance detail saved successfully'));
                  }else{
                        Session::flash('error',trans('Finance detail not saved'));
                  }
            }


          $whereActivi['activity_code'] = 'save_finance_detail';
          $activity = $this->mstRepo->getActivity($whereActivi);
          if(!empty($activity)) {
              $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
              $activity_desc = 'Save Finance Details (CAM). AppID '. $appId;
              $arrActivity['app_id'] = $appId;
              $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrData), $arrActivity);
          }

            return redirect()->route('cam_finance', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }



    public function reviewerSummary(Request $request){
      $offerPTPQ = '';
      $preCondArr = [];
      $postCondArr = [];
      $positiveRiskCmntArr = [];
      $negativeRiskCmntArr = [];
      $appId = $request->get('app_id');
      $bizId = $request->get('biz_id');
      $leaseOfferData = $facilityTypeList = array();
      $leaseOfferData = AppProgramOffer::getAllOffers($appId, '3');
      $termLoanOfferData = AppProgramOffer::getAllOffers($appId, '2');
      $facilityTypeList= $this->mstRepo->getFacilityTypeList()->toarray();
      $arrStaticData = array();
      $arrStaticData['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
      $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
      $arrStaticData['securityDepositType'] = array('1'=>'INR','2'=>'%');
      $arrStaticData['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
      $arrStaticData['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');
      $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$bizId)->where('app_id','=',$appId)->first();
      if(isset($limitOfferData->prgm_offer_id) && $limitOfferData->prgm_offer_id) {
        $offerPTPQ = OfferPTPQ::getOfferPTPQR($limitOfferData->prgm_offer_id);
      }
      if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
        $dataPrePostCond = CamReviewSummPrePost::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])
                        ->where('is_active', 1)->get();
        $dataPrePostCond = $dataPrePostCond ? $dataPrePostCond->toArray() : [];
        if(!empty($dataPrePostCond)) {
          $preCondArr = array_filter($dataPrePostCond, array($this, "filterPreCond"));
          $postCondArr = array_filter($dataPrePostCond, array($this, "filterPostCond"));
        }
      }
      if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
        $dataRiskComments = CamReviewSummRiskCmnt::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])
                        ->where('is_active', 1)->get();
        $dataRiskComments = $dataRiskComments ? $dataRiskComments->toArray() : [];
        if(!empty($dataRiskComments)) {
          $positiveRiskCmntArr = array_filter($dataRiskComments, array($this, "filterRiskCommentPositive"));
          $negativeRiskCmntArr = array_filter($dataRiskComments, array($this, "filterRiskCommentNegative"));
        }
      }
      $supplyOfferData = $this->appRepo->getAllOffers($appId, 1);//for supply chain
      foreach($supplyOfferData as $key=>$val){
        $supplyOfferData[$key]['anchorData'] = $this->userRepo->getAnchorDataById($val->anchor_id)->pluck('f_name')->first();
        $supplyOfferData[$key]['programData'] = $this->appRepo->getSelectedProgramData(['prgm_id' => $val->prgm_id], ['*'], ['programDoc', 'programCharges'])->first();
        $supplyOfferData[$key]['subProgramData'] = $this->appRepo->getSelectedProgramData(['prgm_id' => $val->prgm_id, 'is_null_parent_prgm_id' => true], ['*'], ['programDoc', 'programCharges'])->first();
      }

      $roleData =  Helpers::getUserRole()->first();
      $is_editable = ($roleData->id == config('common.user_role.APPROVER'))?0:1;
      return view('backend.cam.reviewer_summary', [
        'bizId' => $bizId,
        'appId'=> $appId,
        'leaseOfferData'=> $leaseOfferData,
        'termLoanOfferData'=> $termLoanOfferData,
        'reviewerSummaryData'=> $reviewerSummaryData,
        'offerPTPQ' => $offerPTPQ,
        'preCondArr' => $preCondArr,
        'postCondArr' => $postCondArr,
        'arrStaticData' => $arrStaticData,
        'facilityTypeList' => $facilityTypeList,
        'is_editable' => $is_editable,
        'supplyOfferData' => $supplyOfferData,
        'positiveRiskCmntArr' => $positiveRiskCmntArr,
        'negativeRiskCmntArr' => $negativeRiskCmntArr
      ]);
    }

    public function saveReviewerSummary(Request $request) {
      try {
        $userId = Auth::user()->user_id;
        $arrData = $request->all();
        $arrData['product_id'] = config('common.PRODUCT.LEASE_LOAN');  // For lease product
        if(isset($arrData['cam_reviewer_summary_id']) && $arrData['cam_reviewer_summary_id']){
              $result = CamReviewerSummary::updateData($arrData, $userId);
              if($result){
                    $this->savePrePostConditions($request, $arrData['cam_reviewer_summary_id']);
                    $this->saveRiskComments($request, $arrData['cam_reviewer_summary_id']);
                    Session::flash('message',trans('Reviewer Summary updated successfully'));
              }else{
                    Session::flash('message',trans('Reviewer Summary not updated'));
              }
        }else{
            $result = CamReviewerSummary::createData($arrData, $userId);
            if($result){
                    $this->savePrePostConditions($request, $result->cam_reviewer_summary_id);
                    $this->saveRiskComments($request, $result->cam_reviewer_summary_id);
                    Session::flash('message',trans('Reviewer Summary saved successfully'));
              }else{
                    Session::flash('message',trans('Reviewer Summary not saved'));
              }
        }

        $whereActivi['activity_code'] = 'save_reviewer_summary';
        $activity = $this->mstRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Reviewer Summary Save and Update (CAM). AppID '. $arrData['app_id'];
            $arrActivity['app_id'] = $arrData['app_id'];
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrData), $arrActivity);
        }

        return redirect()->route('reviewer_summary', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
      } catch (Exception $ex) {
          return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function mailReviewerSummary(Request $request) {
      if( env('SEND_MAIL_ACTIVE') == 1){
        Mail::to(explode(',', env('SEND_MAIL')))
          ->bcc(explode(',', env('SEND_MAIL_BCC')))
          ->cc(explode(',', env('SEND_MAIL_CC')))
          ->send(new ReviewerSummary($this->mstRepo));

        if(count(Mail::failures()) > 0 ) {
          Session::flash('error',trans('Mail not sent, Please try again later..'));
        } else {
          Session::flash('message',trans('Mail sent successfully.'));
        }
      } else {
        Session::flash('message',trans('Mail not sent, Please try again later.'));
      }
      return redirect()->route('reviewer_summary', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
      //return new \App\Mail\ReviewerSummary($this->mstRepo);
    }

     public function uploadFinanceXLSX(Request $request){
      $app_id = $request->get('app_id');
      $file_type = $request->get('file_type');
      $request_data = _encrypt("$app_id|$file_type");
      return view('backend.cam.upload_xlsx', ['request_data' => $request_data]);
    }

    public function uploadBankXLSX(Request $request){
      $app_id = $request->get('app_id');
      $file_type = $request->get('file_type');
      $request_data = _encrypt("$app_id|$file_type");
      return view('backend.cam.upload_xlsx', ['request_data' => $request_data]);
    }

    public function saveBankXLSX(Request $request){
      $arrFileData = $request->all();
      $request_data = $request->get('request_data');
      list($appId, $fileType) = explode('|', _decrypt($request_data));
      $fileNames = $this->getLatestFileName($appId, $fileType, 'xlsx');
      $filePath = $this->getToUploadPath($appId, $fileType);
      $reqFile = $arrFileData['doc_file']['0'];
      $fileName = $fileNames['new_file'];
      if ($reqFile->move($filePath, $fileName)) {
        return response()->json(['message' => 'File uploaded successfully','status' => 1]);
      }
       return response()->json(['message' => 'Unable to upload file','status' => 0]);
    }


    private function getRangeFromdates(array $array=[]){
       if (empty($array)) {
         return array(
          'from' => date('Y-m-01', strtotime('-6 months', strtotime(date('Y-m-01')))),
          'to' => date("Y-m-d", strtotime('Last day of Last month')),
         );
       }
       $temp=[];
       foreach ($array as $key => $value) {
        $no = preg_replace('#[^0-9]+#', '', $value);
        $temp[] = strlen($no) >= 8 ? substr($no, 0, 8) : substr($no, 0, 6).'01';
       }
       $x = str_split(min($temp), 2);
       $y = str_split(max($temp), 2);
       $min_date = $x[0].$x[1].'-'.$x[2].'-'.$x[3];
       $max_date = $y[0].$y[1].'-'.$y[2].'-'.$y[3];
      return array(
        'from' => $min_date,
        'to' => $max_date,
       );
    }

    private function _getPaginate($sheets, $curr_sheet) {
      $paginate = "";
      $total_pages = count($sheets);
      if ($total_pages <= 1) {
        return "";
      }
      $curr_page = $curr_sheet + 1;
      return getPaginate($total_pages, $curr_page, $sheets);
    }

    private function _getXLSXTable($appId, $fileType = 'finance', $sheet_no = 0){
     $objPHPExcel =  new PHPExcel();
     $files = $this->getLatestFileName($appId, $fileType, 'xlsx');
     $file_name = $files['curr_file'];
     if (empty($file_name)) {
       return ['', ''];
     }
     $inputFileName = $this->getToUploadPath($appId, $fileType).'/'.$file_name;
     if (!file_exists($inputFileName)) {
       return ['', ''];
     }
     $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
     $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
     $allsheets = $objPHPExcel->getSheetNames();
     $pagination = $this->_getPaginate($allsheets, $sheet_no);
     $objWriter->setSheetIndex($sheet_no);
     $html =  $objWriter->dump();
     return [$html, $pagination];
    }

    public function getExcelSheet(Request $request){
     $page = 0;
     if ($request->get('page') && $request->get('page') > 0) {
        $page = $_POST['page'] - 1;
      }
      $appId = $request->get('appId');
      $fileType = $request->get('fileType');
      $sheet_data = $this->_getXLSXTable($appId, $fileType, $page);
      $response['data'] =  $sheet_data[0];
      $response['paginate'] = $sheet_data[1];
      return response()->json(['response' => $response,'status' => 1]);
    }

    public function getLatestFileName($appId, $fileType='banking', $extType='json'){
      $scanpath = $this->getToUploadPath($appId, $fileType);
      if (is_dir($scanpath) == false) {
        $files = [];
      }else{
        $files = scandir($scanpath, SCANDIR_SORT_DESCENDING);
      }
      $files = array_diff($files, [".", ".."]);
      natsort($files);
      $files = array_reverse($files, false);
      $filename = "";
      if (!empty($files)) {
        foreach ($files as $key => $file) {
          $fileparts = pathinfo($file);
          $filename = $fileparts['filename'];
          $ext = $fileparts['extension'];
          if ($extType == $ext) {
             break;
          }
        }
      }

      $included_no = preg_replace('#[^0-9]+#', '', $filename);
      $file_no = substr($included_no, strlen($appId));
      if (empty($file_no) && empty($filename)) {
        $new_file = $appId.'_'.$fileType.".$extType";
        $curr_file = '';
      }else{
        $file_no = (int)$file_no + 1;
        $curr_file = $filename.".$extType";
        $new_file = $appId.'_'.$fileType.'_'.$file_no . ".$extType";
      }
      $fileArr = array(
        'curr_file' => $curr_file,
        'new_file' => $new_file,
      );
      return $fileArr;
    }


    private function getToUploadPath($appId, $type = 'banking'){
      $touploadpath = storage_path('app/public/user/docs/'.$appId);
      if(!Storage::exists('public/user/docs/' .$appId)) {
          Storage::makeDirectory('public/user/docs/' .$appId.'/banking', 0777, true);
          Storage::makeDirectory('public/user/docs/' .$appId.'/finance', 0777, true);
          $touploadpath = storage_path('public/user/docs/' .$appId);
      }
      return $touploadpath .= ($type == 'banking' ? '/banking' : '/finance');
    }

    private function getCommonFilePath($filenameorpath = ''){
      $extrapath = trim($filenameorpath, '/');
      return storage_path('app/public/user/').$extrapath;
    }

   public function finance(Request $request, FinanceModel $fin){
        $appId = $request->get('app_id');
        $xlsx_arr = $this->_getXLSXTable($appId,'finance');
        $xlsx_html = $xlsx_arr[0];
        $xlsx_pagination = $xlsx_arr[1];
        $json_files = $this->getLatestFileName($appId,'finance', 'json');
        $active_json_filename = $json_files['curr_file'];
        $xlsx_files = $this->getLatestFileName($appId,'finance', 'xlsx');
        $active_xlsx_filename = $xlsx_files['curr_file'];
        $bizId = $request->get('biz_id');
        $pending_rec = $fin->getPendingFinanceStatement($appId);
        $perfiosLogId = $pending_rec->perfios_log_id ?? NULL;
        $callBackMessage = '';
        if (isset($perfiosLogId)) {
          $callbackResp = $fin->getCallBackResponse($perfiosLogId);
          if (!empty($callbackResp) && $callbackResp->req_file == '' && $callbackResp->url == '') {
            $callBackMessage = base64_decode($callbackResp->res_file);
          }
        }
        $financedocs = $fin->getFinanceStatements($appId);
        $contents = array();
        if (!empty($active_json_filename) && file_exists($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)) {
          $contents = json_decode(base64_decode(file_get_contents($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)),true);
          $contents = array_replace_recursive(json_decode(base64_decode(getFinContent()),true) , $contents);
        }else{
          if ($this->genBlankfinJSON) {
            $active_json_filename = $this->getCommonFilePath('common_finance.json');
            if (!file_exists($active_json_filename)) {
              $myfile = fopen($active_json_filename, "w");
              \File::put($active_json_filename, getFinContent());
            }
            $contents = json_decode(base64_decode(file_get_contents($active_json_filename)),true);
          }
        }

        $borrower_name = $contents['FinancialStatement']['NameOfTheBorrower'] ?? '';
        $latest_finance_year = 2010;
        $fy = $contents['FinancialStatement']['FY'] ?? array();
        $financeData = [];
        $audited_years = [];
        $curr_fin_year = ((date('m') > 3) ? date('Y') : (date('Y') - 1));
        if (!empty($fy)) {
          foreach ($fy as $k => $v) {
            if ($this->genBlankfinJSON) {
              $v['year'] = empty($v['year']) ? $curr_fin_year : $v['year'];
              $curr_fin_year--;
            }
            $audited_years[] = $v['year'];
            $latest_finance_year = $latest_finance_year < $v['year'] ? $v['year'] : $latest_finance_year;
            $financeData[$v['year']] = $v;
          }
        }
        $financeData =  arrayValuesToInt($financeData);
        $growth_data = [];
        foreach ($audited_years as $Kolkata => $year) {
          if (!empty($financeData[$year-1])) {
             $growth_data[$year] =  getGrowth($financeData[$year], $financeData[$year-1]);
          }else{
             $growth_data[$year] = 0;
          }
        }
        $finDetailData = AppBizFinDetail::where('biz_id','=',$bizId)->where('app_id','=',$appId)->first();
        return view('backend.cam.finance', [
          'financedocs' => $financedocs,
          'appId'=> $appId,
          'pending_rec'=> $pending_rec,
          'callBackMessage'=> $callBackMessage,
          'borrower_name'=> $borrower_name,
          'audited_years'=> $audited_years,
          'finance_data'=> $financeData,
          'growth_data'=> $growth_data,
          'latest_finance_year'=> $latest_finance_year,
          'finDetailData'=>$finDetailData,
          'active_xlsx_filename'=> $active_xlsx_filename,
          'active_json_filename'=> $active_json_filename,
          'xlsx_html'=> $xlsx_html,
          'xlsx_pagination'=> $xlsx_pagination,
        ]);

    }

    public function banking(Request $request, FinanceModel $fin){
        $appId = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $xlsx_arr = $this->_getXLSXTable($appId,'banking');
        $xlsx_html = $xlsx_arr[0];
        $xlsx_pagination = $xlsx_arr[1];
        $json_files = $this->getLatestFileName($appId,'banking', 'json');
        $active_json_filename = $json_files['curr_file'];
        $xlsx_files = $this->getLatestFileName($appId,'banking', 'xlsx');
        $active_xlsx_filename = $xlsx_files['curr_file'];
        $pending_rec = $fin->getPendingBankStatement($appId);
        $bankdocs = $fin->getBankStatements($appId);
        $debtPosition = $fin->getDebtPosition($appId);
        $dataWcf = [];
        $dataTlbl = [];
        $dataBankAna = [];
        if(isset($debtPosition['bank_detail_id'])) {
          $dataWcf = $this->financeRepo->getBankWcFacility($debtPosition['bank_detail_id']);
          $dataWcf = $dataWcf ? $dataWcf->toArray() : [];
          $dataTlbl = $this->financeRepo->getBankTermBusiLoan($debtPosition['bank_detail_id']);
          $dataTlbl = $dataTlbl ? $dataTlbl->toArray() : [];
          $dataBankAna = $this->financeRepo->getBankAnalysis($debtPosition['bank_detail_id']);
          $dataBankAna = $dataBankAna ? $dataBankAna->toArray() : [];
        }
        $contents = array();
        if (!empty($active_json_filename) && file_exists($this->getToUploadPath($appId, 'banking').'/'. $active_json_filename)) {
          $contents = json_decode(base64_decode(file_get_contents($this->getToUploadPath($appId, 'banking').'/'.$active_json_filename)),true);
        }

        $customers_info = [];
        if (!empty($contents)) {
          foreach ($contents['statementdetails'] as $key => $value) {
            $account_no = $contents['accountXns'][0]['accountNo'] ?? NULL;
            $customer_data = $value['customerInfo'] ?? [];
            $customers_info[] = array(
              'name' => $customer_data['name'] ?? NULL,
              'email' => $customer_data['email'] ?? NULL,
              'mobile' => $customer_data['mobile'] ?? NULL,
              'account_no' => $account_no,
              'bank' => $customer_data['bank'] ?? NULL,
              'pan' => $customer_data['pan'] ?? NULL,
            );
          }
        }
        return view('backend.cam.bank', [
          'bankdocs' => $bankdocs,
          'appId'=> $appId,
          'biz_id'=> $biz_id,
          'pending_rec'=> $pending_rec,
          'bank_data'=> $contents,
          'customers_info'=> $customers_info,
          'active_xlsx_filename'=> $active_xlsx_filename,
          'active_json_filename'=> $active_json_filename,
          'xlsx_html'=> $xlsx_html,
          'xlsx_pagination'=> $xlsx_pagination,
          'debtPosition'=> $debtPosition,
          'dataWcf'=> $dataWcf,
          'dataTlbl'=> $dataTlbl,
          'dataBankAna'=> $dataBankAna
          ]);
    }

    public function finance_store(FinanceRequest $request, FinanceModel $fin){
        $financeid = _getRand();
        $insert_data = [];
        $post_data = $request->all();
        unset($post_data['_token']);
        $curr = date('Y');
        foreach ($post_data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                   $insert_data[$curr- 2 + $k][$key]= $v;
                }
            }else{
                $insert_data[$curr-2][$key]= $value;
               $insert_data[$curr-1][$key]= $value;
               $insert_data[$curr][$key]= $value;
            }
        }
        $insert_data[$curr-2]['finance_id'] = $financeid;
        $insert_data[$curr-2]['period_ended'] = date($curr-2 . '-03-31');
        $insert_data[$curr-1]['finance_id'] = $financeid;
        $insert_data[$curr-1]['period_ended'] = date($curr-1 . '-03-31');
        $insert_data[$curr]['finance_id'] = $financeid;
        $insert_data[$curr]['period_ended'] = date($curr . '-03-31');

        foreach ($insert_data as  $ins_arr) {
            $fin->create($ins_arr);
        }
        Session::flash('message',trans('Record Inserted Successfully'));
        return redirect()->route('cam_finance');
    }

    public function updateBankDocument(Request $request){
      $perfios_txn_id = $request->session()->get('perfios_txn_id');
      $allData = $request->all();
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $doc_id = $request->get('doc_id');
      $app_doc_file_id = $request->get('app_doc_file_id');
      $fin = new FinanceModel;
      $bank_doc_data = $fin->getSingleBankStatement($appId, $app_doc_file_id);
      $bankdata = $fin->getBankData();
      $req_data = _encrypt("$appId|$doc_id|$biz_id|$app_doc_file_id|$perfios_txn_id");
      return view('backend.cam.upload_bank',compact('bank_doc_data', 'req_data','bankdata','perfios_txn_id'));
    }

    public function saveBankDocument(Request $request){
        $arrFileData = $request->all();
        $perfios_txn_id = $request->session()->get('perfios_txn_id');
        if (empty($perfios_txn_id)) {
          return response()->json(['message' => 'You can\'t re-process without analyse the files.','status' => 0]);
        }
        $userId = Auth::user()->user_id;
        $doc_id = $request->get('doc_id');
        $biz_id = $request->get('biz_id');
        $appId = $request->get('app_id');
        $app_doc_file_id = $request->get('app_doc_file_id');
        $req_data = $request->get('req_data');
        $encrypted = "$appId|$doc_id|$biz_id|$app_doc_file_id|$perfios_txn_id";
        if ($encrypted != _decrypt($req_data)) {
         return response()->json(['message' => 'Requested Data is not valid','status' => 0]);
        }
        if (empty($arrFileData['doc_file']) && !empty($arrFileData['reupload'])) {
         return response()->json(['message' => 'File is required to Re-upload','status' => 0]);
        }
        $fin = new FinanceModel;
        $appUser = $fin->getUserByAPP($appId);
        $appUserId = $appUser['user_id'];
        $file_bank_id = $arrFileData['file_bank_id'];
        $bankData = $fin->getBankDetail($file_bank_id);
        $arrFileData['doc_name'] = $bankData['bank_name'] ?? NULL;
        $arrFileData['finc_year'] = NULL;
        $arrFileData['gst_month'] = $arrFileData['bank_month'];
        $arrFileData['gst_year'] = $arrFileData['bank_year'];
        $arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
        if (empty($arrFileData['doc_file']) && !empty($arrFileData['reprocess'])) {
          $bank_errors = $request->session()->get('bank_errors');
          if (empty($bank_errors[$app_doc_file_id])) {
            return response()->json(['message' => 'You can\'t re-process without error on this file.','status' => 0]);
          }
          $fileid = $bank_errors[$app_doc_file_id]['fileid'];
          $arrayToUpdate = array(
            "is_pwd_protected" => $arrFileData['is_pwd_protected'],
            "is_scanned" => $arrFileData['is_scanned'],
            "pwd_txt" => $arrFileData['pwd_txt'],
            "doc_name" => $arrFileData['doc_name'],
            "file_bank_id" => $arrFileData['file_bank_id'],
            "finc_year" => NULL,
            "gst_month" => $arrFileData['gst_month'],
            "gst_year" => $arrFileData['gst_year'],
          );
          $is_updated = $this->docRepo->UpdateAppDocument($arrayToUpdate, $app_doc_file_id);
          if ($is_updated) {
             $reprocess_data = array(
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
              'app_id' => $appId,
              'app_doc_file_id' => $app_doc_file_id,
              'fileid' => $fileid,
            );
            return $this->_reProcessStmt($reprocess_data);
          }
        }else{
          $arrFile['doc_file'] = $arrFileData['doc_file']['0'];
          $uploadData = Helpers::uploadAppFile($arrFile, $appId);
          $userFile = $this->docRepo->saveFile($uploadData);
          $appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
          $appDocResponse = $this->docRepo->saveAppDoc($appDocData);
          $is_deleted = $this->docRepo->deleteDocument($app_doc_file_id);
          $app_doc_file_id = $appDocResponse['app_doc_file_id'];
          if ($app_doc_file_id && $is_deleted) {
            $reupload_data = array(
              'app_id' => $appId,
              'app_doc_file_id' => $app_doc_file_id,
              'perfiostransactionid' => $perfios_txn_id,
              'prolitus_txn_id' => $request->session()->get('prolitus_txn_id'),
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
            );
            return $this->_reUploadBankStmt($reupload_data);
          }else{
            return response()->json(['message' => 'Unable to Re-Upload the statement.','status' => 0]);
          }
        }
    }


    private function _reUploadBankStmt($reupload_data){
       $app_id = $reupload_data['app_id'];
       $doc_id = $reupload_data['doc_id'];
       $biz_id = $reupload_data['biz_id'];
       $app_doc_file_id = $reupload_data['app_doc_file_id'];
       $perfiostransactionid = $reupload_data['perfiostransactionid'];
       $prolitus_txn = $reupload_data['prolitus_txn_id'];
       $fin = new FinanceModel();
       $file_doc = $fin->getSingleBankStatement($app_id, $app_doc_file_id);
       $bank_detail = $fin->getBankDetail($file_doc->file_bank_id);
       $perfios_bank_id = $bank_detail->perfios_bank_id ?? NULL;
       $filepath = $file_doc['file_path'];
       $app_doc_file_id = $file_doc['app_doc_file_id'];
       $password = $file_doc['file_password'];
        $req_arr = array(
          'perfiosTransactionId' => $perfiostransactionid,
          'file_content' => public_path('storage/'.$filepath),
         );
        $bsa = new Bsa_lib();
        $upl_file = $bsa->api_call(Bsa_lib::UPL_FILE, $req_arr);
        if ($upl_file['status'] == 'success') {
            $reprocess_data = array(
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
              'app_id' => $app_id,
              'app_doc_file_id' => $app_doc_file_id,
              'fileid' => $upl_file['fileid']
            );
            return $this->_reProcessStmt($reprocess_data);
        }else{
           return response()->json(['message' => $proc_txn['message'],'status' => 0]);
        }
        return response()->json(['message' => 'Unable to upload the file.','status' => 1]);
    }


    private function _reProcessStmt($reprocess_data){
        $perfios_txn_id = Session::get('perfios_txn_id');
        if (empty($perfios_txn_id)) {
          return response()->json(['message' => 'You can\'t re-process without analyse the files.','status' => 0]);
        }
        $userId = Auth::user()->user_id;
        $doc_id = $reprocess_data['doc_id'];
        $biz_id = $reprocess_data['biz_id'];
        $app_id = $reprocess_data['app_id'];
        $app_doc_file_id = $reprocess_data['app_doc_file_id'];

       $fin = new FinanceModel();
       $file_doc = $fin->getSingleBankStatement($app_id, $app_doc_file_id);
       $bank_detail = $fin->getBankDetail($file_doc->file_bank_id);
       $perfios_bank_id = $bank_detail->perfios_bank_id ?? NULL;
       $filepath = $file_doc['file_path'];
       $password = $file_doc['file_password'];
       $prolitus_txn = Session::get('prolitus_txn_id');
        $req_arr = array(
          'perfiosTransactionId' => $perfios_txn_id,
          'fileId' => $reprocess_data['fileid'],
          'institutionId' => $perfios_bank_id,
          'password' => $password,
        );
        $bsa = new Bsa_lib();
        $proc_txn = $bsa->api_call(Bsa_lib::REPRC_STMT, $req_arr);
        if ($proc_txn['status'] == 'success') {
             return response()->json(['message' => "File processed Successfully",'status' => 1, 'biz_perfios_id' => $perfios_txn_id]);
        }else{
            return response()->json(['message' => $proc_txn['message'],'status' => 0]);
        }
    }



    public function _uploadFiles($path, $reqFiles){
      $inputArr = [];
      foreach ($reqFiles as $key => $reqFile) {
        $fileName = $reqFile->hashName();
        $filePath = storage_path("app/public/") . $path;
        $fullPath = $filePath . $fileName;
        $inputArr[$key]['file_path'] = $path . $fileName;
        $inputArr[$key]['file_type'] = $reqFile->getClientMimeType();
        $inputArr[$key]['file_name'] = $reqFile->getClientOriginalName();
        $inputArr[$key]['file_size'] = $reqFile->getClientSize();
        $inputArr[$key]['file_encp_key'] =  md5($fullPath);
        $inputArr[$key]['created_by'] = 1;
        $inputArr[$key]['updated_by'] = 1;
        $reqFile->move($filePath, $fileName);
      }
        return $inputArr[0] ?? $inputArr;
    }

    public function analyse_bank(Request $request) {
      $post_data = $request->all();
      $appId = $request->get('appId');
      $filepath = $this->getBankFilePath($appId);
      $response = $this->_callBankApi($filepath, $appId);
      $response['result'] = base64_encode($response['result'] ?? '');
      if (empty($response['perfiosTransactionId']) && empty($response['perfiostransactionid'])) {
         $response['perfiosTransactionId'] = NULL;
      }else{
          $response['perfiosTransactionId'] = $response['perfiosTransactionId'] ?? $response['perfiostransactionid'];
      }
      if (!empty($response['perfiosTransactionId'])) {
         $request->session()->put('perfios_txn_id', $response['perfiosTransactionId']);
         $request->session()->put('prolitus_txn_id', $response['prolitusTransactionId']);
      }

      $log_data = array(
        'app_id' =>  $appId,
        'status' => $response['status'],
        'type' => '1',
        'api_name' => $response['api_type'] ?? NULL,
        'prolitus_txn_id' => $response['prolitusTransactionId'] ?? NULL,
        'perfios_log_id' => $response['perfiosTransactionId'],
        'created_by' => Auth::user()->user_id,
      );
      FinanceModel::insertPerfios($log_data);
      $errors = [];
      if (!empty($response['errors'])) {
        $request->session()->put('bank_errors', $response['errors']);
        foreach ($response['errors'] as $app_doc_file_id => $perfios_err) {
         $errors[$app_doc_file_id] = $perfios_err['message'];
        }
      }
      if ($response['status'] == 'success') {
        return response()->json(['message' =>'Bank Statement analysed successfully.','status' => 1,
          'value' => $response]);
      }else{
        return response()->json(['message' =>$response['message'] ?? 'Something went wrong','errors' => $errors,'perfios_log_id' => $response['perfiosTransactionId'],'status' => 0,'value'=>['file_url'=>'']]);
      }
    }

    public function analyse_finance(Request $request) {
      $post_data = $request->all();
      $appId = $request->get('appId');
      $filepath = $this->getFinanceFilePath($appId);
      $response = $this->_callFinanceApi($filepath, $appId);
      $response['result'] = base64_encode($response['result'] ?? '');
      if (empty($response['perfiosTransactionId']) && empty($response['perfiostransactionid'])) {
         $response['perfiosTransactionId'] = NULL;
      }else{
          $response['perfiosTransactionId'] = $response['perfiosTransactionId'] ?? $response['perfiostransactionid'];
      }

      $log_data = array(
        'app_id' =>  $appId,
        'status' => $response['status'],
        'type' => '2',
        'api_name' => $response['api_type'] ?? NULL,
        'prolitus_txn_id' => $response['prolitusTransactionId'] ?? NULL,
        'perfios_log_id' => $response['perfiosTransactionId'],
        'created_by' => Auth::user()->user_id,
      );
      FinanceModel::insertPerfios($log_data);
      if ($response['status'] == 'success') {
        return response()->json(['message' =>'Financial Statement analysed successfully.','status' => 1,
          'value' => $response]);
      }else{
        return response()->json(['message' =>$response['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
      }
    }


    public function getFinanceFilePath($appId){
        $fin = new FinanceModel();
        $financedocs = $fin->getFinanceStatements($appId);
        $files = [];
        $dates = [];
        foreach ($financedocs as $doc) {
          $files[] = array(
            'app_doc_file_id' => $doc->app_doc_file_id,
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
           if (!empty($doc->gst_year) && !empty($doc->gst_month)) {
              $dates[] = $doc->gst_year .'-'.sprintf('%02d',$doc->gst_month);
           }
        }
        $files[] = $dates;
        return $files;
    }

    private function getBankFilePath($appId) {
        $fin = new FinanceModel();
        $bankdocs = $fin->getBankStatements($appId);
        $files = [];
        $dates = [];
        foreach ($bankdocs as $doc) {
           $bank_detail = $fin->getBankDetail($doc->file_bank_id);
           $files[] = array(
            'app_doc_file_id' => $doc->app_doc_file_id,
            'facility' => $doc->facility,
            'sanctionlimitfixed' => $doc->sanctionlimitfixed,
            'drawingpowervariableamount' => $doc->drawingpowervariableamount,
            'sanctionlimitvariableamount' => $doc->sanctionlimitvariableamount,
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'institutionId' => $bank_detail->perfios_bank_id ?? NULL,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
           if (!empty($doc->gst_year) && !empty($doc->gst_month)) {
              $dates[] = $doc->gst_year .'-'. sprintf('%02d',$doc->gst_month);
           }
        }
        $files[] = $dates;
        return $files;
    }

    private function _callBankApi($filespath, $appId){
        $userLoan = FinanceModel::getLoanByAPP($appId);
        $loanAmount = (int)$userLoan['loan_amount'];
        $dates = array_pop($filespath);
        $ranges = $this->getRangeFromdates($dates);
        $bsa = new Bsa_lib();
        $reportType = 'json';
        $prolitus_txn = _getRand(18);
        $process_txn_cnt = 0;
        $req_arr = array(
            'txnId' => $prolitus_txn, //'bharatSTmt',
            'loanAmount' => $loanAmount,
            'loanDuration' => '12',
            'loanType' => 'SME Loan',
            'processingType' => 'STATEMENT',
            'facility' => 'NONE',
            'sanctionLimitFixed' => 'false',
            'acceptancePolicy' => 'atLeastOneTransactionInRange',
            'yearMonthFrom' => date('Y-m',strtotime($ranges['from'])),
            'yearMonthTo' => date('Y-m',strtotime($ranges['to'])),
            'transactionCompleteCallbackUrl' => route('api_perfios_bsa_callback'),
         );
        foreach ($filespath as $bankdocs) {
          $req_arr['drawingpowervariableamount'][] = $bankdocs['drawingpowervariableamount'];
          $req_arr['sanctionlimitvariableamount'][] = $bankdocs['sanctionlimitvariableamount'];


          if ($loanAmount < $bankdocs['drawingpowervariableamount'] || $loanAmount < $bankdocs['sanctionlimitvariableamount']) {
            return ["status" => "fail","message" => "Loan amount can not be less than drawingpowervariableamount or sanctionlimitvariableamount","api_type"=>Bsa_lib::INIT_TXN];
          }
        }
        $init_txn = $bsa->api_call(Bsa_lib::INIT_TXN, $req_arr);
        if ($init_txn['status'] == 'success') {
          foreach ($filespath as $file_doc) {
             $filepath = $file_doc['file_path'];
             $app_doc_file_id = $file_doc['app_doc_file_id'];
             $password = $file_doc['file_password'];
             $institutionId = $file_doc['institutionId'];
              $req_arr = array(
                'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                'file_content' => $filepath,
               );
              $upl_file = $bsa->api_call(Bsa_lib::UPL_FILE, $req_arr);
              if ($upl_file['status'] == 'success') {
                  $req_arr = array(
                    'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                    'fileId' => $upl_file['fileid'],
                    'institutionId' => $institutionId,
                    'password' => $password,
                  );
                  $proc_txn = $bsa->api_call(Bsa_lib::PRC_STMT, $req_arr);
                  if ($proc_txn['status'] == 'success') {
                      $process_txn_cnt++;
                  }else{
                      $error[$app_doc_file_id] = $proc_txn;
                      $error[$app_doc_file_id]['fileid'] = $upl_file['fileid'];
                      $error[$app_doc_file_id]['api_type'] = Bsa_lib::PRC_STMT;
                  }
              }else{
                $error[$app_doc_file_id] = $upl_file;
                $error[$app_doc_file_id]['fileid'] = $upl_file['fileid'];
                $error[$app_doc_file_id]['api_type'] = Bsa_lib::UPL_FILE;
              }
          }
          if ($process_txn_cnt == count($filespath)) {
             $req_arr = array(
                'perfiosTransactionId' => $init_txn['perfiostransactionid'],
             );
             $rep_gen = $bsa->api_call(Bsa_lib::REP_GEN, $req_arr);
             if ($rep_gen['status'] == 'success') {
                $rep_gen['prolitusTransactionId'] = $prolitus_txn;
                $rep_gen['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res = $rep_gen;
                $final_res['api_type'] = Bsa_lib::REP_GEN;
             }else{
                $final_res = $rep_gen;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res['api_type'] = Bsa_lib::REP_GEN;
             }
          }else{
                $final_res['errors'] = $error;
                $final_res['status'] = 'fail';
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res['api_type'] = Bsa_lib::PRC_STMT;
          }
        }else{
            $final_res = $init_txn;
            $final_res['api_type'] = Bsa_lib::INIT_TXN;
        }
        if ($final_res['status'] != 'success') {
            return $final_res;
        }

        if (!empty($is_scanned) && strtolower($is_scanned) == '1') {
           $final_res['api_type'] = Bsa_lib::REP_GEN;
           return $final_res;
        }



        $nameArr = $this->getLatestFileName($appId, 'banking', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
          'perfiosTransactionId' => $init_txn['perfiostransactionid'],
          'types' => 'xlsx',
        );
        if ($this->download_xlsx) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen($this->getToUploadPath($appId,'banking') .'/'.$file_name, "w");
          \File::put($this->getToUploadPath($appId,'banking') .'/'.$file_name, $final_res['result']);
        }
        $file= url("storage/user/docs/$appId/banking/". $file_name);
        $req_arr['types'] =  $reportType;
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'banking', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$init_txn['perfiostransactionid'],'biz_perfios_id');
        }
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
        return $final_res;
    }

    public function _callFinanceApi($filespathwithYear, $appId) {
        $userLoan = FinanceModel::getLoanByAPP($appId);
        $loanAmount = (int)$userLoan['loan_amount'];
        $dates = array_pop($filespathwithYear);
        $perfios = new Perfios_lib();
        $reportType = 'json';
        $prolitus_txn = _getRand(18);
        $process_txn_cnt = 0;
        $apiVersion = '2.1';
        $vendorId = 'capsave';

        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'txnId' => $prolitus_txn,
            'institutionId' => '10996',
            'loanAmount' => $loanAmount,
            'loanDuration' => '24',
            'loanType' => 'Home',
            'transactionCompleteCallbackUrl' => route('api_perfios_fsa_callback'),
        );
        $filespath = [];
        $filesCount = 0;
        foreach ($filespathwithYear as $key => $value) {
          $filespath[$value['fin_year']][] = $value;
        }
        $start_txn = $perfios->api_call(Perfios_lib::STRT_TXN, $req_arr);
         if ($start_txn['status'] == 'success') {
          foreach ($filespath as $fin_year => $file_documents) {
            $financial_year = substr($fin_year, -4);
            $req_arr = array(
                  'apiVersion' => $apiVersion,
                  'vendorId' => $vendorId,
                  'perfiosTransactionId' => $start_txn['perfiostransactionid'],
                  'financialYear' => $financial_year,
               );
              $add_year = $perfios->api_call(Perfios_lib::ADD_YEAR, $req_arr);
              if ($add_year['status'] == 'success') {
                foreach ($file_documents as  $file_doc) {
                  $filepath = $file_doc['file_path'];
                  $file_password = $file_doc['file_password'];
                  $req_arr = array(
                    'file_content' => $filepath,
                    'file_password' => $file_password,
                    'perfiosTransactionId' => $start_txn['perfiostransactionid'],
                    'financialYear' => $financial_year,
                  );
                  $upl_stmt = $perfios->api_call(Perfios_lib::UPL_STMT, $req_arr);
                  if ($upl_stmt['status'] == 'success') {
                       $process_txn_cnt++;
                  }else{
                      $upl_stmt_error = $upl_stmt;
                      $upl_stmt_error['prolitusTransactionId'] = $prolitus_txn;
                      $upl_stmt_error['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                      $upl_stmt_error['api_type'] = Perfios_lib::UPL_STMT;
                  }
                  $filesCount++;
                }
              }else{
                $add_year_error = $add_year;
                $add_year_error['prolitusTransactionId'] = $prolitus_txn;
                $add_year_error['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $add_year_error['api_type'] = Perfios_lib::ADD_YEAR;
              }
          }
          if ($process_txn_cnt == $filesCount) {
             $req_arr = array(
                'apiVersion' => $apiVersion,
                'vendorId' => $vendorId,
                'perfiosTransactionId' => $start_txn['perfiostransactionid'],
             );
             $cmplt_txn = $perfios->api_call(Perfios_lib::CMPLT_TXN, $req_arr);
             if ($cmplt_txn['status'] == 'success') {
                $final_res = $cmplt_txn;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
              }else{
                $final_res = $cmplt_txn;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
              }
          }else{
            $final_res = $add_year_error ?? $upl_stmt_error;
            $final_res['prolitusTransactionId'] = $prolitus_txn;
            $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
            $final_res['api_type'] = !empty($add_year_error) ? Perfios_lib::ADD_YEAR : Perfios_lib::UPL_STMT;
          }
         }else{
             $final_res = $start_txn;
             $final_res['api_type'] = Perfios_lib::STRT_TXN;
         }


        if ($final_res['status'] != 'success') {
            return $final_res;
        }

        if (!empty($is_scanned) && strtolower($is_scanned) == 'yes') {
           $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
           return $final_res;
        }

        $nameArr = $this->getLatestFileName($appId, 'finance', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'perfiosTransactionId' => $start_txn['perfiostransactionid'],
            'reportType' => 'xlsx',
            'txnId' => $prolitus_txn,
        );
        if ($this->download_xlsx) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$file_name, $final_res['result']);
        }
        $file= url("storage/user/docs/$appId/finance/". $file_name);
        $req_arr['reportType'] = $reportType;
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);

          $nameArr = $this->getLatestFileName($appId, 'finance', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$start_txn['perfiostransactionid'],'biz_perfios_id');
        }
        $final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
        return $final_res;
    }

    public function getFinanceReport(Request $request) {
        $alldata = $request->all();
        $appId = $alldata['appId'];
        $biz_perfios_id = $alldata['biz_perfios_id'];
        $perfios_data = FinanceModel::getPerfiosData($biz_perfios_id);
        if ($perfios_data['app_id'] != $appId) {
          return response()->json(['message' => 'This application is not belonging to you.','status' => 0,'value'=>['file_url'=>'']]);
        }

        $perfiostransactionid = $perfios_data['perfios_log_id'];
        $prolitus_txn = $perfios_data['prolitus_txn_id'];

        $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'finance', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
              'apiVersion' => $apiVersion,
              'vendorId' => $vendorId,
              'perfiosTransactionId' => $perfiostransactionid,
              'reportType' => 'xlsx',
              'txnId' => $prolitus_txn,
        );
        if ($this->download_xlsx) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
          }else{
            $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$file_name, "w");
            \File::put($this->getToUploadPath($appId, 'finance') .'/'.$file_name, $final_res['result']);
          }
        }
        $file= url("storage/user/docs/$appId/finance/". $file_name);
        $req_arr['reportType'] = 'json';
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
        $final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'finance', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        if ($final_res['status'] == 'success') {
          return response()->json(['message' =>'Financial Statement analysed successfully.','status' => 1,
            'value' => $final_res]);
        }else{
          return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
        }
    }


    public function getBankReport(Request $request) {
        $alldata = $request->all();
        $appId = $alldata['appId'];
        $biz_perfios_id = $alldata['biz_perfios_id'];
        $perfios_data = FinanceModel::getPerfiosData($biz_perfios_id);
        if ($perfios_data['app_id'] != $appId) {
          return response()->json(['message' => 'This application is not belonging to you.','status' => 0,'value'=>['file_url'=>'']]);
        }
        $perfiostransactionid = $perfios_data['perfios_log_id'];
        $prolitus_txn = $perfios_data['prolitus_txn_id'];
        $bsa = new Bsa_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'banking', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
          'perfiosTransactionId' => $perfiostransactionid,
          'types' => 'xlsx',
        );
        if ($this->download_xlsx) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
               $log_data = array(
                'app_id' =>  $appId,
                'status' => $final_res['status'],
                'type' => '1',
                'api_name' => Bsa_lib::GET_REP,
                'prolitus_txn_id' => $prolitus_txn ?? NULL,
                'perfios_log_id' => $perfiostransactionid,
                'created_by' => Auth::user()->user_id,
              );
              FinanceModel::insertPerfios($log_data);
              return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
          }else{
             $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$file_name, "w");
             \File::put($this->getToUploadPath($appId, 'finance') .'/'.$file_name, $final_res['result']);
          }
        }
        $file= url("storage/user/docs/$appId/banking/". $file_name);
        $req_arr['types'] = 'json';
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'banking', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }

        if ($final_res['status'] == 'success') {
          return response()->json(['message' =>'Banking Statement analysed successfully.','status' => 1,
            'value' => $final_res]);
        }else{
          return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
        }
    }

    /**
     * Show Limit Assessment
     *
     * @param Request $request
     * @return view
     */
    public function showLimitAssessment(Request $request)
    {
        $appId = (int)$request->get('app_id');
        $bizId = $request->get('biz_id');

        $supplyPrgmLimitData = $this->appRepo->getProgramLimitData($appId, 1);
        $termPrgmLimitData = $this->appRepo->getProgramLimitData($appId, 2);
        $leasingPrgmLimitData = $this->appRepo->getProgramLimitData($appId, 3);
        $limitData = $this->appRepo->getAppLimit($appId);
        $prgmLimitTotal = $this->appRepo->getTotalPrgmLimitByAppId($appId);
        $tot_offered_limit = $this->appRepo->getTotalOfferedLimit($appId);

        $product_types = $this->mstRepo->getProductDataList();

        //$offerStatus = $this->appRepo->getOfferStatus(['app_id' => $appId, 'is_approve'=>1, 'is_active'=>1, 'status'=>1]);//to check the offer status
        $offerStatus = $this->appRepo->getAppData($appId)->status;//to check offer is sanctioned or not

        $approveStatus = $this->appRepo->getApproverStatus(['app_id'=>$appId, 'approver_user_id'=>Auth::user()->user_id, 'is_active'=>1]);
        $currStage = Helpers::getCurrentWfStage($appId);
        $currStageCode = isset($currStage->stage_code)? $currStage->stage_code: '';
        $userRole = $this->userRepo->getBackendUser(Auth::user()->user_id);
        $appData = $this->appRepo->getAppData($appId);
        $appType = $appData->app_type;

        return view('backend.cam.limit_assessment')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('limitData', $limitData)
                ->with('totOfferedLimit', $tot_offered_limit)
                ->with('prgmLimitTotal', $prgmLimitTotal)
                ->with('approveStatus', $approveStatus)
                ->with('supplyPrgmLimitData', $supplyPrgmLimitData)
                ->with('termPrgmLimitData', $termPrgmLimitData)
                ->with('leasingPrgmLimitData', $leasingPrgmLimitData)
                ->with('currStageCode', $currStageCode)
                ->with('offerStatus', $offerStatus)
                ->with('userRole', $userRole)
                ->with('product_types', $product_types)
                ->with('appType', $appType);
    }

    /**
     * Save Limit Assessment
     *
     * @param Request $request
     * @return view
     */
    public function saveLimitAssessment(Request $request)
    {
        try {
            $appId = (int)$request->get('app_id');
            $bizId = $request->get('biz_id');

            $checkProgram = $this->appRepo->checkduplicateProgram([
              'app_id'=>$appId,
              'product_id'=>$request->product_id
              ]);

             $checkApprovalStatus = $this->appRepo->getAppApprovers($appId);

            if($checkProgram->count()){
              Session::flash('message',trans('backend_messages.already_exist'));
              return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            }elseif($checkApprovalStatus->count()){
              Session::flash('message', trans('backend_messages.under_approval'));
              return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            }

            //Validate Enchancement Limit
            $totLimitAmt = str_replace(',', '', $request->get('tot_limit_amt'));
            $result = \Helpers::checkLimitAmount($appId, $request->product_id, $request->limit_amt);

            if ($result['app_type'] == 2 && isset($result['tot_limit_amt'])
                    && $result['tot_limit_amt'] > $totLimitAmt) {
                Session::flash('error', trans('backend_messages.enhanced_tot_limit_amt_validation'));
                return redirect()->back()->withInput();
            } else if ($result['app_type'] == 3 && isset($result['tot_limit_amt'])
                    && ($totLimitAmt >= $result['tot_limit_amt'])) {
                Session::flash('error', trans('backend_messages.reduced_tot_limit_amt_validation'));
                return redirect()->back()->withInput();
            } else if ($result['app_type'] == 3 && isset($result['parent_inv_utilized_amt'])
                    && ($result['parent_inv_utilized_amt'] >= $totLimitAmt)) {
                Session::flash('error', trans('backend_messages.reduced_utilized_amt_validation'));
                return redirect()->back()->withInput();
            } else if ($result['status']) {
                Session::flash('error', $result['message']);
                return redirect()->back()->withInput();
            }

            $totalLimit = $this->appRepo->getAppLimit($appId);

            if($totalLimit){
              //$this->appRepo->saveAppLimit(['tot_limit_amt'=>str_replace(',', '', $request->tot_limit_amt)], $totalLimit->app_limit_id);
            }else{
              $get_res =  $this->appRepo->getSingleAnchorDataByAppId($appId);
              $app_limit = $this->appRepo->saveAppLimit([
                          'app_id'=>$appId,
                          'biz_id'=>$bizId,
                          'user_id' => $get_res['user_id'],
                          'status' =>0,
                          'tot_limit_amt'=>str_replace(',', '', $request->tot_limit_amt),
                          'created_by'=>\Auth::user()->user_id,
                          'created_at'=>\Carbon\Carbon::now(),
                          ]);

            }

            $app_prgm_limit = $this->appRepo->saveProgramLimit([
                          'app_limit_id'=>($totalLimit)? $totalLimit->app_limit_id : $app_limit->app_limit_id,
                          'app_id'=>$appId,
                          'biz_id'=>$bizId,
                          'product_id'=>$request->product_id,
                          'anchor_id'=>$request->anchor_id,
                          'prgm_id'=>$request->prgm_id,
                          'limit_amt'=>str_replace(',', '', $request->limit_amt),
                          'created_by'=>\Auth::user()->user_id,
                          'created_at'=>\Carbon\Carbon::now(),
                          ]);

            $whereActivi['activity_code'] = 'save_limit_assessment';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Limit in Limit Assessment. AppID '. $appId;
                $arrActivity['app_id'] = $appId;
                if (isset($app_limit) && $app_limit) {
                  $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['app_limit' => $app_limit, 'app_prgm_limit' => $app_prgm_limit]), $arrActivity);
                }
            }

            if ($app_prgm_limit) {
                //Update workflow stage
                //Helpers::updateWfStage('approver', $appId, $wf_status = 1, $assign_role = true);
                Session::flash('message',trans('backend_messages.limit_assessment_success'));
                return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            } else {
                return redirect()->back()->withErrors(trans('backend_messages.limit_assessment_fail'));
            }

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    /**
     * Save Approve Limit offer
     *
     * @param Request $request
     * @return type
     */
    public function approveOffer(Request $request)
    {
        try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $userId = $request->has('user_id') ? $request->get('user_id') : null;
            \DB::beginTransaction();
            $appData = $this->appRepo->getAppData($appId);
            if ($appData && in_array($appData->app_type, [3]) ) {
								$parentAppId = $appData->parent_app_id;
								$parentUserId = $appData->user_id;
								$productId = 1;
								$invUtilizedAmt = 0;
								$pAppPrgmLimit = $this->appRepo->getUtilizeLimit($parentAppId, $productId);
								$totalProductLimit = Helpers::getTotalProductLimit($parentAppId, $productId);
								foreach ($pAppPrgmLimit as $value) {
									$attr=[];
									$attr['user_id'] = $parentUserId;
									$attr['app_id'] = $parentAppId;
									$attr['anchor_id'] = $value->anchor_id;
									$attr['prgm_id'] = $value->prgm_id;
									$attr['prgm_offer_id'] = $value->prgm_offer_id;
									// $invUtilizedAmt += Helpers::invoiceAnchorLimitApprove($attr);
                  $invUtilizedAmt += Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($attr);
								}
              if ($totalProductLimit > 0 && $invUtilizedAmt > $totalProductLimit) {
                Session::flash('error', trans('backend_messages.reduction_utilized_amt_appoval_validation'));
                return redirect()->route('cam_report', ['app_id' => $appId, 'biz_id' => $bizId]);
              } else {
                $actualEndDate = \Carbon\Carbon::now()->format('Y-m-d');
                $appLimitData  = $this->appRepo->getAppLimitData(['user_id' => $parentUserId, 'app_id' => $parentAppId]);
                $this->appRepo->updateAppLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId]);
								$this->appRepo->updatePrgmLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId, 'product_id' => $productId]);
								Helpers::updateAppCurrentStatus($parentAppId, config('common.mst_status_id.APP_CLOSED'));
								$this->appRepo->updateAppData($parentAppId, ['status' => 3, 'is_child_sanctioned' => 2]);
              }
            }

            $appApprData = [
                'app_id' => $appId,
                'approver_user_id' => \Auth::user()->user_id,
                'status' => 1
              ];
            $this->appRepo->saveAppApprovers($appApprData);

            //update approve status in offer table after all approver approve the offer.
            $this->appRepo->changeOfferApprove((int)$appId);
            Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_LIMIT_APPROVED'));
            \DB::commit();
            Session::flash('message',trans('backend_messages.offer_approved'));
            return redirect()->route('cam_report', ['app_id' => $appId, 'biz_id' => $bizId]);
        }catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Open Reject offer Pop Up
     *
     * @param Request $request
     * @return type
     */
    public function rejectOfferForm(Request $request)
    {
        try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            return view('backend.cam.reject_offer')
            ->with(['app_id' => $appId, 'biz_id' => $bizId]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save Reject offer comment
     *
     * @param Request $request
     * @return type
     */
    public function rejectOffer(Request $request)
    {
        try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            $cmntText = $request->get('comment_txt');
            $appApprData = [
                'app_id' => $appId,
                'approver_user_id' => \Auth::user()->user_id,
                'status' => 2
              ];
            $this->appRepo->saveAppApprovers($appApprData);

            $addl_data = [];
            $addl_data['sharing_comment'] = $cmntText;
            $selRoleId = 6;
            $roles = $this->appRepo->getBackStageUsers($appId, [$selRoleId]);
            $selUserId = $roles[0]->user_id;
            $currStage = Helpers::getCurrentWfStage($appId);
            //$selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId);
            $selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId, $user_journey=2, $wf_start_order_no=$currStage->order_no, $orderBy='DESC');
            Helpers::updateWfStageManual($appId, $selRoleStage->order_no, $currStage->order_no, $wf_status = 2, $selUserId, $addl_data);
            Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_LIMIT_REJECTED'));

            Session::flash('message', trans('backend_messages.offer_rejected'));
            Session::flash('operation_status', 1);
            return redirect()->route('cam_report', ['app_id' => $appId, 'biz_id' => $bizId]);
            //return redirect()->back();
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /*function for showing offer data*/
    public function showLimitOffer(Request $request){
      $appId = (int)$request->get('app_id');
      $biz_id = $request->get('biz_id');
      $aplid = $request->get('app_prgm_limit_id');
      $prgmOfferId = $request->has('prgm_offer_id') ? $request->get('prgm_offer_id') : null;
      $bizOwners = BizOwner::getCompanyOwnerByBizId($request->get('biz_id'));

      $totalLimit; //total exposure limit amount
      $prgmLimit; //program limit
      $totalOfferedAmount; //total offered amount including all product type from offer table
      $prgmOfferedAmount; //total offered amount related to program from offer table
      $currentOfferAmount; //current offered amount corresponding to app_prgm_limit_id

      $facilityTypeList= $this->mstRepo->getFacilityTypeList();
      $limitData= $this->appRepo->getLimit($aplid);
      $offerData= $this->appRepo->getOfferData(['prgm_offer_id' => $prgmOfferId]);
      $invUtilizedAmt = 0;
      if ($limitData->product_id == 1) {
        $appData = $this->appRepo->getAppData($appId);
        $appType = $appData->app_type;
        $user = $appData->user;
        $user_type = $user->is_buyer;
        //$anchors = $user->anchors;
        $anchors = $this->userRepo->getAnchorsByUserId($user->user_id);
        $anchorArr=[];
        foreach($anchors as $anchor){
          array_push($anchorArr, $anchor->anchor_id);
        }
        $anchorPrgms = $this->appRepo->getPrgmsByAnchor($anchorArr, $user_type);

        if ($appType == 3) {
          $productId = 1;
          $parentAppId = $appData->parent_app_id;
          $parentUserId = $appData->user_id;
          $pAppPrgmLimit = $this->appRepo->getUtilizeLimit($parentAppId, $productId);
          foreach ($pAppPrgmLimit as $value) {
            $attr=[];
            $attr['user_id'] = $parentUserId;
            $attr['app_id'] = $parentAppId;
            $attr['anchor_id'] = $value->anchor_id;
            $attr['prgm_id'] = $value->prgm_id;
            $attr['prgm_offer_id'] = $value->prgm_offer_id;
            // $invUtilizedAmt += Helpers::invoiceAnchorLimitApprove($attr);
            $invUtilizedAmt += Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($attr);
          }
        }
      } else {
        $appType = '';
        $anchors = [];
        $anchorPrgms = [];
      }
      // get Total Sub Limit amount by app_prgm_limit_id
      $totalSubLmtAmt = $this->appRepo->getTotalByPrgmLimitId($aplid);

      $currentOfferAmount = isset($offerData->prgm_limit_amt)? $offerData->prgm_limit_amt: 0;
      $totalOfferedAmount = $this->appRepo->getTotalOfferedLimit($appId);
      $totalLimit = $this->appRepo->getAppLimit($appId);
      $equips = $this->appRepo->getEquipmentList();

      if(!is_null($limitData->prgm_id)){
        $prgmOfferedAmount= $this->appRepo->getProgramBalanceLimit($limitData->prgm_id);
        $prgmLimit = $limitData->program->anchor_sub_limit;
      }else{
        $prgmOfferedAmount = 0;
        $prgmLimit = 0;
      }
      // $currentOfferAmount = $offerData->prgm_limit_amt ?? 0;
      // $limitBalance = (int)$limitData->limit_amt - (int)$totalSubLmtAmt + (int)$currentOfferAmount;
      $assets = [];
      if ($limitData->product_id == 2) {
        $assets = $this->appRepo->getAssetList();
      }

      $page = ($limitData->product_id == 1)? 'supply_limit_offer': (($limitData->product_id == 2)? 'term_limit_offer': 'leasing_limit_offer');
      return view('backend.cam.'.$page, ['offerData'=>$offerData, 'limitData'=>$limitData, 'totalOfferedAmount'=>$totalOfferedAmount, 'programOfferedAmount'=>$prgmOfferedAmount, 'totalLimit'=> $totalLimit->tot_limit_amt, 'currentOfferAmount'=> $currentOfferAmount, 'programLimit'=> $prgmLimit, 'equips'=> $equips, 'facilityTypeList'=>$facilityTypeList, 'subTotalAmount'=>$totalSubLmtAmt, 'anchors'=>$anchors, 'anchorPrgms'=>$anchorPrgms, 'bizOwners'=>$bizOwners, 'appType'=>$appType, 'assets' => $assets, 'invUtilizedAmt' => $invUtilizedAmt]);
    }

    /*function for updating offer data*/
    public function updateLimitOffer(Request $request){
      try{
        $appId = $request->get('app_id');        
        $bizId = $request->get('biz_id');
        $anchor_id = $request->get('anchor_id');
        $prgmOfferId = $request->get('offer_id');
        $aplid = (int)$request->get('app_prgm_limit_id');
        $request['prgm_limit_amt'] = str_replace(',', '', $request->prgm_limit_amt);
        $limitData = $this->appRepo->getLimit($aplid);
        $anchorData = Anchor::getAnchorById($anchor_id);
        // enhancement check
        $program_id = (int)$request->prgm_id;
        $anchorId = (int)$request->anchor_id;
        $prgm_data =  $this->appRepo->getProgram(['prgm_id' => $program_id]);

        if ($limitData->product_id == 1) {
          $program_id = (int)$request->prgm_id;
          $prgm_data =  $this->appRepo->getProgram(['prgm_id' => $program_id]);
          $offerIsExist = \Helpers::checkAnchorPrgmOfferDuplicate($prgm_data->anchor_id, $program_id, $appId);

          if ((!$prgmOfferId && $offerIsExist) || ($prgmOfferId && $offerIsExist && $prgmOfferId != $offerIsExist->prgm_offer_id)) {
            Session::flash('error', 'Anchor Offer is already generated for this program.');
            return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
          }

          if ($prgm_data->product_id == 1) {
            $anchorPrgmLimit =  $this->getAnchorProgramLimit($appId, $program_id, $prgmOfferId);
           // dd($anchorPrgmLimit,$request->prgm_limit_amt,$appId,$program_id,$prgmOfferId,$anchorPrgmLimit['prgm_limit']);
            if ($anchorData->is_fungible == 1) {
                /*if($request->prgm_limit_amt > $anchorPrgmLimit['prgm_limit']) {
                  Session::flash('error', 'Program limit amount should not be greater than the balance limit.');
                  return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
                }*/

                if($request->prgm_limit_amt > $anchorPrgmLimit['prgmBalLimitAmt']) {
                  Session::flash('error', 'Program limit amount should not be greater than the balance limit.');
                  return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
                }
            }
            if ($anchorData->is_fungible == 0) {
                if ($request->prgm_limit_amt > $anchorPrgmLimit['anchorBalLimitAmt']) {
                  Session::flash('error', 'Program limit amount should not be greater than the anchor balance limit.');
                  return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
                }
            }
          }

          $appData = $this->appRepo->getAppData($appId);
          if ($appData->app_type == 3) {
            $productId = 1;
            $parentAppId = $appData->parent_app_id;
            $parentUserId = $appData->user_id;
            $pAppPrgmLimit = $this->appRepo->getUtilizeLimit($parentAppId, $productId);
            $invUtilizedAmt = 0;
            foreach ($pAppPrgmLimit as $value) {
              $attr=[];
              $attr['user_id'] = $parentUserId;
              $attr['app_id'] = $parentAppId;
              $attr['anchor_id'] = $value->anchor_id;
              $attr['prgm_id'] = $value->prgm_id;
              $attr['prgm_offer_id'] = $value->prgm_offer_id;
              // $invUtilizedAmt += \Helpers::invoiceAnchorLimitApprove($attr);
              $invUtilizedAmt += Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($attr);
            }

            if ($request->prgm_limit_amt <= $invUtilizedAmt) {
              Session::flash('error', 'Program Limit amount can\'t be less than or equal to the previous utilized limit.');
              return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            }
          }

          if ($appData->app_type == 2) {
            $previousProgramLimit = AppProgramOffer::getAmountOfferLimit(['anchor_id' => $anchorId, 'prgm_id' => $program_id, 'app_id' => $appData->parent_app_id]);
            if ($request->prgm_limit_amt <= $previousProgramLimit) {
              Session::flash('error', 'Program Limit amount can\'t be less than or equal to the previous program limit.');
              return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            }
          }
        }

        $request['processing_fee'] = str_replace(',', '', $request->processing_fee);
        $request['check_bounce_fee'] = str_replace(',', '', $request->check_bounce_fee);
        $request['created_at'] = \Carbon\Carbon::now();
        $request['created_by'] = Auth::user()->user_id;
        if($request->has('addl_security')){
          $request['addl_security'] = implode(',', $request->addl_security);
        }
        if($request->has('sub_limit')) {
            $request['prgm_limit_amt'] = str_replace(',', '', $request->sub_limit);
        }
        if($request->has('facility_type_id') && $request->facility_type_id != 3){
          $request['discounting'] = null;
        }elseif($request->has('facility_type_id') && $request->facility_type_id == 3){
          $request['ruby_sheet_xirr'] = null;
          $request['cash_flow_xirr'] = null;
          $request['security_deposit'] = null;
          $request['security_deposit_type'] = null;
          $request['security_deposit_of'] = null;
        }
        
        //$checkApprovalStatus = $this->appRepo->getAppApprovers($appId);
        //if($checkApprovalStatus->count()){
        $whereCondition = ['app_id' => $appId, 'is_approve' => 1, 'status_is_null_or_accepted' =>1];
        $offerData = $this->appRepo->getOfferData($whereCondition);
        if ($offerData && isset($offerData->prgm_offer_id) ) {
          Session::flash('message', trans('backend_messages.under_approval'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }
        \DB::beginTransaction();
        //if (empty($prgmOfferId)) {
          $whereCondition = ['app_id' => $appId, 'is_approve' => 1, 'status_is_null_or_accepted' =>1];
          $offerData = $this->appRepo->getOfferData($whereCondition);
          if ($offerData && isset($offerData->prgm_offer_id)) {
            Session::flash('message', trans('backend_messages.under_approval'));
            return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
          }
          //if (empty($prgmOfferId)) {
            Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_GENERATED'));
            //}
        $offerData= $this->appRepo->addProgramOffer($request->all(), $aplid, $prgmOfferId);
            
        $whereActivi['activity_code'] = 'update_limit_offer';
        $activity = $this->mstRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Add/Update Offer. AppID '. $appId;
            $arrActivity['app_id'] = $appId;
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($offerData), $arrActivity);
        }
        
        $limitData = $this->appRepo->getLimit($aplid);
        if($limitData->product_id == 1){
            $this->addOfferPrimarySecurity($request, $offerData->prgm_offer_id);
            $this->addOfferCollateralSecurity($request, $offerData->prgm_offer_id);
            $this->addOfferPersonalGuarantee($request, $offerData->prgm_offer_id);
            $this->addOfferCorporateGuarantee($request, $offerData->prgm_offer_id);
            $this->addOfferEscrowMechanism($request, $offerData->prgm_offer_id);
            $this->addOfferCharges($request, $offerData->prgm_offer_id);
        }elseif(($request->has('facility_type_id') && $request->facility_type_id != 3) && ($limitData->product_id == 3)){
          /*Add offer PTPQ block*/
          $ptpqArr =[];
          foreach($request->ptpq_from as $key=>$val){
            $ptpqArr[$key]['prgm_offer_id'] = $offerData->prgm_offer_id;
            $ptpqArr[$key]['ptpq_from'] = $request->ptpq_from[$key];
            $ptpqArr[$key]['ptpq_to'] = $request->ptpq_to[$key];
            $ptpqArr[$key]['ptpq_rate'] = $request->ptpq_rate[$key];
            $ptpqArr[$key]['created_at'] = \Carbon\Carbon::now();
            $ptpqArr[$key]['created_by'] = Auth::user()->user_id;
          }
          $this->appRepo->addOfferPTPQ($ptpqArr);
        }

        if($limitData->product_id == 2){
          if (isset($offerData->asset_insurance) && $offerData->asset_insurance == 2 || !isset($offerData->asset_insurance)) {
            $offerData->update([
              'asset_name' => null,
              'timelines_for_insurance' => null,
              'asset_comment' => null,
            ]);
          }
          $this->addOfferPersonalGuarantee($request, $offerData->prgm_offer_id);
        }

        /*
        if (\Helpers::checkApprPrgm($request->prgm_id)) {
            $updatePrgmData = [];
            $updatePrgmData['is_edit_allow'] = 1;

            $whereUpdatePrgmData = [];
            $whereUpdatePrgmData['prgm_id'] = $request->prgm_id;
            $this->appRepo->updateProgramData($updatePrgmData, $whereUpdatePrgmData);
        }
        */
        if($offerData){
          \DB::commit();
          if($prgmOfferId){
            Session::flash('message',trans('backend_messages.limit_offer_update_success'));
          }else{
            Session::flash('message',trans('backend_messages.limit_offer_success'));
          }
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }else{
          Session::flash('message',trans('backend_messages.limit_assessment_fail'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }
      }catch(\Exception $ex){
        \DB::rollBack();
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function showLimit(Request $request){
      $appId = (int)$request->get('app_id');
      $biz_id = $request->get('biz_id');
      $aplid = $request->get('app_prgm_limit_id');

      $currentPrgmLimitData= $this->appRepo->getLimit($aplid); // current program limit amount
      $totalPrgmLimit= $this->appRepo->getTotalPrgmLimitByAppId($appId); // total limit of all program from program limit table
      $totalLimit = $this->appRepo->getAppLimit($appId); //total exposure limit

      $totalOfferedAmount = $this->appRepo->getTotalByPrgmLimitId($aplid); // total offered amount by app_prgm_limit_id

      return view('backend.cam.limit', ['totalOfferedAmount'=>$totalOfferedAmount, 'currentPrgmLimitData'=>$currentPrgmLimitData,  'totalLimit'=> $totalLimit->tot_limit_amt, 'totalPrgmLimit'=> $totalPrgmLimit]);
    }

    public function updateLimit(Request $request){
      try{
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $aplid = (int)$request->get('app_prgm_limit_id');
        $request['limit_amt'] = str_replace(',', '', $request->limit_amt);

        //$checkApprovalStatus = $this->appRepo->getAppApprovers($appId);
        //if($checkApprovalStatus->count()){
        $whereCondition = ['app_id' => $appId, 'is_approve' => 1, 'status_is_null_or_accepted' =>1];
        $offerData = $this->appRepo->getOfferData($whereCondition);
        if ($offerData && isset($offerData->prgm_offer_id) ) {
          Session::flash('message', trans('backend_messages.under_approval'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }

        //Validate Enchancement Limit
        $result = \Helpers::checkLimitAmount($appId, $request->product_id, $request->limit_amt, ['app_prgm_limit_id' => $aplid]);
        if ($result['status']) {
            Session::flash('error', $result['message']);
            return redirect()->back()->withInput();
        }

        $limitData= $this->appRepo->saveProgramLimit($request->all(), $aplid);

        $whereActivi['activity_code'] = 'update_limit';
        $activity = $this->mstRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Update Limit. AppID '. $appId;
            $arrActivity['app_id'] = $appId;
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
        }

        if($limitData){
          //Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_GENERATED'));
          Session::flash('message',trans('backend_messages.limit_assessment_success'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }else{
          Session::flash('message',trans('backend_messages.limit_assessment_fail'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }
      }catch(\Exception $ex){
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function gstin(Request $request, FinanceModel $fin){
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $gstdocs = $fin->getGSTStatements($appId);
      $user = $fin->getUserByAPP($appId);
      $user_id = $user['user_id'];
      $gst_details = $fin->getSelectedGstForApp($biz_id);
      $all_gst_details = $fin->getAllGstForApp($biz_id);
      $gst_no = $gst_details['pan_gst_hash'];

      $currenttop3Cus ='';
      $currenttop3Sup ='';
      $previoustop3Cus ='';
      $previoustop3Sup ='';
      $gstRes='';
      $fileName=$appId."_".$gst_no .".json";
     $filePath=storage_path('app/public/user').'/'.$fileName;

      if(file_exists($filePath)){
      $myfile = file_get_contents($filePath);
      $gstRes=json_decode(base64_decode($myfile),TRUE);
      $currenttop3Cus = ($gstRes) ? $gstRes['current']['top3Cus']:"";
      $previoustop3Cus = ($gstRes) ? $gstRes['previous']['top3Cus']:"";
      $currenttop3Sup =   ($gstRes) ? $gstRes['current']['top3Sup']:"";
      $previoustop3Sup =   ($gstRes) ? $gstRes['previous']['top3Sup']:"";
    }
    //dd($gstRes['current']['quarterly_summary']['quarter1'],"=====",$gstRes['current']['quarterly_summary']['quarter1']['months']);
    //dd($gstRes['last_six_mnth_smry']);
        return view('backend.cam.gstin', ['gstdocs' => $gstdocs, 'appId'=> $appId, 'gst_no'=> $gst_no,'all_gst_details'=> $all_gst_details, 'currenttop3Cus'=> $currenttop3Cus,
         'currenttop3Sup'=> $currenttop3Sup,
        'previoustop3Cus'=>$previoustop3Cus,
        'previoustop3Sup'=>$previoustop3Sup,
        'gstResponsShow'=>$gstRes,
        ]);
    }




    public function showPromoter(Request $request){
        $attribute['biz_id'] = $request->get('biz_id');
        $attribute['app_id'] = $request->get('app_id');
        $arrPromoterData = $this->userRepo->getOwnerApiDetail($attribute);
        $arrCamData = Cam::where('biz_id','=',$attribute['biz_id'])->where('app_id','=',$attribute['app_id'])->first();
        return view('backend.cam.promoter')->with([
            'arrPromoterData' => $arrPromoterData,
            'attribute' => $attribute,
            'arrCamData' => $arrCamData
            ]);;
    }


    /*  for iframe model  */
     public function pullCibilCommercial(Request $request){
       $request =  $request->all();
    }
     /*  for iframe model  */
     public function pullCibilPromoter(Request $request){
       $request =  $request->all();
    }
     /*  for iframe model  */
     public function viewCibilReport(Request $request){
       $request =  $request->all();
    }

    /**
     * View Anchor Form
     *
     * @param Request $request
     * @return null
     *
     * @author Anand
     */
    public function anchorViewForm(Request $request, Gupshup_lib $gupshup)
    {
        try {
            $biz_id = $request->get('biz_id');
            $app_id = $request->get('app_id');
            $liftingData = $this->appRepo->getLiftingDetail($app_id);
            $anchorRelationData = $this->appRepo->getAnchorRelationDetails($app_id);
            $data = [];
            if (!empty($liftingData)) {
                foreach ($liftingData as $key => $value) {
                    $year = $value['year'];
                    $totalPurMaterial = $value['total_pur_material'];
                    $data[$year]['mt_value'][] = $value['mt_value'];
                    $data[$year]['mt_type'] = $value['mt_type'];
                    $data[$year]['anchor_lift_detail_id'][] = $value['anchor_lift_detail_id'];
                    $data[$year]['year'] = $year;
                    $data[$year]['total_pur_material'] = $totalPurMaterial;
                    $data[$year]['mt_amount'][] = $value['amount'];
                }
            }
            return view('backend.cam.cam_anchor_view',['data'=> $data])
                ->with('biz_id',$biz_id)
                ->with('anchorRelationData', $anchorRelationData)
                ->with('app_id',$app_id);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Save Anchor Form
     *
     * @param Request $request
     * @return null
     *
     * @author Anand
     */
   public function  SaveAnchorForm(Request $request,AnchorInfoRequest $anchor)
    {
        try {
            $allData = $request->all();
            $userId = Auth::user()->user_id;
            $relationShipArr = [];
            $liftingArr = [];
            $relationShipArr['biz_id']                          = $allData['biz_id'];
            $relationShipArr['app_id']                          = $allData['app_id'];
            $relationShipArr['year_of_association']             = $allData['year_of_association'];
            $relationShipArr['year_of_assoc_actual']            = $allData['year_of_assoc_actual'];
            $relationShipArr['year_of_assoc_remark']            = $allData['year_of_assoc_remark'];
            $relationShipArr['payment_terms']                   = $allData['payment_terms'];
            $relationShipArr['grp_rating']                      = $allData['grp_rating'];
            $relationShipArr['contact_person']                  = $allData['contact_person'];
            $relationShipArr['contact_number']                  = $allData['contact_number'];
            $relationShipArr['dependence_on_anchor']            = $allData['dependence_on_anchor'];
            $relationShipArr['dependence_on_anchor_actual']     = $allData['dependence_on_anchor_actual'];
            $relationShipArr['dependence_on_anchor_remark']     = $allData['dependence_on_anchor_remark'];
            $relationShipArr['qoq_ot_from_anchor']              = $allData['qoq_ot_from_anchor'];
            $relationShipArr['qoq_ot_from_anchor_actual']       = $allData['qoq_ot_from_anchor_actual'];
            $relationShipArr['qoq_ot_from_anchor_remark']       = $allData['qoq_ot_from_anchor_remark'];
            $relationShipArr['cat_relevance_by_anchor']         = $allData['cat_relevance_by_anchor'];
            $relationShipArr['cat_relevance_by_anchor_actual']  = $allData['cat_relevance_by_anchor_actual'];
            $relationShipArr['cat_relevance_by_anchor_remark']  = $allData['cat_relevance_by_anchor_remark'];
            $relationShipArr['repayment_track_record']          = $allData['repayment_track_record'];
            $relationShipArr['repayment_track_record_actual']   = $allData['repayment_track_record_actual'];
            $relationShipArr['repayment_track_record_remark']   = $allData['repayment_track_record_remark'];
            $relationShipArr['sec_third_gen_trader']            = isset($allData['sec_third_gen_trader']) ? $allData['sec_third_gen_trader'] : null;
            $relationShipArr['gen_trader_actual']               = $allData['gen_trader_actual'];
            $relationShipArr['gen_trader_remark']               = $allData['gen_trader_remark'];
            $relationShipArr['alt_buss_of_trader']              = isset($allData['alt_buss_of_trader']) ? $allData['alt_buss_of_trader'] : null;
            $relationShipArr['alt_buss_of_trader_actual']       = $allData['alt_buss_of_trader_actual'];
            $relationShipArr['alt_buss_of_trader_remark']       = $allData['alt_buss_of_trader_remark'];
            $relationShipArr['self_owned_prop']                 = isset($allData['self_owned_prop']) ? $allData['self_owned_prop'] : null;
            $relationShipArr['self_owned_prop_actual']          = $allData['self_owned_prop_actual'];
            $relationShipArr['self_owned_prop_remark']          = $allData['self_owned_prop_remark'];
            $relationShipArr['trade_ref_check_actual']          = $allData['trade_ref_check_actual'];
            $relationShipArr['trade_ref_check_remark']          = $allData['trade_ref_check_remark'];
            $relationShipArr['adv_tax_payment']                 = isset($allData['adv_tax_payment']) ? $allData['adv_tax_payment'] : null;
            $relationShipArr['adv_tax_payment_actual']          = $allData['adv_tax_payment_actual'];
            $relationShipArr['adv_tax_payment_remark']          = $allData['adv_tax_payment_remark'];
            $relationShipArr['security_deposit']                = str_replace(',', '', $allData['security_deposit']);
            $relationShipArr['note_on_lifting']                 = $allData['note_on_lifting'];
            $relationShipArr['reference_from_anchor']           = $allData['reference_from_anchor'];
            $relationShipArr['anchor_risk_comments']            = $allData['anchor_risk_comments'];
            $anchorRelationData = $this->appRepo->getAnchorRelationDetails($allData['app_id']);
            if (!empty($anchorRelationData)) {
                $relationShipArr['updated_by'] = $userId;
                $this->appRepo->updateAnchorRelationDetails($relationShipArr, $anchorRelationData['anchor_relation_id']);
            }else{
                $relationShipArr['created_by'] = $userId;
                $this->appRepo->saveAnchorRelationDetails($relationShipArr);
            }


            //need to saveddd $relationShipArr and pass its id to lifting table

            //store array date of month
            $months = $allData['month'];
            $mtType = $allData['mt_type'];
            $years = $allData['year'];
            $totalPurMaterial = $allData['total_pur_material'];
            $countMonths = count($months);
            #dd($months, $mtType, $years, $totalPurMaterial, $countMonths);
            $liftingData = $this->appRepo->getLiftingDetail($allData['app_id']);
            for($i = 0; $i < $countMonths; $i++){
               foreach($months[$i]['mt_value'] as $key => $value){
                   if (!empty($liftingData)) {
                     $liftingArr['anchor_lift_detail_id'] = $months[$i]['anchor_lift_detail_id'][$key];;
                   }
                   $liftingArr['app_id'] = $allData['app_id'];
                   $liftingArr['year'] = $years[$i];
                   $liftingArr['total_pur_material'] = $totalPurMaterial[$i];
                   $liftingArr['month'] = $key+1;
                   $liftingArr['mt_type'] = $mtType[$i] ?? 0;
                   $liftingArr['mt_value'] = $value ?? 0;
                   $liftingArr['amount'] = $months[$i]['mt_amount'][$key] ?? 0;
                   if (!empty($liftingData)) {
                      $liftingArr['updated_by'] = $userId;
                      $this->appRepo->updateLiftingDetail($liftingArr, $liftingArr['anchor_lift_detail_id']);
                   }else{
                        $liftingArr['created_by'] = $userId;
                        $this->appRepo->creates($liftingArr);
                   }
               }
           }

          $whereActivi['activity_code'] = 'save_anchor_view';
          $activity = $this->mstRepo->getActivity($whereActivi);
          if(!empty($activity)) {
              $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
              $activity_desc = 'Save Anchor View (CAM). AppID '. $allData['app_id'];
              $arrActivity['app_id'] = $allData['app_id'];
              $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($allData), $arrActivity);
          }

           return redirect()->back()->with('message', 'Anchor Data Saved Successfully.');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function camHygieneSave(Request $request){
      try {
            $arrHygieneData = $request->all();
            $userId = Auth::user()->user_id;
            if($arrHygieneData['cam_hygiene_id'] != ''){
                 $updateHygieneDat = CamHygiene::updateHygieneData($arrHygieneData, $userId);
                 if($updateHygieneDat){
                        Session::flash('message',trans('CAM hygiene information updated successfully'));
                 }else{
                       Session::flash('message',trans('CAM hygiene information not updated successfully'));
                 }
            }else{
                $saveHygieneData = CamHygiene::creates($arrHygieneData, $userId);
                if($saveHygieneData){
                        Session::flash('message',trans('CAM hygiene information saved successfully'));
                 }else{
                       Session::flash('message',trans('CAM hygiene information not saved successfully'));
                 }
            }

            $whereActivi['activity_code'] = 'cam_hygiene_save';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Credit History & Hygine Check Save AppID. '. $arrHygieneData['app_id'];
                $arrActivity['app_id'] = $arrHygieneData['app_id'];
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrHygieneData), $arrActivity);
            }

            return redirect()->route('cam_cibil', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function promoterCommentSave(Request $request){
       try{
            $arrCamData = $request->all();
            //  dd($arrCamData);
            $userId = Auth::user()->user_id;
            $owneridArray = $arrCamData['ownerid'];
            $ckycNumberArray = $arrCamData['ckycNumber'];
            $appId = $arrCamData['app_id'];
            $docId = 77;
            foreach($owneridArray as $key => $ownerId) {
                echo "<br>-->".$ckycNumberArray[$key]."<br>";
                if($ckycNumberArray[$key]!='') {
                    if (preg_match('/^[0-9a-zA-Z]+$/', $ckycNumberArray[$key])) {
                        if(!empty($ckycNumberArray[$key])) {
                            $ownerDocCheck  = $this->docRepo->appOwnerDocCheck($appId, $docId, $ownerId);
                           if(!empty($ownerDocCheck)) {
                            $appDocResponse = $this->docRepo->updateAppDocNumberFilewithArray($ownerDocCheck, $ckycNumberArray[$key]);
                           } else {
                            $appDocData['is_ovd_enabled'] = 0;
                            $appDocData['app_id'] = $appId;
                            $appDocData['biz_owner_id'] = $ownerId;
                            $appDocData['doc_id'] = $docId;
                            $appDocData['is_active'] = 1;
                            $appDocData['is_upload'] = 1;
                            $appDocData['is_active'] = 1;
                            $appDocData['doc_id_no'] = ($ckycNumberArray[$key]) ? $ckycNumberArray[$key] : '';
                            $appDocResponse = $this->docRepo->saveAppDoc($appDocData);

                           }
                        }
                    } else {
                        Session::flash('error',trans('CKYC allow only Alphanumeric'));
                        return redirect()->route('cam_promoter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
                    }
                }
            }

            if($arrCamData['cam_report_id'] != ''){
                 $updateCamData = Cam::updatePromoterComment($arrCamData, $userId);
                 if($updateCamData){
                        Session::flash('message',trans('Management information updated successfully'));
                 }else{
                       Session::flash('message',trans('Management information not updated successfully'));
                 }
            }else{
                $saveCamData = Cam::savePromoterComment($arrCamData, $userId);
                if($saveCamData){
                        Session::flash('message',trans('Management information saved successfully'));
                 }else{
                       Session::flash('message',trans('Management information not saved successfully'));
                 }
            }

            $whereActivi['activity_code'] = 'cam_promoter_comment_save';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'CAM Promoter Comment Save AppID. '. $arrCamData['app_id'];
                $arrActivity['app_id'] = $arrCamData['app_id'];
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrCamData), $arrActivity);
            }

            return redirect()->route('cam_promoter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function viewCamReport(Request $request){
      try{
        $viewData = $this->getCamReportData($request);
        return view('backend.cam.viewCamReport')->with($viewData);
      } catch (Exception $ex) {
          return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function generateCamReport(Request $request){
      try{
        $viewData = $this->getCamReportData($request);
        $bizId = $request->get('biz_id');
        $appId = $request->get('app_id');
        ob_start();
        DPDF::setOptions(['isHtml5ParserEnabled'=> true,'isRemoteEnabled', true]);
        $pdf = DPDF::loadView('backend.cam.downloadCamReport', $viewData,[],'UTF-8');
        self::generateCamPdf($appId, $bizId, $pdf->output());

        $whereActivi['activity_code'] = 'generate_cam_report';
        $activity = $this->mstRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Generate CAM Report AppID. '. $appId;
            $arrActivity['app_id'] = $appId;
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($viewData), $arrActivity);
        }

        return $pdf->download('CamReport.pdf');
      } catch (Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    private function generateCamPdf($appId, $bizId, $pdfContent) {
      $uploadData = Helpers::generateCamPdf($appId, $bizId, $pdfContent);
      $docFile = $this->docRepo->saveFile($uploadData);
      if(!empty($docFile->file_id)) {
          UserAppDoc::where('app_id', '=', $appId)
          ->where('file_type', '=', 2)
          ->where('product_id', '=', config('common.PRODUCT.LEASE_LOAN'))
          ->update(['is_active' => '0']);

          UserAppDoc::create(array(
              'app_id' => $appId,
              'file_id' => $docFile->file_id,
              'product_id' => config('common.PRODUCT.LEASE_LOAN'),
              'file_type' => 2,
              'created_by' => \Auth::user()->user_id,
              'updated_by' => \Auth::user()->user_id
          ));
      }
    }

    public function saveBankDetail(Request $request) {
      try {
            $resultFlag = false;
            $arrData['app_id'] = request()->get('app_id');
            $date = $request->get('debt_on');
            $fund_date = $request->get('fund_date');
            $nonfund_date = $request->get('nonfund_date');
            $tblfund_date = $request->get('tbl_fund_date');
            /*
            if (empty($date)) {
               Session::flash('error',trans('Debt on field can\'t be empty'));
               return redirect()->route('cam_bank', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
            }
             *
             */
            $arrData['debt_on'] = !empty($date) ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d') : null;
            $arrData['fund_ason_date'] = $fund_date != null ? Carbon::createFromFormat('d/m/Y', $fund_date)->format('Y-m-d') : null;
            $arrData['nonfund_ason_date'] = $nonfund_date != null ? Carbon::createFromFormat('d/m/Y', $nonfund_date)->format('Y-m-d') : null;
            $arrData['tbl_fund_ason_date'] = $tblfund_date != null ? Carbon::createFromFormat('d/m/Y', $tblfund_date)->format('Y-m-d') : null;
            $arrData['debt_position_comments'] = request()->get('debt_position_comments');
            $arrData['created_by'] = Auth::user()->user_id;
            $bank_detail_id = $request->get('bank_detail_id');
            if (!empty($bank_detail_id)) {
              $result = FinanceModel::updatePerfios($arrData,'app_biz_bank_detail', $bank_detail_id ,'bank_detail_id');
              $this->saveBankWorkCapitalFacility($request, (int) $bank_detail_id);
              $this->saveBankTermBusiLoan($request, (int) $bank_detail_id);
              $this->saveBankAnalysis($request, (int) $bank_detail_id);
              $resultFlag = true;
            }else{
              $result_id = FinanceModel::insertPerfios($arrData, 'app_biz_bank_detail');
              $this->saveBankWorkCapitalFacility($request, (int) $result_id);
              $this->saveBankTermBusiLoan($request, (int) $result_id);
              $this->saveBankAnalysis($request, (int) $result_id);
              $resultFlag = true;
            }

            $whereActivi['activity_code'] = 'save_bank_detail';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Bank (Banking) Details CAM AppID. '. $arrData['app_id'];
                $arrActivity['app_id'] = $arrData['app_id'];
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrData), $arrActivity);
            }

            if($resultFlag){
                Session::flash('message',trans('Bank detail saved successfully'));
            }else{
                Session::flash('error',trans('Bank detail not saved'));
            }
            return redirect()->route('cam_bank', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function addOfferPrimarySecurity($request, $prgm_offer_id){
      if($request->primary_security == 1){
        $psArr =[];
        foreach($request->ps['ps_security_id'] as $key=>$ps){
          $psArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $psArr[$key]['ps_security_id'] = $request->ps['ps_security_id'][$key];
          $psArr[$key]['ps_type_of_security_id'] = $request->ps['ps_type_of_security_id'][$key];
          $psArr[$key]['ps_status_of_security_id'] = $request->ps['ps_status_of_security_id'][$key];
          $psArr[$key]['ps_time_for_perfecting_security_id'] = $request->ps['ps_time_for_perfecting_security_id'][$key];
          $psArr[$key]['ps_desc_of_security'] = $request->ps['ps_desc_of_security'][$key];
          $psArr[$key]['created_at'] = \Carbon\Carbon::now();
          $psArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferPrimarySecurity($psArr);
      }
    }

    public function addOfferCollateralSecurity($request, $prgm_offer_id){
      if($request->collateral_security == 1){
        $csArr =[];
        foreach($request->cs['cs_desc_security_id'] as $key=>$cs){
          $csArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $csArr[$key]['cs_desc_security_id'] = $request->cs['cs_desc_security_id'][$key];
          $csArr[$key]['cs_type_of_security_id'] = $request->cs['cs_type_of_security_id'][$key];


          $csArr[$key]['cs_status_of_security_id'] = $request->cs['cs_status_of_security_id'][$key];
          $csArr[$key]['cs_time_for_perfecting_security_id'] = $request->cs['cs_time_for_perfecting_security_id'][$key];
          $csArr[$key]['cs_desc_of_security'] = $request->cs['cs_desc_of_security'][$key];
          $csArr[$key]['created_at'] = \Carbon\Carbon::now();
          $csArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferCollateralSecurity($csArr);
      }
    }

    public function addOfferPersonalGuarantee($request, $prgm_offer_id){
      if($request->personal_guarantee == 1){
        $pgArr =[];
        foreach($request->pg['pg_name_of_guarantor_id'] as $key=>$pg){
          $pgArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $pgArr[$key]['pg_name_of_guarantor_id'] = $request->pg['pg_name_of_guarantor_id'][$key];
          $pgArr[$key]['pg_time_for_perfecting_security_id'] = $request->pg['pg_time_for_perfecting_security_id'][$key];
          $pgArr[$key]['pg_residential_address'] = $request->pg['pg_residential_address'][$key];
          $pgArr[$key]['pg_net_worth'] = $request->pg['pg_net_worth'][$key];
          $pgArr[$key]['pg_comments'] = $request->pg['pg_comments'][$key];
          $pgArr[$key]['created_at'] = \Carbon\Carbon::now();
          $pgArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferPersonalGuarantee($pgArr);
      }
    }

    public function addOfferCorporateGuarantee($request, $prgm_offer_id){
      if($request->corporate_guarantee == 1){
        $cgArr =[];
        foreach($request->cg['cg_type_id'] as $key=>$cg){
          $cgArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $cgArr[$key]['cg_type_id'] = $request->cg['cg_type_id'][$key];
          $cgArr[$key]['cg_name_of_guarantor_id'] = $request->cg['cg_name_of_guarantor_id'][$key];
          $cgArr[$key]['cg_time_for_perfecting_security_id'] = $request->cg['cg_time_for_perfecting_security_id'][$key];
          $cgArr[$key]['cg_residential_address'] = $request->cg['cg_residential_address'][$key];
          $cgArr[$key]['cg_comments'] = $request->cg['cg_comments'][$key];
          $cgArr[$key]['created_at'] = \Carbon\Carbon::now();
          $cgArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferCorporateGuarantee($cgArr);
      }
    }

    public function addOfferEscrowMechanism($request, $prgm_offer_id){
      if($request->escrow_mechanism == 1){
        $emArr =[];
        foreach($request->em['em_debtor_id'] as $key=>$em){
          $emArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $emArr[$key]['em_debtor_id'] = $request->em['em_debtor_id'][$key];
          $emArr[$key]['em_expected_cash_flow'] = $request->em['em_expected_cash_flow'][$key];
          $emArr[$key]['em_time_for_perfecting_security_id'] = $request->em['em_time_for_perfecting_security_id'][$key];
          $emArr[$key]['em_mechanism_id'] = $request->em['em_mechanism_id'][$key];
          $emArr[$key]['em_comments'] = $request->em['em_comments'][$key];
          $emArr[$key]['created_at'] = \Carbon\Carbon::now();
          $emArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferEscrowMechanism($emArr);
      }
    }

    public function addOfferCharges($request, $prgm_offer_id){
      if($request->has('charge_names')){
        $chArr =[];
        foreach($request->charge_names as $key=>$ch){
          $id_type = explode('#', $key);
          $chArr[$key]['prgm_offer_id'] = $prgm_offer_id;
          $chArr[$key]['charge_id'] = $id_type[0];
          $chArr[$key]['chrg_type'] = $id_type[1];
          $chArr[$key]['chrg_value'] = $ch;
          $chArr[$key]['created_at'] = \Carbon\Carbon::now();
          $chArr[$key]['created_by'] = Auth::user()->user_id;
        }
        $this->appRepo->addOfferCharges($chArr);
      }
    }

    /**
     * Open Approve Limit Pop Up
     *
     * @param Request $request
     * @return type
     */
    public function approveLimitForm(Request $request)
    {
        try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            return view('backend.cam.approve_limit')
            ->with(['app_id' => $appId, 'biz_id' => $bizId]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /*function for showing offer interest rate data*/
    public function editOfferIRForm(Request $request){
      $prgmOfferId = $request->get('prgm_offer_id');
      $offerData= $this->appRepo->getOfferData(['prgm_offer_id' => $prgmOfferId]);
      return view('backend.cam.edit_offer',['offerData'=> $offerData]);
    }

    /*function for updating offer interest rate data*/
    public function updateOfferIR(Request $request){
      try{

        $validator = Validator::make($request->all(), [
          'interest_rate' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $prgmOfferId = $request->get('prgm_offer_id');
        $offerData= $this->appRepo->getOfferData(['prgm_offer_id' => $prgmOfferId,'app_id' => $appId]);

        $status = TRUE;
        if($offerData && isset($offerData->invoice) && $offerData->invoice->isNotEmpty()){
          foreach($offerData->invoice as $invoice){
            if($invoice->status_id > 9){
              $status = FALSE;
              break;
            }
          }
        }

        if($status){
          $updatedArr['interest_rate'] = $request->get('interest_rate');
          $result= $this->appRepo->saveOfferData($updatedArr,$prgmOfferId);
          Session::flash('message',trans('backend_messages.offer_ir_update'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }else{
          Session::flash('message',trans('backend_messages.offer_ir_fail'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }

      }catch(\Exception $ex){
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }
}
