<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Session;
use Helpers;
use PHPExcel; 
use PHPExcel_IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Contracts\Ui\DataProviderInterface;

class ApportionmentController extends Controller
{

    public function __construct(InvLmsRepoInterface $lms_repo ,DataProviderInterface $dataProvider){
        $this->lmsRepo = $lms_repo;
        $this->dataProvider = $dataProvider;
	}
    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewUnsettledTrans(Request $request){
        try {
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 

            return view('lms.apportionment.unsettledTransactions')
                ->with('userDetails', $userDetails);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewSettledTrans(Request $request){
        try {
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 

            return view('lms.apportionment.settledTransactions')
                ->with('userDetails', $userDetails); 

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * View Refund Transactions of User
     * @param Request $request
     * @return view
     */
    public function viewRefundTrans(Request $request){
        try {
            $userId = $request->user_id;
            $userDetails = $this->getUserDetails($userId); 

            return view('lms.apportionment.RefundTransactions')
                ->with('userDetails', $userDetails); 

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    /**
     * Get Unsettled Transactions 
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getUnsettledTrans(int $userId){
        return $this->lmsRepo->getUnsettledTrans($userId);
    }

    /**
     * Get Settled Transactions
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getSettledTrans($userId){
        return $this->lmsRepo->getSettledTrans($userId);
    }

    /**
     * Get Refund Transactions
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getRefundTrans($userId){
        return $this->lmsRepo->getRefundTrans($userId);
    }
    
    /**
     * Get User Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getUserDetails($userId){

    }

    /**
     * Get Payment Details
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    private function getPaymentDetails($paymentId){

    }

    /**
     * View Unsettled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listUnsettledTrans(Request $request){
        try {
            $userId = $request->user_id;
            $paymentId = $request->payment_id;
            
            $payment_date = null;
            $payment = $this->lmsRepo->getPaymentDetail($paymentId);    
            if(!empty($payment)){
                $transactions = $this->getUnsettledTrans($userId);
                return $this->dataProvider->getUnsettledTrans($request,$transactions,$payment);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    
    /**
     * View Settled Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listSettledTrans(Request $request){
        try {
            $userId = $request->user_id;  
            $transactions = $this->getSettledTrans($userId);
            return $this->dataProvider->getSettledTrans($request,$transactions);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * View Refund Transactions of User
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function listRefundTrans(Request $request){
        try {
            $userId = $request->user_id;
            $transactions = $this->getRefundTrans($userId);
            return $this->dataProvider->getRefundTrans($request,$transactions);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
}
