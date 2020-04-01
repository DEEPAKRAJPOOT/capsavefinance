<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Response;
use Exception;

use Helpers;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\CamHygiene;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\AppBizFinDetail;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Master\Equipment;
use App\Inv\Repositories\Models\CamReviewerSummary;
use App\Inv\Repositories\Models\CamReviewSummPrePost;
use App\Inv\Repositories\Models\GroupCompanyExposure;
use App\Inv\Repositories\Models\Master\Group;

trait CamTrait
{
    protected function getCamReportData(Request $request){
        try{
            $preCondArr = [];
            $postCondArr = [];
            $arrRequest['biz_id'] = $bizId = $request->get('biz_id');
            $arrRequest['app_id'] = $appId = $request->get('app_id');
            $json_files = $this->getLatestFileName($appId,'finance', 'json');
            $arrStaticData = array();
            $arrStaticData['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
            $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
            $arrStaticData['securityDepositType'] = array('1'=>'INR','2'=>'%');
            $arrStaticData['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
            $arrStaticData['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');

            $active_json_filename = $json_files['curr_file'];
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
            $curr_fin_year = date('Y') - 1;
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
                $Columns = getFinancialDetailSummaryColumns();
                $FinanceColumns = [];
                foreach ($Columns as $key => $cols) {
                  $FinanceColumns = array_merge($FinanceColumns, $cols);
                }
               // dd(getTotalFinanceData($financeData['2017']));
                $leaseOfferData = array();
                $leaseOfferData = AppProgramOffer::getAllOffers($arrRequest['app_id'], '3');
                $facilityTypeList= $this->mstRepo->getFacilityTypeList()->toarray();

                $arrOwnerData = BizOwner::getCompanyOwnerByBizId($arrRequest['biz_id']);
                $arrEntityData = Business::getEntityByBizId($arrRequest['biz_id']);
                $arrBizData = Business::getApplicationById($arrRequest['biz_id']);
                $arrBankDetails = FinanceModel::getDebtPosition($appId);
                $arrApproverData =  $this->appRepo->getAppApproversDetails($appId);
                $arrReviewer = $this->appRepo->getBackStageUsers($appId, array('7'));
               
                $arrHygieneData = CamHygiene::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
                $finacialDetails = AppBizFinDetail::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();

                $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();        
         
                $arrCamData = Cam::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
                if(!empty($arrCamData)){
                    $arrUserData = $this->userRepo->find($arrCamData->updated_by, '');
                    $arrCamData->By_updated = "$arrUserData->f_name $arrUserData->l_name";
                }

                if(isset($arrCamData['t_o_f_security_check'])){
                    $arrCamData['t_o_f_security_check'] = explode(',', $arrCamData['t_o_f_security_check']);
                }
                $arrGroupCompany = array();
                if(isset($arrCamData['group_company']) && is_numeric($arrCamData['group_company'])){
                  $arrGroupCompany = GroupCompanyExposure::where(['group_Id'=>$arrCamData['group_company'], 'is_active'=>1])->get()->toArray();
                  $arrMstGroup =  Group::where('id', $arrCamData['group_company'])->first()->toArray();
                  if(!empty($arrMstGroup)){
                    $arrCamData['group_company'] = $arrMstGroup['name'];
                  }
                } 
                if(!empty($arrGroupCompany)){
                    $temp = array();
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



                 //dd($arrGroupCompany);
                /*start code for approve button */
                $approveStatus = $this->appRepo->getApproverStatus(['app_id'=>$appId, 'approver_user_id'=>Auth::user()->user_id, 'is_active'=>1]);
                $currStage = Helpers::getCurrentWfStage($appId);                
                $currStageCode = isset($currStage->stage_code)? $currStage->stage_code: ''; 
                /*end code for approve button */
                 

                if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
                    $dataPrePostCond = CamReviewSummPrePost::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])
                                    ->where('is_active', 1)->get();
                    $dataPrePostCond = $dataPrePostCond ? $dataPrePostCond->toArray() : [];
                    if(!empty($dataPrePostCond)) {
                      $preCondArr = array_filter($dataPrePostCond, array($this, "filterPreCond"));
                      $postCondArr = array_filter($dataPrePostCond, array($this, "filterPostCond"));
                    }
                } 
                $supplyOfferData = $this->appRepo->getAllOffers($appId, 1);//for supply chain
                foreach($supplyOfferData as $key=>$val){
                  $supplyOfferData[$key]['anchorData'] = $this->userRepo->getAnchorDataById($val->anchor_id)->pluck('f_name')->first();
                  $supplyOfferData[$key]['programData'] = $this->appRepo->getSelectedProgramData(['prgm_id' => $val->prgm_id], ['*'], ['programDoc', 'programCharges'])->first();
                  $supplyOfferData[$key]['subProgramData'] = $this->appRepo->getSelectedProgramData(['prgm_id' => $val->prgm_id, 'is_null_parent_prgm_id' => true], ['*'], ['programDoc', 'programCharges'])->first();
                }
                return [
                    'arrCamData' =>$arrCamData,
                    'arrBizData' => $arrBizData, 
                    'reviewerSummaryData' => $reviewerSummaryData,
                    'arrHygieneData' => $arrHygieneData,
                    'finacialDetails' => $finacialDetails,
                    'arrOwnerData' => $arrOwnerData,
                    'arrEntityData' => $arrEntityData,
                    'financeData' => $financeData,
                    'growthData' => $growth_data,
                    'FinanceColumns' => $FinanceColumns,
                    'audited_years' => $audited_years,
                    'leaseOfferData' => $leaseOfferData,
                    'arrBankDetails' => $arrBankDetails,
                    'arrApproverData' => $arrApproverData,
                    'arrReviewer' => $arrReviewer,
                    'arrStaticData' => $arrStaticData,
                    'approveStatus' => $approveStatus,
                    'currStageCode' => $currStageCode,
                    'preCondArr' => $preCondArr,
                    'postCondArr' => $postCondArr,
                    'facilityTypeList'=>$facilityTypeList,
                    'arrGroupCompany' => $arrGroupCompany,
                    'supplyOfferData' => $supplyOfferData
                ];
      } catch (Exception $ex) {
          return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    protected function savePrePostConditions($request, $cam_reviewer_summary_id)
    {
        $updateData = [];
        $updateData['is_active'] = 0;
        $updateData['updated_by'] = Auth::user()->user_id;
        $updPrePost = CamReviewSummPrePost::where('cam_reviewer_summary_id', $cam_reviewer_summary_id)
                        ->whereIn('cond_type', [1,2]);
        $updPrePost->update($updateData);
        $arrData =[];
        if(isset($request->pre_cond)) {
            foreach($request->pre_cond as $key=>$val){
                if($request->pre_cond[$key] != null) {
                    $arrData[$key]['cam_reviewer_summary_id'] = $cam_reviewer_summary_id;
                    $arrData[$key]['cond'] = $request->pre_cond[$key];
                    $arrData[$key]['timeline'] = $request->pre_timeline[$key];
                    $arrData[$key]['cond_type'] = 1;
                    $arrData[$key]['is_active'] = 1;
                    $arrData[$key]['created_at'] = \Carbon\Carbon::now();
                    $arrData[$key]['created_by'] = Auth::user()->user_id;
                }
            }  
            CamReviewSummPrePost::insert($arrData);          
        }

        $arrData =[];
        if(isset($request->post_cond)) {
          foreach($request->post_cond as $key=>$val){
              if($request->post_cond[$key] != null) {
                  $arrData[$key]['cam_reviewer_summary_id'] = $cam_reviewer_summary_id;
                  $arrData[$key]['cond'] = $request->post_cond[$key];
                  $arrData[$key]['timeline'] = $request->post_timeline[$key];
                  $arrData[$key]['cond_type'] = 2;
                  $arrData[$key]['is_active'] = 1;
                  $arrData[$key]['created_at'] = \Carbon\Carbon::now();
                  $arrData[$key]['created_by'] = Auth::user()->user_id;
              }
          }    
          CamReviewSummPrePost::insert($arrData);        
        }

        // if(isset($arrData) && !empty($arrData)) {
        //   CamReviewSummPrePost::insert($arrData);
        // }
        
    }
}
