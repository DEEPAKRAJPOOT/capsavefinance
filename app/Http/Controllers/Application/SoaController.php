<?php

namespace App\Http\Controllers\Application;

use App\Inv\Repositories\Models\Lms\TransType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\ApplicationInterface as AppRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class SoaController extends Controller {

	protected $appRepo;
	protected $userRepo;
    protected $lmsRepo;

	public function __construct(AppRepoInterface $appRepo, InvLmsRepoInterface $lms_repo, InvUserRepoInterface $user_repo) {
		$this->appRepo  =  $appRepo;
		$this->userRepo = $user_repo;
		$this->lmsRepo = $lms_repo;
		$this->middleware('auth');
	}

	/* soa listing  */
	public function soaList(Request $request)
    {
        $request->offsetset('user_id', auth()->user()->user_id);
        $transTypes = TransType::getTransTypeFilterList();
        $maxPrincipalDPD = null;
        $maxInterestDPD = null;
        $result = null;
		if($request->has('user_id') && $request->user_id){
            $result = $this->getUserLimitDetais($request->user_id);
            $user = $this->userRepo->lmsGetCustomer($request->user_id);
            $maxInterestDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.INTEREST'));
            $maxPrincipalDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
            if($user && $user->app_id){
				$userData['user_id'] = $user->user_id;
				$userData['customer_id'] = $user->customer_id;
				$appDetail = $this->appRepo->getAppDataByAppId($user->app_id);
				if($appDetail){
					$userData['app_id'] = $appDetail->app_id;
					$userData['biz_id'] = $appDetail->biz_id;
			    }
			}
		}
        return view('frontend.soa.list')
        ->with('user', $userData)
        ->with('transTypes', $transTypes)
        ->with('maxDPD', 1)
        ->with('maxPrincipalDPD', $maxPrincipalDPD)
        ->with('maxInterestDPD', $maxInterestDPD)
        ->with(['userInfo' =>  $result['userInfo'] ?? null, 'application' => $result['application'] ?? null, 'anchors' =>  $result['anchors'] ?? null]);
	}

    /* soa consolidated listing  */
    public function soaConsolidatedList(Request $request)
    {
        $request->offsetset('user_id', auth()->user()->user_id);
        $userData = [];
        $transTypes = TransType::getTransTypeFilterList();
		if($request->has('user_id')){
            $result = $this->getUserLimitDetais($request->user_id);
            if(isset($result['userInfo'])){
                $result['userInfo']->outstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
                $result['userInfo']->marginOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.MARGIN')] ])->sum('outstanding'),2);
                $result['userInfo']->nonfactoredOutstandingAmt = number_format($this->lmsRepo->getUnsettledTrans($request->user_id, ['trans_type_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
                $result['userInfo']->unsettledPaymentAmt = number_format($this->lmsRepo->getUnsettledPayments($request->user_id)->sum('amount'),2);
            }
            $user = $this->userRepo->lmsGetCustomer($request->user_id);
            $maxInterestDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.INTEREST'));
            $maxPrincipalDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
            if($user && $user->app_id){
				$userData['user_id'] = $user->user_id;
				$userData['customer_id'] = $user->customer_id;
				$appDetail = $this->appRepo->getAppDataByAppId($user->app_id);
				if($appDetail){
					$userData['app_id'] = $appDetail->app_id;
					$userData['biz_id'] = $appDetail->biz_id;
				}
			}
		}

        return view('frontend.soa.consolidated_list')
        ->with('user', $userData)
        ->with('transTypes', $transTypes)
        ->with('maxDPD', 1)
        ->with('maxPrincipalDPD', $maxPrincipalDPD)
        ->with('maxInterestDPD', $maxInterestDPD)
        ->with(['userInfo' =>  $result['userInfo'],
                'application' => $result['application'],
                'anchors' =>  $result['anchors']
            ]);
    }

    public  function  getUserLimitDetais($user_id)
    {
        try {
            $totalLimit = 0;
            $totalCunsumeLimit = 0;
            $consumeLimit = 0;
            $transactions = 0;
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $application = $this->appRepo->getCustomerApplications($user_id);
            $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);

            foreach ($application as $key => $app) {
                if (isset($app->prgmLimits)) {
                    foreach ($app->prgmLimits as $value) {
                        $totalLimit += $value->limit_amt;
                    }
                }
                if (isset($app->acceptedOffers)) {
                    foreach ($app->acceptedOffers as $value) {
                        $totalCunsumeLimit += $value->prgm_limit_amt;
                    }
                }
            }
            if($userInfo){
                $userInfo->total_limit = number_format($totalLimit);
                $userInfo->consume_limit = number_format($totalCunsumeLimit);
                $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
            }

            $data['userInfo'] = $userInfo;
            $data['application'] = $application;
            $data['anchors'] = $anchors;
            return $data;
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
