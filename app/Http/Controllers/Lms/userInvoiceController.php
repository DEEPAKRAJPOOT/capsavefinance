<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInvoiceInterface as InvUserInvRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use DB;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class userInvoiceController extends Controller
{
    //use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $UserInvRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master, InvUserInvRepoInterface $UserInvRepo) {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->master = $master;
        $this->UserInvRepo = $UserInvRepo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display invoice as per User.
     *
     */
    public function getUserInvoice(Request $request) {
        // return view('lms.invoice.generate_invoice');
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);

            return view('lms.invoice.user_invoice_list')->with(['userInfo' => $userInfo]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Create invoice as per User.
     *
     */
    public function createUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $appInfo = $this->UserInvRepo->getAppsByUserId($user_id);
            $appID = $appInfo[0]->app_id;
            $gstInfo = $this->UserInvRepo->getGSTs($appID);
            $customerID = $this->UserInvRepo->getUserCustomerID($user_id);

            $state_list = $this->UserInvRepo->getStateListCode();

            return view('lms.invoice.create_user_invoice')
            ->with(['userInfo' => $userInfo, 'state_list' => $state_list, 'appInfo' => $appInfo, 'gstInfo' => $gstInfo, 'customerID' => $customerID ]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Save invoice as per User.
     *
     */
    public function saveUserInvoice(Request $request) {
        try {
            $arrUserData = $request->all();
            $user_id = $request->get('user_id');
            dd($arrUserData);

            $arrUserData['created_at'] = \carbon\Carbon::now();
            $arrUserData['created_by'] = Auth::user()->user_id;
            $status = false;
            $entity_id = false;


            $userInvoice_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $appInfo = $this->UserInvRepo->getAppsByUserId($user_id);
            $appID = $appInfo[0]->app_id;
            $gstInfo = $this->UserInvRepo->getGSTs($appID);
            $customerID = $this->UserInvRepo->getUserCustomerID($user_id);

            $state_list = $this->UserInvRepo->getStateListCode();

            
            if(!empty($request->get('id'))){
                $entity_id = preg_replace('#[^0-9]#', '', $request->get('id'));
                $entity_data = $this->masterRepo->findEntityById($entity_id);
                if(!empty($entity_data)) {
                    $status = $this->masterRepo->updateEntity($arrEntityData, $entity_id);
                }
            }else{
               $status = $this->masterRepo->saveEntity($arrEntityData); 
            }
            if($status){
                Session::flash('message', $entity_id ? trans('master_messages.entity_edit_success') :trans('master_messages.entity_add_success'));
                return redirect()->route('get_entity_list');
            }else{
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_entity_list');
            }
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Get Business Address by ajax
     */
    public function getBizUserInvoiceAddr(Request $request) {
       try {
        $user_id = $request->get('user_id');

        return $this->UserInvRepo->getBizUserInvoiceAddr($user_id);

       } catch(Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
    }

    /**
     * Get state code by ajax
     */
    public function getUserStateCode(Request $request) {
        try {
            $state_code = $request->get('state_code');

            return $this->UserInvRepo->getUserStateCodeList($state_code);
    
           } catch(Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
           }
    }

}
