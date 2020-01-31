<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class AddressController extends Controller
{
    //use ApplicationTrait;

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
        $user_id = $request->get('user_id');

        $userInfo = $this->userRepo->getCustomerDetail($user_id);
        return view('lms.address.index')->with(['userInfo' => $userInfo]);
    }

    public function addAddress(Request $request)
    {
        $user_id = $request->get('user_id');


        $state_list =  $this->master->getAddStateList()->toArray();
        return view('lms.address.add_address')->with(['user_id' => $user_id, 'state_list' => $state_list]);
    }

    public function saveAddress(Request $request)
    {
        try {

            $user_id = (int) $request->get('user_id');

            $app_data =  $this->appRepo->getAppDataByOrder(['user_id' => $user_id])->first();

            $arrAddressData = $request->all();
            $arrAddressData['biz_id'] = isset($app_data->biz_id) ? $app_data->biz_id : null;
            $arrAddressData['created_by'] = Auth::user()->user_id;
            $arrAddressData['address_type'] = 6;
            unset($arrAddressData['_token']);


            $status = false;
            $userAddress_id = false;
            if (!empty($request->get('biz_addr_id'))) {

                $userAddress_id = preg_replace('#[^0-9]#', '', $request->get('biz_addr_id'));
                $address_data = $this->appRepo->findUserAddressyById($userAddress_id);
                if (!empty($address_data)) {
                    $arrAddressData['updated_at'] = \carbon\Carbon::now();
                    $status = $this->appRepo->updateUserAddress($arrAddressData, $userAddress_id);
                }
            } else {
                $arrAddressData['created_at'] = \carbon\Carbon::now();
                $status = $this->appRepo->saveAddress($arrAddressData);
            }
            if ($status) {
                Session::flash('message', $userAddress_id ? trans('success_messages.userAdress_edit_success') : trans('success_messages.userAdress_add_success'));
            } else {
                Session::flash('error', trans('master_messages.something_went_wrong'));
            }
            return redirect()->route('addr_get_customer_list', ['user_id' => $user_id]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function editAddress(Request $request)
    {
        $user_id = (int) $request->get('user_id');

        $userAddress_id = preg_replace('#[^0-9]#', '', $request->get('biz_addr_id'));
        $userAddress_data = $this->appRepo->findUserAddressyById($userAddress_id);
        $state_list = ['' => 'Please Select'] + $this->master->getAddStateList()->toArray();

        return view('lms.address.edit_address', ['biz_addr_id' => $request->get('biz_addr_id'),  'userAddress_data' => $userAddress_data, 'user_id' => $user_id, 'state_list' => $state_list]);
    }
}
