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


trait CamTrait
{
    protected function getCamReportData(Request $request){
        try{
            $arrRequest['biz_id'] = $bizId = $request->get('biz_id');
            $arrRequest['app_id'] = $appId = $request->get('app_id');
            $json_files = $this->getLatestFileName($appId,'finance', 'json');
            $arrStaticData = array();
            $arrStaticData['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
            $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Year','3'=>'Quarter','4'=>'Months');
            $arrStaticData['securityDepositType'] = array('1'=>'%','2'=>'INR');
            $arrStaticData['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
            $arrStaticData['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');

            $active_json_filename = $json_files['curr_file'];
            if (!empty($active_json_filename) && file_exists($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)) {
                      $contents = json_decode(base64_decode(file_get_contents($this->getToUploadPath($appId, 'finance').'/'. $active_json_filename)),true);
              }
              $fy = $contents['FinancialStatement']['FY'] ?? array();
              $financeData = [];
              $latest_finance_year = '2000';
              $audited_years = [];
              if (!empty($fy)) {
                foreach ($fy as $k => $v) {
                  $audited_years[] = $v['year'];
                  $latest_finance_year = $latest_finance_year < $v['year'] ? $v['year'] : $latest_finance_year;
                  $financeData[$v['year']] = $v;
                }
              }
                $Columns = getFinancialDetailSummaryColumns();
                $FinanceColumns = [];
                foreach ($Columns as $key => $cols) {
                  $FinanceColumns = array_merge($FinanceColumns, $cols);
                }
               // dd(getTotalFinanceData($financeData['2017']));
                $leaseOfferData = AppProgramOffer::getAllOffers($arrRequest['app_id'], '3');
                $arrOwnerData = BizOwner::getCompanyOwnerByBizId($arrRequest['biz_id']);
                $arrEntityData = Business::getEntityByBizId($arrRequest['biz_id']);
                $arrBizData = Business::getApplicationById($arrRequest['biz_id']);
                $arrBankDetails = FinanceModel::getDebtPosition($appId);
                $arrApproverData =  $this->appRepo->getAppApproversDetails($appId);
                $arrCM = $this->appRepo->getBackStageUsers($appId, array('6'));
                $arrHygieneData = CamHygiene::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
                $finacialDetails = AppBizFinDetail::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();

                $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();        
         
                $arrCamData = Cam::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();

                if(isset($arrCamData['t_o_f_security_check'])){
                    $arrCamData['t_o_f_security_check'] = explode(',', $arrCamData['t_o_f_security_check']);
                }

                /*start code for approve button */
                $approveStatus = $this->appRepo->getApproverStatus(['app_id'=>$appId, 'approver_user_id'=>Auth::user()->user_id, 'is_active'=>1]);
                $currStage = Helpers::getCurrentWfStage($appId);                
                $currStageCode = $currStage->stage_code; 
                /*end code for approve button */
                 if(isset($arrCamData->existing_exposure) && $arrCamData->existing_exposure > 0){
                     $arrCamData->existing_exposure =  (sprintf('%.6f', $arrCamData->existing_exposure/1000000) + 0);
                }
                if(isset($arrCamData->proposed_exposure) && $arrCamData->proposed_exposure > 0){
                     $arrCamData->proposed_exposure =  (sprintf('%.6f', $arrCamData->proposed_exposure/1000000) + 0);
                }
                if( isset($arrCamData->total_exposure) &&  $arrCamData->total_exposure > 0){
                     $arrCamData->total_exposure =  (sprintf('%.6f', $arrCamData->total_exposure/1000000) + 0);
                }

                return [
                    'arrCamData' =>$arrCamData ,
                    'arrBizData' => $arrBizData, 
                    'reviewerSummaryData' => $reviewerSummaryData,
                    'arrHygieneData' => $arrHygieneData,
                    'finacialDetails' => $finacialDetails,
                    'arrOwnerData' => $arrOwnerData,
                    'arrEntityData' => $arrEntityData,
                    'financeData' => $financeData,
                    'FinanceColumns' => $FinanceColumns,
                    'audited_years' => $audited_years,
                    'leaseOfferData' => $leaseOfferData,
                    'arrBankDetails' => $arrBankDetails,
                    'arrApproverData' => $arrApproverData,
                    'arrCM' => $arrCM,
                    'arrStaticData' => $arrStaticData,
                    'approveStatus' => $approveStatus,
                    'currStageCode' => $currStageCode,
                ];
      } catch (Exception $ex) {
          return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }
}