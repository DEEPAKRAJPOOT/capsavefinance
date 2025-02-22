<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Models\Lms\UserInvoiceRelation;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class AddressController extends Controller
{
    //use ApplicationTrait;
    use ActivityLogTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        //	$this->docRepo = $doc_repo;
        $this->master = $master;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $totalLimit = 0;
        $totalCunsumeLimit = 0;
        $consumeLimit = 0;
        $transactions = 0;
        $user_id = $request->get('user_id');
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
        $userInfo->total_limit = number_format($totalLimit);
        $userInfo->consume_limit = number_format($totalCunsumeLimit);
        $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
        return view('lms.address.index')->with(['userInfo' => $userInfo]);
    }

    public function addAddress(Request $request)
    {
        $user_id = $request->get('user_id');

        $gsts = $this->appRepo->getGSTsByUserId($user_id);
        $app_gsts = $this->appRepo->getAppGSTsByUserId($user_id);
        // start for default button
        $currCompData = UserInvoiceRelation::getUserCurrCompany($user_id);
        $is_show_default = ($currCompData)? 0: 1;
        // end for default button

        $state_list =  $this->master->getAddStateList()->toArray();
        return view('lms.address.add_address')->with(['user_id' => $user_id, 'state_list' => $state_list, 'gsts' => $gsts, 'app_gsts' => $app_gsts, 'is_show_default'=> $is_show_default]);
    }

    public function saveAddress(Request $request)
    {
        try {

            $user_id = (int) $request->get('user_id');

            $app_data =  $this->appRepo->getAppDataByOrder(['user_id' => $user_id])->first();

            $arrAddressData = $request->all();
            $arrAddressData['biz_id'] = isset($app_data->biz_id) ? $app_data->biz_id : null;
            $arrAddressData['created_by'] = Auth::user()->user_id;
            unset($arrAddressData['_token']);


            $status = false;
            $userAddress_id = false;
            if($request->has('is_default')){
                $this->appRepo->unsetDefaultAddress($user_id);
            }else{
                $arrAddressData['is_default'] = 0;
            }

            if (!empty($request->get('biz_addr_id'))) {
                $userAddress_id = preg_replace('#[^0-9]#', '', $request->get('biz_addr_id'));
                $address_data = $this->appRepo->findUserAddressyById($userAddress_id);
                if (!empty($address_data)) {
                    $arrAddressData['updated_at'] = \carbon\Carbon::now();
                    $status = $this->appRepo->updateUserAddress($arrAddressData, $userAddress_id);
                }
                $bizAddressId = $request->get('biz_addr_id');
            } else {
                $arrAddressData['address_type'] = 6;
                $arrAddressData['created_at'] = \carbon\Carbon::now();
                $status = $this->appRepo->saveAddress($arrAddressData);
                $bizAddressId = $status->biz_addr_id;
            }

            // update biz_pan_gst_api_id in biz_pan_gst table
            if($request->has('gst_no')){
                $this->appRepo->updateGstHideAddress(['is_gst_hide'=>1,'biz_pan_gst_api_id'=>$arrAddressData['biz_pan_gst_api_id'], 'user_id'=>$user_id, 'pan_gst_hash'=>$request->get('gst_no')], $bizAddressId);
            }else{
                //$arrAddressData['is_default'] = 0;
            }
            
            if ($status) {
                Session::flash('message', $userAddress_id ? trans('success_messages.userAdress_edit_success') : trans('success_messages.userAdress_add_success'));
            } else {
                Session::flash('error', trans('master_messages.something_went_wrong'));
            }

            $whereActivi['activity_code'] = 'save_addr';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Address (Manage Sanction Cases) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAddressData), $arrActivity);
            }              
            return redirect()->route('addr_get_customer_list', ['user_id' => $user_id]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editAddress(Request $request)
    {
        $user_id = (int) $request->get('user_id');

        $gsts = $this->appRepo->getGSTsByUserId($user_id);
        $app_gsts = $this->appRepo->getAppGSTsByUserId($user_id);

        $userAddress_id = preg_replace('#[^0-9]#', '', $request->get('biz_addr_id'));
        $userAddress_data = $this->appRepo->findUserAddressyById($userAddress_id);
        $state_list = ['' => 'Please Select'] + $this->master->getAddStateList()->toArray();

        // start for default button
        $currCompData = UserInvoiceRelation::getUserCurrCompany($user_id);
        $is_show_default = ($currCompData)? 0: 1;
        // end for default button

        return view('lms.address.edit_address', ['biz_addr_id' => $request->get('biz_addr_id'),  'userAddress_data' => $userAddress_data, 'user_id' => $user_id, 'state_list' => $state_list, 'gsts'=> $gsts, 'app_gsts'=> $app_gsts, 'is_show_default'=>$is_show_default]);
    }

    public function changeFIStatus(Request $request)
    {
        try {
            $status = false;
            $arrActivity = [];
            if (!empty($request->get('biz_addr_id'))) {
                $userAddress_id = preg_replace('#[^0-9]#', '', $request->get('biz_addr_id'));
                $address_data = $this->appRepo->findUserAddressyById($userAddress_id);
                if (!empty($address_data) && is_numeric($userAddress_id) && is_numeric($request->get('status'))) {
                    $arrAddressData['updated_at'] = \carbon\Carbon::now();
                    $arrAddressData['rcu_status'] = $request->get('status') == 1 ? 0 : 1;
                    $status = $this->appRepo->updateUserAddress($arrAddressData, $userAddress_id);
                }
            }
            if ($status) {
                $arrActivity['activity_code'] = 'chng_fi_status';
                // $arrActivity['activity_desc'] = 'Manually Done By Hirdesh Dixit';
                $arrActivity['activity_desc'] = 'Manually change fi status';                
                $arrActivity['user_id'] = $request->get('user_id');
                $arrActivity['app_id'] = $address_data->business->app->app_id;
                \Event::dispatch("ADD_ACTIVITY_LOG", serialize($arrActivity));
                Session::flash('message', $userAddress_id ? trans('success_messages.fi_status_success') : trans('success_messages.fi_status_success'));
            } else {
                Session::flash('error', trans('master_messages.something_went_wrong'));
            }
            return redirect()->route('addr_get_customer_list', ['user_id' => $request->get('user_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
