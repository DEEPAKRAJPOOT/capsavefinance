<?php
namespace App\Http\Controllers;
use Auth;
use Helpers;
use Session;
use Mail;
use Carbon\Carbon;
use Event;
use Datetime;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Ui\DataProviderInterface;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Master\Country;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Master\Cluster;
use App\Inv\Repositories\Models\Master\RightType;
use App\Inv\Repositories\Models\Master\Source;
use App\Inv\Repositories\Models\Rights;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\RightCommission;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInvoiceInterface as InvUserInvRepoInterface;
use App\Http\Requests\Company\ShareholderFormRequest;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\UserReqDoc;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\Master\Group;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\GroupCompanyExposure;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Lms\OverdueReportLog;
use App\Inv\Repositories\Contracts\Traits\InvoiceTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Models\AppSanctionLetter;
use App\Inv\Repositories\Models\Lms\ChargesTransactions;
use App\Inv\Repositories\Models\Lms\ChargeTransactionDeleteLog;
use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\AppSecurityDoc;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Lms\OutstandingReportLog;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\Lms\ReconReportLog;
use App\Inv\Repositories\Contracts\UcicUserInterface as InvUcicUserRepoInterface;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\UcicUserUcic;

class AjaxController extends Controller {

    /**
     * Request
     *
     * @var \Illuminate\Http\Request;
     */
    protected $request;
    protected $user;
    protected $application;
    protected $invRepo;
    protected $docRepo;
    protected $lms_repo;
    protected $ucicuser_repo;
    protected $appRepo;
    use ApplicationTrait;
    use LmsTrait;
    use ActivityLogTrait;


    function __construct(Request $request, InvUserRepoInterface $user, InvAppRepoInterface $application,InvMasterRepoInterface $master, InvoiceInterface $invRepo,InvDocumentRepoInterface $docRepo, FinanceInterface $finRepo, InvLmsRepoInterface $lms_repo, InvUserInvRepoInterface $UserInvRepo, ReportInterface $reportsRepo, InvUcicUserRepoInterface $ucicuser_repo) {
        // If request is not ajax, send a bad request error
        if (!$request->ajax() && strpos(php_sapi_name(), 'cli') === false) {
            abort(400);
        }
        $this->request = $request;
        $this->userRepo = $user;
        $this->application = $application;
        $this->masterRepo = $master;
        $this->lmsRepo = $lms_repo;
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->finRepo = $finRepo;
        $this->UserInvRepo = $UserInvRepo;        
        $this->reportsRepo = $reportsRepo;
        $this->middleware('checkEodProcess');
        $this->middleware('checkBackendLeadAccess');
        $this->ucicuser_repo = $ucicuser_repo;
        $this->appRepo = $application;
    }

    /**
     * Get all User list
     *
     * @return json user data
     */
    public function getLeads(DataProviderInterface $dataProvider) {
        $usersList = $this->userRepo->getAllUsers();
        $users = $dataProvider->getUsersList($this->request, $usersList);
        return $users;
    }

    /**
     * Get all User assigned lead list
     *
     * @return json user leads data
     */
    public function getUserLead(DataProviderInterface $dataProvider) {
        $data = $this->request->all();
        
        $usersList = $this->userRepo->getAssignedUsers($data['role_id'],$data['user_id']);
        $users = $dataProvider->getUsersLeadList($this->request, $usersList);
        return $users;
    }

    /**
     * set all selected lead to session
     *
     * @return json set leads data
     */
    public function setUsersLeads() {
        try{

            $data = $this->request->all();
            if(!Session::has('toAssignedData'))
            {
                Session::put('toAssignedData',json_encode($data));
            }else{

                Session::forget('toAssignedData');
                Session::put('toAssignedData',json_encode($data));
            }
            
            $toAssignedData = Session::get('toAssignedData');
            if($toAssignedData != null)
                return response()->json(['status' => 1,'message' => 'User\'s lead set successfully.']);
            else
                return response()->json(['status' => 0,'message' => 'Something went wrong!']);   
            
        } catch (Exception $ex) {
            return response()->json(['status' => 0,'message' => $ex]);
            
        }    
    }


    /**
     * set all selected lead to session
     *
     * @return json set leads data
     */
    public function setUsersApplication() {
        try{

            $data = $this->request->all();
            if(!Session::has('toAssignedData'))
            {
                Session::put('toAssignedData',json_encode($data));
            }else{

                Session::forget('toAssignedData');
                Session::put('toAssignedData',json_encode($data));
            }
            
            $toAssignedData = Session::get('toAssignedData');
            if($toAssignedData != null)
                return response()->json(['status' => 1,'message' => 'User\'s application set successfully.']);
            else
                return response()->json(['status' => 0,'message' => 'Something went wrong!']);   
            
        } catch (Exception $ex) {
            return response()->json(['status' => 0,'message' => $ex]);
            
        }    
    }

    /**
     * Get all country list ajax
     *
     * @return json country data
     */
    public function getStateList(DataProviderInterface $dataProvider) {
        $all_state = State::getStateList();
        $state = $dataProvider->getStateList($this->request, $all_state);
        return $state;
    }

    /**
     * Delete country
     *
     * @return int
     */
    public function deleteCountries(Request $request) {
        $cntry_id = $request->get('cid');
        return Country::deleteCountry($cntry_id);
    }

    /**
     * Delete state
     *
     * @return int
     */
    public function deleteState(Request $request) {
        $state_id = $request->get('state_id');
        return State::deleteState($state_id);
    }

    /**
     * Get all User list
     *
     * @return json user data
     */
    public function getUsersList(DataProviderInterface $dataProvider) {

        $usersList = $this->userRepo->getAllUsers();
        $countries = $dataProvider->getUsersList($this->request, $usersList);
        return $countries;
    }

    /**
     * Get all cluster list ajax
     *
     * @return json cluster data
     */
    public function getClusterList(DataProviderInterface $dataProvider) {
        try {
            $cluster_data = Cluster::getClusterList();

            $cluster = $dataProvider->getClusterList($this->request, $cluster_data);
            return $cluster;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Delete Cluster
     *
     * @return int
     */
    public function deleteCluster(Request $request) {
        $c_id = $request->get('id');
        return Cluster::deleteCluster($c_id);
    }

    /**
     * Get all rights type list ajax
     *
     * @return json cluster data
     */
    public function getRightTyprList(DataProviderInterface $dataProvider) {
        try {
            $right_data = RightType::getRightTypeList();
            $res = $dataProvider->getRightTyprList($this->request, $right_data);
            return $res;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Delete rights type
     *
     * @return int
     */
    public function deleteRightType(Request $request) {
        $c_id = $request->get('id');
        return RightType::deleteRightType($c_id);
    }

    /**
     * Get all Source list ajax
     *
     * @return json Source data
     */
    public function getSourceList(DataProviderInterface $dataProvider) {
        try {
            $source_data = Source::getSourceList();

            $res = $dataProvider->getSourceList($this->request, $source_data);
            return $res;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Delete rights type
     *
     * @return int
     */
    public function deleteSource(Request $request) {
        $s_id = $request->get('id');
        return Source::deleteSource($s_id);
    }

    public function changeuserName($fistName, $lastName, $phone) {
        //echo "<pre>";
        //print_r($request);
        $fistNameNew = substr($fistName, 0, 3);
        $lastNameNew = substr($lastName, 0, 3);
        $phoneNew = substr($phone, 0, 4);
        $userName = $fistNameNew . $lastNameNew . $phoneNew;
        return $userName;
    }
    
    /**
     * Get all User list
     *
     * @return json user data
     */
    public function getCasePools(DataProviderInterface $dataProvider) {
        $appList = $this->application->getApplicationPoolData();
        $apppool = $dataProvider->getAppLicationPool($this->request, $appList);
        return $apppool;
    }
    
    /**
     * Get all Application list
     *
     * @return json user data
     */
    public function getApplications(DataProviderInterface $dataProvider) {
        $appList = $this->application->getApplications();
        $applications = $dataProvider->getAppList($this->request, $appList);
        return $applications;
    }

    /**
     * Get all Application list
     *
     * @return json user data
     */
    public function getAssignedApplications(DataProviderInterface $dataProvider) {
        $appList = $this->application->getAssignedApplications($this->request);
        $applications = $dataProvider->getAssignedAppList($this->request, $appList);
        return $applications;
    }
    
    /**
     * Get all Application list
     *
     * @return json user data
     */
    public function getFiRcuAppList(DataProviderInterface $dataProvider) {
        $appList = $this->application->getAgencyApplications();
        $applications = $dataProvider->getFiRcuAppList($this->request, $appList);
        return $applications;
    }
    
    
    public function getAnchorLists(DataProviderInterface $dataProvider) { 
     $anchUsersList = $this->userRepo->getAllAnchor($orderBy='anchor_id', $datatable=true);
     $users = $dataProvider->getAnchorList($this->request, $anchUsersList);
     return $users;
    }
    
    public function getAnchorLeadLists(DataProviderInterface $dataProvider){
      $anchLeadList = $this->userRepo->getAllAnchorUsers(true);
      $users = $dataProvider->getAnchorLeadList($this->request, $anchLeadList);
        
        return $users; 
    }

    public function checkExistUser(Request $request) {
        // dd($request);
        $email = $request->post('username');
        $anchUsersList = $this->userRepo->getUserByemail($email);
        return $anchUsersList;
    }

    /**
     * Get user specific Application list for frontend
     *
     * @return json user data
     */
    public function getUserApplications(DataProviderInterface $dataProvider) {
        $appList = $this->application->getUserApplications();
        $applications = $dataProvider->getUserAppList($this->request, $appList);
        return $applications;
    }
  
    //////////////////// use for invoice list/////////////////
     public function getInvoiceList(DataProviderInterface $dataProvider) {
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,7);
        $invoice = $dataProvider->getInvoiceList($this->request, $invoice_data);
        return $invoice;
    }
   //////////////////// use for invoice list/////////////////
     public function getBackendInvoiceList(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,7);
        $invoice = $dataProvider->getBackendInvoiceList($this->request, $invoice_data);
        $invoice = $invoice->getData(true); //extract data
        foreach ($invoice['data'] as &$inv) {
            $inv['upfront_interest'] = $this->calculateUpfrontInterest($inv);
        }
        return new JsonResponse($invoice);
    } 
    
     //////////////////// use for invoice list/////////////////
     public function getFrontendInvoiceList(DataProviderInterface $dataProvider) {
        $invoice_data = $this->invRepo->getUserAllInvoice($this->request);
        $invoice = $dataProvider->getFrontendInvoiceList($this->request, $invoice_data);
        return $invoice;
    } 
   //////////////////// get use wise  invoice list/////////////////
     public function getUserWiseInvoiceList(DataProviderInterface $dataProvider) {
        $invoice_data = $this->invRepo->getUserWiseInvoiceData($this->request->user_id);
        $invoice = $dataProvider->getUserWiseInvoiceList($this->request, $invoice_data);
        return $invoice;
    }   

      //////////////////// use for Approve invoice list/////////////////
     public function getBackendInvoiceListApprove(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,8);
        // dd($invoice_data->first());
        $invoice = $dataProvider->getBackendInvoiceListApprove($this->request, $invoice_data);
        $invoice = $invoice->getData(true); //extract data
        foreach ($invoice['data'] as &$inv) {
            $inv['upfront_interest'] = $this->calculateUpfrontInterest($inv);
        }
        return new JsonResponse($invoice);
    } 
        
    
     //////////////////// use for exception case invoice list/////////////////
     public function getBackendEpList(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,28);
        $invoice = $dataProvider->getBackendEpList($this->request, $invoice_data);
        return $invoice;
    } 
      //////////////////// use for Invoice Disbursed Que list/////////////////
     public function getBackendInvoiceListDisbursedQue(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        ini_set('max_execution_time', 10000);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,9);
        $invoiceDetail = clone $invoice_data;
        $anchorIds = $invoiceDetail->distinct('anchor_id')->pluck('anchor_id')->toArray();
        $supplierIds = $invoiceDetail->distinct('supplier_id')->pluck('supplier_id')->toArray();
        $IsOverdueArray = [];
        $isLimitExpiredArray = [];
        $isLimitExceedArray = [];
        $isAnchorLimitExceededArray = [];
        foreach($invoiceDetail->get() as $invoice) {
            if(!isset($IsOverdueArray[$invoice->supplier_id])) {
                $IsOverdueArray[$invoice->supplier_id] = InvoiceTrait::invoiceOverdueCheck($invoice->supplier);
            }

            if (!isset($isLimitExpiredArray[$invoice->supplier_id])) {
                $isLimitExpiredArray[$invoice->supplier_id] = InvoiceTrait::limitExpire($invoice->supplier_id, $invoice->app_id);
            }

            $attribute['user_id'] = $supplierId = $invoice->supplier_id;
            $attribute['anchor_id'] = $anchorId = $invoice->anchor_id;
            $attribute['prgm_id'] = $prgmId = $invoice->program_id;
            $attribute['program_id'] = $invoice->program_id;
            $attribute['prgm_offer_id'] = $offerId = $invoice->prgm_offer_id;
            $attribute['app_id'] = $appId = $invoice->app_id;

            if(!isset($limitExceed["$supplierId:$anchorId:$prgmId:$offerId:$appId"])) {
                $limitExceed["$supplierId:$anchorId:$prgmId:$offerId:$appId"]['limit'] = $limit = InvoiceTrait::ProgramLimit($attribute);
                $limitExceed["$supplierId:$anchorId:$prgmId:$offerId:$appId"]['sum'] = $sum = Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($attribute);
            }else {
                $limit = $limitExceed["$supplierId:$anchorId:$prgmId:$offerId:$appId"]['limit'];
                $sum = $limitExceed["$supplierId:$anchorId:$prgmId:$offerId:$appId"]['sum'];
            }

            $isLimitExceedArray["$invoice->invoice_id:$supplierId:$anchorId:$prgmId:$offerId:$appId"] = Helpers::checkInvoiceLimitExceed($sum, $limit, $invoice->invoice_margin_amount);
            // $isLimitExceedArray[$invoice->invoice_id] = InvoiceTrait::isLimitExceed($invoice->invoice_id);

            if (!isset($isAnchorLimitExceededArray[$invoice->anchor_id])) {
                $isAnchorLimitExceededArray[$invoice->anchor_id] = InvoiceTrait::isAnchorLimitExceeded($invoice->anchor_id, 0);
            }
        }

        $invoice = $dataProvider->getBackendInvoiceListDisbursedQue($this->request, $invoice_data,$IsOverdueArray, $isLimitExpiredArray,$isLimitExceedArray, $isAnchorLimitExceededArray);
        $invoice = $invoice->getData(true); //extract data
        foreach ($invoice['data'] as &$inv) {
            $inv['upfront_interest'] = $this->calculateUpfrontInterest($inv);
        }
        return new JsonResponse($invoice);
    } 
    
      //////////////////// use for Invoice Disbursed Que list/////////////////
     public function getBackendInvoiceListBank(DataProviderInterface $dataProvider) {
       
        $customersDisbursalList = $this->userRepo->lmsGetSentToBankInvCustomer();
        $users = $dataProvider->lmsGetSentToBankInvCustomers($this->request, $customersDisbursalList);
        return $users;
    } 
       //////////////////// use for Invoice Disbursed Que list/////////////////
     public function getFrontendInvoiceListBank(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getUserAllInvoice($this->request,10);
        $invoice = $dataProvider->getFrontendInvoiceListBank($this->request, $invoice_data);
        return $invoice;
    }  
      //////////////////// use for Invoice Disbursed Que list/////////////////
     public function getBackendInvoiceListFailedDisbursed(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,11);
        $invoice = $dataProvider->getBackendInvoiceListFailedDisbursed($this->request, $invoice_data);
        return $invoice;
    } 
    
      //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListDisbursed(DataProviderInterface $dataProvider) {
        ini_set('memory_limit',-1);
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,12);
        $invoice = $dataProvider->getBackendInvoiceListDisbursed($this->request, $invoice_data);
        return $invoice;
    } 
    
     //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListRepaid(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,13);
        $invoice = $dataProvider->getBackendInvoiceListRepaid($this->request, $invoice_data);
        return $invoice;
    } 
    
      //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListReject(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllManageInvoice($this->request,14);
        $invoice = $dataProvider->getBackendInvoiceListReject($this->request, $invoice_data);
        return $invoice;
    } 
    
      //////////////////// use for Invoice Activity  list/////////////////
     public function getBackendInvoiceActivityList(DataProviderInterface $dataProvider) {
       
        $invoice_activity_data = $this->invRepo->getAllActivityInvoiceLog($this->request->inv_name);
        $invoice_activity_data = $dataProvider->getBackendInvoiceActivityList($this->request, $invoice_activity_data);
        return $invoice_activity_data;
    }
    
      //////////////////// Use For Bulk Transaction /////////////////
     public function getBackendBulkTransaction(DataProviderInterface $dataProvider) 
    {
       $trans_data = $this->invRepo->getAllManualTransaction();
       $trans_data = $dataProvider->getAllManualTransaction($this->request, $trans_data);
       return   $trans_data;
   } 
    
    ///////////////////////use fro rePayment///////////////////////////////
    function saveRepayment(Request $request)
    {
        $arrFileData = $request->all();
        $uploadData = Helpers::uploadAppFile($arrFileData, $arrFileData['app_id']);
        $userFile = $this->docRepo->saveFile($uploadData);
        $user_id  = Auth::user()->user_id;
        $mytime = Carbon::now();
        $invTrnas  = ['user_id' =>  $arrFileData['user_id'],
                        'invoice_id' => $arrFileData['invoice_id'],
                        'repaid_amount' =>  $arrFileData['repaid_amount'],
                        'repaid_date' => ($arrFileData['repaid_date']) ? Carbon::createFromFormat('d/m/Y', $arrFileData['repaid_date'])->format('Y-m-d') : '',
                        'trans_type'   => 17,            
                        'file_id' => $userFile->file_id,
                        'created_at' => $mytime,
                        'created_by' => $user_id ];
           $result = $this->invRepo->saveRepayment($invTrnas);
            if( $arrFileData['repaid_amount'] > $arrFileData['final_amount'])
            {
                $amount = 0;
            }
            else
            {
               $amount =  $arrFileData['final_amount'] - $arrFileData['repaid_amount'];
            }
        if($result)
        {   ///////////// update repayment here////////////////////////
            $data['invoice_id']        = $arrFileData['invoice_id'];
            $data['repaid_amount']  = $arrFileData['repaid_amount'];
            $result = $this->invRepo->updateRepayment($data);
            $utr  ="";
            $check  ="";
            $unr  ="";
            if($arrFileData['payment_type']==1)
            {
                $utr =   $arrFileData['utr_no'];  
            }
            else  if($arrFileData['payment_type']==2)
            {
               $check = $arrFileData['utr_no'];
            }
              else  if($arrFileData['payment_type']==3)
            {
               $unr =  $arrFileData['utr_no'];
            }
            $tran  = [  'gl_flag' => 1,
                        'soa_flag' => 1,
                        'user_id' =>  $arrFileData['user_id'],
                        'trans_date' => ($arrFileData['repaid_date']) ? Carbon::createFromFormat('d/m/Y', $arrFileData['repaid_date'])->format('Y-m-d') : '',
                        'trans_type'   => 17, 
                        'pay_from'   => 0,
                        'amount' =>  $arrFileData['repaid_amount'],
                        'mode_of_pay' =>  $arrFileData['payment_type'],
                        'comment' =>  $arrFileData['comment'],
                        'utr_no' =>  $utr,
                        'cheque_no' =>  $check,
                        'unr_no'    => $unr,
                        'created_at' =>  $mytime,
                        'created_by' =>  $user_id];
            
            $res = $this->invRepo->saveRepaymentTrans($tran);
           if($res)
            {
               return \Response::json(['status' => 1,'amount' =>  $amount]);
            }
           else 
           {
              return \Response::json(['status' => 0,'amount' =>0]);
           }
        }
        else {
           return \Response::json(['status' => 0,'amount' =>0]);
        }
    }
    
     
    public function saveExcelPayment(Request $request)
    {
         $validatedData = $request->validate([
                'customer' => 'required',
                'upload' => 'required'
          ]);
       $extension = $request['upload']->getClientOriginalExtension();
       if($extension!="csv" || $extension!="csv")
       {
          return response()->json(['status' => 0,'message' => 'Please check  file format']); 
       }
       
       $i=0;
       $date = Carbon::now();
       $data = array();
        $userId =  $request['customer'];
        $id = Auth::user()->user_id;
        if ($request['upload']) {
            if (!Storage::exists('/public/user/' . $userId . '/excelpayment')) {
                Storage::makeDirectory('/public/user/' . $userId . '/excelpayment', 0775, true);
            }
            $path = Storage::put('public/user/' . $userId . '/excelpayment', $request['upload'], null);
            $inputArr['file_path'] = str_replace('public/', '', $path);
       }
        $csvFilePath = Storage::url("public/" . $inputArr['file_path']);
         $file = fopen($csvFilePath, "r");
     
        while (!feof($file)) {
          
            $rowData[] = explode(",",fgets($file));
          }
        $rowcount = count($rowData) -1;
        foreach($rowData as $key=>$row)
        {
           
            if($i > 0 && $i < $rowcount)  
            {
               
               if(count($row) < 5)
              {  
                  return response()->json(['status' => 0,'message' => 'Please check column format']); 
              }  
               
                $payment_date  = $row[0]; 
                $virtual_acc  = $row[1]; 
                $amount  = $row[2];
                $payment_ref_no  = $row[3];
                $remarks  =  $row[4];
                $payment_date_format  = $this->validateDate($payment_date, $format = 'd/m/Y');
               
               if(strlen($payment_date) < 10)
               {
                         return response()->json(['status' => 0,'message' => 'Please check the  payment date, It Should be "dd/mm/yy" format']); 
               } 
                if( $payment_date_format==false)
               {
                    return response()->json(['status' => 0,'message' => 'Please check the payment date, It should be "dd/mm/yy" format']); 
             
               }
               if($amount=='')
               {
                     return response()->json(['status' => 0,'message' => 'Please check amount, Amount should not be null']); 
               } 
                if(!is_numeric($amount))
               {
                    return response()->json(['status' => 0,'message' => 'Please check  amount, string value not allowed']); 
               } 
                $data[$i]['payment_date'] = ($payment_date) ? Carbon::createFromFormat('d/m/Y', $payment_date)->format('Y-m-d') : '';
                $data[$i]['amount'] =  $amount;
                $data[$i]['user_id'] = $request['customer'];
                $data[$i]['virtual_account_no'] =  $virtual_acc;
                $data[$i]['payment_ref_no'] =  $payment_ref_no;
                $data[$i]['file_path'] =  $inputArr['file_path'];
                $data[$i]['remark'] = $remarks;
                $data[$i]['created_by'] =  $id;
                $data[$i]['created_at'] =  $date;
             }
          
           $i++;
         
        }
        if(count($data)==0)
        {
                
           return response()->json(['status' => 0,'message' => 'Something went wrong, Please try again']); 
              
        }
       else {
               $whr  = ['status' =>0,'user_id' =>$request['customer']];
                $this->invRepo->deleteExcelTrans($whr);
                $res =  $this->invRepo->insertExcelTrans($data);
                if($res)
                {
                    $getTempInvoice =  $this->invRepo->getExcelTrans($whr);
                    return response()->json(['status' => 1,'data' => $getTempInvoice]); 
                }
                else
                {
                    return response()->json(['status' => 0,'message' => 'Something went wrong, Please try again']); 
       
                }
         }       
                
    }    
  
    
    /* get customer id    /**
     */
    public function getCustomerId(Request $request) 
    {
        $result  =  $this->invRepo->getCustomerId($request->user_id);
        return \Response::json(['status' => $request->user_id,'result' => $result]); 
    }
     
   /* @param DataProviderInterface $dataProvider
      * @param Request $request
      * @return type
      * 
      * 
      */
    
   public function updateInvoiceApprove(Request $request)
   {
        \DB::beginTransaction();
        try {
           if($request->status==8)
           {
               $statuscheck = InvoiceTrait::updateApproveStatus($request);
               \DB::commit();
               return $statuscheck;
            
           }elseif($request->status==14)
           {
            $invoice_id = $request->invoice_id;
             $mytime = Carbon::now(); 
             $cDate   =  $mytime->toDateTimeString();
             $uid = Auth::user()->user_id;
             InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$request->status);
              $res = BizInvoice::where(['invoice_id' =>$invoice_id])->update(['status_id' =>$request->status,'status_update_time' => $cDate,'updated_by' =>$uid]);
              \DB::commit();
              return \Response::json(['status' => $res]);
           }
           else
           {
                $invoice_id = $request->invoice_id;
                $invData = $this->invRepo->getInvoiceData(['invoice_id' => $invoice_id],['supplier_id','app_id']);        
                $supplier_id = isset($invData[0]) ? $invData[0]->supplier_id : null;
                $app_id = isset($invData[0]) ? $invData[0]->app_id : null;                                
                $isLimitExpired = InvoiceTrait::limitExpire($supplier_id, $app_id);
                $isLimitExceed = InvoiceTrait::isLimitExceed($invoice_id);                                
                if ($isLimitExpired || $isLimitExceed) {
                    if ($isLimitExpired) {
                        $remark = 'Customer limit has been expired';
                        $res = 4;
                    } else {                        
                        $remark = 'Limit Exceed';
                        $res = 2;
                    }
                    $attr=[];
                    $attr['invoice_id'] = $invoice_id;
                    $attr['status'] = 28;
                    $attr['remark'] = $remark;
                    $attr['invoice_id'] = $invoice_id;
                    InvoiceTrait::updateInvoiceData($attr);
                    
                } else {               
                    $res =   $this->invRepo->updateInvoice($request->invoice_id,$request->status);   
                }
                $whereActivi['activity_code'] = 'update_invoice_approve_single_tab';
                $activity = $this->masterRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Update Invoice Approve, Approve Tab (Manage Invoice)';
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
                }
                \DB::commit();                
              return \Response::json(['status' => $res]);
           }
        } catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
   }
  
    public function getFiLists(DataProviderInterface $dataProvider, Request $request){
        $fiLists = $this->application->getFiLists($request);
        $fis = $dataProvider->getFiListsList($this->request, $fiLists);
        return $fis; 
    }

    /**
     * get role list
     * @param Request $request
     */ 
    public function getRoleLists(DataProviderInterface $dataProvider) {
       $anchRoleList = $this->userRepo->getRoleList();
       $role = $dataProvider->getRoleList($this->request, $anchRoleList);
       return $role;
    }
    
    /**
     * get role list
     * @param Request $request
     */ 
    public function getUserRoleLists(DataProviderInterface $dataProvider) {
       $List = $this->userRepo->getAllData();
       $role = $dataProvider->getUserRoleList($this->request, $List);
       return $role;
    }

    /**
     * change FI status by agent
     * @param Request $request
     */
    public function changeAgentFiStatus(Request $request){
        $fiAddId = $request->get('fi_addr_id');
        $changeStatus = $request->get('status');
        $agencyName;
        $trigger_email;
        $where = [];
        $where['fi_addr_id'] = $fiAddId;
        $status = $this->application->changeAgentFiStatus($request);
        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $request_info = $request->get('address_id');
        $roleData = \Auth::user()->user_id;
        $assignees = AppAssignment::getAppAssigneWithRoleId((int) $app_id);
        $fiLists = $this->application->getAddressforAgencyFI($biz_id);
        $getFiAddData = $this->application->getFiAddressData($where);
        $checkRoleUserCRCPA = AppAssignment::getAllRoleDataByUserIdAppID($getFiAddData[0]->from_id, $app_id);
        if(!empty($checkRoleUserCRCPA[0])) {
            $triggerUserCreData = $this->userRepo->getUserDetail($getFiAddData[0]->from_id);
            $trigger_email = $triggerUserCreData;
        }
        foreach ($fiLists as $key => $fiList) {
            foreach($fiList->fiAddress as $fiAdd) {
                $agencyName = $fiAdd->agency->comp_name;
            }
        }

        $userCreDataMail = array();
        if(!empty($assignees[0])) {
            foreach ($assignees as $key => $value) {
                $userCreData = $this->userRepo->getUserDetail($value->to_user_id);
                $userCreDataMail[] = $userCreData->email;
            }
            $currUserData = $this->userRepo->getUserDetail($roleData);

            $emailDatas['email'] = isset($userCreDataMail) ? $userCreDataMail : '';
            $emailDatas['name'] = isset($trigger_email) ? $trigger_email->f_name . ' ' . $trigger_email->l_name : '';
            $emailDatas['curr_user'] = isset($currUserData) ? $currUserData->f_name . ' ' . $currUserData->l_name : '';
            $emailDatas['curr_email'] = isset($currUserData) ? $currUserData->email : '';
            $emailDatas['comment'] = isset($comment) ? $comment : '';
            $emailDatas['trigger_type'] = 'FI';
            $emailDatas['subject'] = 'Case Id '. $request_info .' of Agency ' . $agencyName .' updated the status';
            $emailDatas['agency_name'] = $agencyName;
            $emailDatas['trigger_email'] = isset($trigger_email) ? $trigger_email->email : '';
            $emailDatas['change_status'] = config('common.FI_RCU_STATUS')[$changeStatus];
            \Event::dispatch("AGENCY_UPDATE_MAIL_TO_CPA_CR", serialize($emailDatas));
            
        }
        return $status;
    }

    /**
     * change FI status by Credit manager
     * @param Request $request
     */
    public function changeCmFiStatus(Request $request){
      $status = $this->application->changeCmFiStatus($request);
      return $status;
    }

    
    
    /**
     * Get sub industry
     * 
     * @param Request $request
     * @return type Mixed
     * @throws BlankDataExceptions 
     */
    public function getSubIndustry(Request $request)
    {
        $id = $request->get('id');
        $segment_id = $request->get('segmentId');
        if (is_null($id)) {
          //  throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        if($segment_id != null)
            $result = $this->application->getSubIndustryByWhere(['industry_id' => $id, 'segment_id' => $segment_id]);
        else
            $result = $this->application->getSubIndustryByWhere(['industry_id' => $id]);
        
        return response()->json($result);
    }
    
    
    /**
     * get program list
     * 
     * @param Request $request
     * @param DataProviderInterface $dataProvider
     * @return type mixed
     */
    public function getProgramList(Request $request, DataProviderInterface $dataProvider)
    {
        $anchor_id = (int) $request->get('anchor_id');
        return $dataProvider->getPromgramList($request, $this->application->getProgramListById($anchor_id));
    }



    /**
     * change Rcu status by agent
     * @param Request $request
     */
    public function changeAgentRcuStatus(Request $request){
      $status = $this->application->changeAgentRcuStatus($request);
        $docId = $request->get('rcu_doc_id');
        $changeStatus = $request->get('status');
        $where = [];
        $where['rcu_doc_id'] = $docId;

        $app_id = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $request_info = $request->get('address_id');
        $roleData = \Auth::user()->user_id;

        $assignees = AppAssignment::getAppAssigneWithRoleId((int) $app_id);
        if(Auth::user()->agency_id != null)
            $fiLists = $this->application->getRcuActiveLists($app_id);
        else
            $fiLists = $this->application->getRcuLists($app_id);

        foreach ($fiLists as $key => $value) {
            if(Auth::user()->agency_id != null)
                $fiLists[$key]['agencies'] = $this->application->getRcuActiveAgencies($app_id, $value->doc_id);
            else
                $fiLists[$key]['agencies'] = $this->application->getRcuAgencies($app_id, $value->doc_id);        
        }
        $getFiAddData = $this->application->getRcuDocumentData($where);
        $checkRoleUserCRCPA = AppAssignment::getAllRoleDataByUserIdAppID($getFiAddData[0]->from_id, $app_id);
        if(!empty($checkRoleUserCRCPA[0])) {
            $triggerUserCreData = $this->userRepo->getUserDetail($getFiAddData[0]->from_id);
            $trigger_email = $triggerUserCreData;
        }

        $userCreDataMail = array();
        if(!empty($assignees[0])) {
            foreach ($assignees as $key => $value) {
                $userCreData = $this->userRepo->getUserDetail($value->to_user_id);
                $userCreDataMail[] = $userCreData->email;
            }

                $currUserData = $this->userRepo->getUserDetail($roleData);

                $emailDatas['email'] = isset($userCreDataMail) ? $userCreDataMail : '';
                $emailDatas['name'] = isset($trigger_email) ? $trigger_email->f_name . ' ' . $trigger_email->l_name : '';
                $emailDatas['curr_user'] = isset($currUserData) ? $currUserData->f_name . ' ' . $currUserData->l_name : '';
                $emailDatas['curr_email'] = isset($currUserData) ? $currUserData->email : '';
                $emailDatas['comment'] = isset($comment) ? $comment : '';
                $emailDatas['trigger_type'] = 'RCU';
                $emailDatas['subject'] = 'Case Id '. $request_info .' of Agency ' . $fiLists[0]['agencies'][0]->agency->comp_name .' updated the status';
                $emailDatas['agency_name'] = $fiLists[0]['agencies'][0]->agency->comp_name;
                $emailDatas['trigger_email'] = isset($trigger_email) ? $trigger_email->email : '';
                $emailDatas['change_status'] = config('common.FI_RCU_STATUS')[$changeStatus];
                \Event::dispatch("AGENCY_UPDATE_MAIL_TO_CPA_CR", serialize($emailDatas));
        }
      return $status;
    }

    /**
     * change FI status by Credit manager
     * @param Request $request
     */
    public function changeCmRcuStatus(Request $request){
      $status = $this->application->changeCmRcuStatus($request);
      return $status;
    }

    public function getAgencyLists(DataProviderInterface $dataProvider) { 
     $agencyList = $this->userRepo->getAllAgency();
     $agency = $dataProvider->getAgencyList($this->request, $agencyList);
     return $agency;
    }

    public function getAgencyUserLists(DataProviderInterface $dataProvider) { 
     $agencyUserList = $this->userRepo->getAgencyUserLists();
     $agencyUsers = $dataProvider->getAgencyUserLists($this->request, $agencyUserList);
     return $agencyUsers;
    }
    

    /**
     * Get Backend User List By Role Id
     * 
     */
    public function getBackendUserList(Request $request)
    {
        $roleId = $request->get('role_id');
        $usersNotIn = [];
        if ($request->has('user_id')) {
            $userId = $request->get('user_id');
            $usersNotIn = [$userId];
        }
        
        $backendUserList = $this->userRepo->getBackendUsersByRoleId($roleId, $usersNotIn);
        return \Response()->json($backendUserList);
    }


    public function getChargeLists(DataProviderInterface $dataProvider) { 
     $chargesList = $this->masterRepo->getAllCharges();
     $charges = $dataProvider->getChargesList($this->request, $chargesList);
     return $charges;
    }

     public function getLmsChargeLists(DataProviderInterface $dataProvider) { 
      
     $chargesTransList = $this->lmsRepo->getAllTransCharges($this->request->user_id);
     $chargesTransList = $dataProvider->getLmsChargeLists($this->request, $chargesTransList);
     return $chargesTransList;
    }
    
    

    public function getDocLists(DataProviderInterface $dataProvider) { 
     $documentsList = $this->masterRepo->getAllDocuments();
     $documents = $dataProvider->getDocumentsList($this->request, $documentsList);
     return $documents;
    }


    public function getIndustryLists(DataProviderInterface $dataProvider) { 
     $industriesList = $this->masterRepo->getAllIndustries();
     $industries = $dataProvider->getIndustriesList($this->request, $industriesList);
     return $industries;
    }

    // Entities List
    public function getEntityLists(DataProviderInterface $dataProvider) {
        $entities = $this->masterRepo->getAllEntities();
        $data = $dataProvider->getAllEntity($this->request, $entities);
        return $data;
     }
    
   //////* get program charge master  *?
     public function getTransName(Request $request)
     {
       $res  =  $this->lmsRepo->getTransName($request);
       if(count($res) > 0)
       {
               $amountSum  =  $this->lmsRepo->getLimitAmount($request);
               if($amountSum)
               {
                  return response()->json(['status' => 1,'res' => $res,'amount' =>$amountSum]);
               }
               else
               {
                 return response()->json(['status' => 0]);  
               }
    
       }
       else
       {
             return response()->json(['status' => 0]);
       }
     }
    
         public function  getCalculationAmount(Request $request)
      {
          
       if($request->chrg_applicable_id==1)
       {
         $amountSum  =  $this->lmsRepo->getLimitAmount($request);
         $amountSum  = $amountSum[0];
       }
       else if($request->chrg_applicable_id==2 || $request->chrg_applicable_id==3)
       {
         $amountSum  =  $this->lmsRepo->getOutstandingAmount($request);
       }
       else
       {
            $amountSum =  0;
       }
     
     
        if($amountSum)
        {
          
            if($request->is_gst_applicable==1)
            {
                /* apply percentage on amount  ****/
                $getPercentAmount =   $amountSum*$request->percent/100;
                  /* apply GST on percentage amount  ****/
                $getAfterGstAmount =  $getPercentAmount*18/100;
                $final_amount = $getPercentAmount+$getAfterGstAmount; 
            }
            else
            { /* apply percentage on amount  ****/
                $getPercentAmount =  0;
                  /* apply GST on percentage amount  ****/
                $getAfterGstAmount = 0;
                $final_amount = 0; 
                
            }
            return response()->json(['status' => 1,'limit_amount' =>$amountSum,'charge_amount' => $getPercentAmount,'gst_amount' => $final_amount]); 
          }
          else
          {
              /* apply percentage on amount  ****/
                $getPercentAmount =  0;
                  /* apply GST on percentage amount  ****/
                $getAfterGstAmount = 0;
                $final_amount = 0; 
                     return response()->json(['status' => 0,'limit_amount' =>$amountSum,'charge_amount' => $getPercentAmount,'gst_amount' => $final_amount]); 
        
         
          }
          
      }
      
        
      public function  getChrgAmount(Request $request)
      {
          $res =  $request->all();
          $getamount  =   $this->lmsRepo->getSingleChargeAmount($res);
          if($getamount)
          {
            $getPercentage  = $this->masterRepo->getLastGSTRecord();
            if($getPercentage)
            {
              $tax_value  = $getPercentage['tax_value'];
              $chid  = $getPercentage['tax_id'];
            }
            else
            {
               $tax_value  =0; 
               $chid  = 0;
            }
               $request['chrg_applicable_id']  = $getamount->chrg_applicable_id; 
               $gst_percentage                 = $getamount->charge->gst_percentage ?? $getamount->gst_percentage;
               $app = "";
               $sel ="";
                $res =   [  1 => "Limit Amount",
                            2 => "Outstanding Amount",
                            3 => "Outstanding Principal"];
           
                
                 foreach($res as $key=>$val)
                 {
                     if($getamount->chrg_applicable_id==$key)
                     {
                         $sel = "selected";
                     }
                     else
                     {
                          $sel = "";
                     }
                     $app.= "<option value=".$key." $sel>".$val."</option>";
                 }
             
                 if($getamount->chrg_applicable_id==1)
                 {
                   
                     $limitAmount  =  $this->lmsRepo->getLimitAmount($request);
                    /// $limitAmount  = $limitAmount[0];
                   
                 }
                 else if($getamount->chrg_applicable_id==2)
                 {
                     
                     $limitAmount  =  $this->lmsRepo->getOutstandingAmount($request);
                    
                 }
                 else if($getamount->chrg_applicable_id==3)
                 {
                     $limitAmount  =  $this->lmsRepo->getOutstandingAmount($request);
                 }
                 else
                 {
                     $limitAmount  = 0;
                 }
             
          
             return response()->json(['status' => 1,
                 'chrg_applicable_id' => $getamount->chrg_applicable_id,
                 'amount' => $getamount->chrg_calculation_amt,
                 'id' => $getamount->id,
                 'limit' => $limitAmount,
                 'type' => $getamount->chrg_calculation_type,
                 'is_gst_applicable' => $getamount->charge->is_gst_applicable ?? $getamount->is_gst_applicable,
                 'gst_percentage'  =>  $tax_value,
                 'applicable' =>$app]); 
          }
          else
          {
              return response()->json(['status' => 0]); 
          }
          
      }
    /**
     * get charges  html
     * 
     * @param Request $request
     * @return type mixed
     */
    public function getCharagesHtml(Request $request)
    {

        try {
            $id = $request->get('id');
            $len = (int) $request->get('len');
           // dd($len);
            $returns = [];
            $chargeData = $this->application->getChargeData(['id' => $id])->first();
            $chrg_applicable_data = [
                1 => 'Limit Amount', 
                2 => ' Outstanding Amount',
                3 => 'Oustanding Principal',
                4 => 'Outstanding Interest',
                5 => 'Overdue Amount'
            ];
            $returns['contents'] = \View::make('backend.lms.charges_html', 
                    ['data' => $chargeData ,'applicable_data'=>$chrg_applicable_data,'len'=>$len])
                    ->render();
            return ($returns ? \Response::json($returns) : $returns);
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    
    /**
     * get sub program list
     * 
     * @param Request $request
     * @param DataProviderInterface $dataProvider
     * @return type mixed
     */
    public function getSubProgramList(Request $request, DataProviderInterface $dataProvider)
    {
        $program_id = (int) $request->get('program_id');
        $anchor_id = (int) $request->get('anchor_id');
        return $dataProvider->getSubProgramList($request, $this->application->getSubProgramListByParentId($anchor_id, $program_id));
    }

    /**
     * Get DoA Levels List
     * 
     * @param Request $request
     * @param DataProviderInterface $dataProvider
     * @return mixed
     */
    public function getDoaLevelsList(Request $request, DataProviderInterface $dataProvider)
    {        
        $levelList = $this->masterRepo->getDoaLevels();
        return $dataProvider->getDoaLevelsList($request, $levelList);
    }
        
    /**
     * Get City List By State Id
     * 
     * @param Request $request   
     * @return json
     */
    public function getCityList(Request $request)
    {
        $stateId = $request->get('state_id');               
        $cityList = $this->masterRepo->getCity($stateId);
        return \Response()->json($cityList);
    }
    
  /**
   * Get all customer list
   *
   * @return json customer data
   */
  public function lmsGetCustomer(DataProviderInterface $dataProvider) {
    $customersList = $this->userRepo->lmsGetCustomers();
    $users = $dataProvider->lmsGetCustomers($this->request, $customersList);
    return $users;
  }   
  
  public function getCustomer(Request $request){
    $data = LmsUser::getCustomers($request->input('query'));
    return response()->json($data);
  }

  /**
   * Get all customer list
   *
   * @return json customer data
   */
  public function lmsGetDisbursalCustomer(DataProviderInterface $dataProvider) {
    $customersDisbursalList = $this->userRepo->lmsGetDisbursalCustomer();
    $users = $dataProvider->lmsGetDisbursalCustomers($this->request, $customersDisbursalList);
    return $users;
  }

    /**
     * get anchors by product id
     * 
     * @param product_id
     * @return anchors
     */
    public function getAnchorsByProduct(Request $request)
    {
        $product_id = (int)$request->product_id;
        $anchors =  $this->application->getAnchorsByProduct($product_id);
        return json_encode($anchors);
    }
    /**
     * get programs by anchor id
     * 
     * @param anchor_id
     * @return programs
     */
    public function getProgramsByAnchor(Request $request)
    {
        $anchor_id = (int)$request->anchor_id;
        $programs =  $this->application->getProgramsByAnchor($anchor_id);
        return json_encode($programs);
    }

    /**
     * get program balance limit
     * 
     * @param program_id
     * @return program limit
     */
    public function getProgramBalanceLimit_11_feb(Request $request)
    {
        $appId = (int)$request->app_id;
        $program_id = (int)$request->program_id;
        $prgm_limit =  $this->application->getProgramBalanceLimit($program_id);                
        $prgm_data =  $this->application->getProgramData(['prgm_id' => $program_id]);
        $anchorData = Anchor::getAnchorById($anchor_id);
        $utilizedLimit = 0;
        if ($prgm_data && $prgm_data->copied_prgm_id) {            
            $utilizedLimit = \Helpers::getPrgmBalLimit($prgm_data->copied_prgm_id);
        }
        if($anchorData->is_fungible == 0) {
            return json_encode(['prgm_limit' => $prgm_limit + $utilizedLimit , 'prgm_data' => $prgm_data]);
        } else {
            return json_encode(['prgm_limit' => $prgm_limit , 'prgm_data' => $prgm_data]);
        }
    }

    public function getProgramBalanceLimit(Request $request)
    {
        $appId = (int)$request->app_id;
        $program_id = (int)$request->program_id;
        $offer_id = (int)$request->offer_id;
        $anchorId = (int)$request->anchor_id;

        $data = $this->getAnchorProgramLimit($appId, $program_id, $offer_id);
        
        $appData = $this->application->getAppData($appId);
        if ($appData && in_array($appData->app_type, [2])) {
            $data['previousProgramLimit'] = $this->invRepo->getAmountOfferLimit(['anchor_id' => $anchorId, 'prgm_id' => $program_id, 'app_id' => $appData->parent_app_id]);
        }

        return json_encode($data);
        $prgm_limit =  $this->application->getProgramBalanceLimit($program_id);
        $prgm_data =  $this->application->getProgramData(['prgm_id' => $program_id]);
        $anchor_id = $prgm_data->anchor_id;
        $anchorData = Anchor::getAnchorById($anchor_id);
        $utilizedLimit = 0;
        if ($prgm_data && $prgm_data->copied_prgm_id) {
            $utilizedLimit = \Helpers::getPrgmBalLimit($prgm_data->copied_prgm_id);
        }
            return json_encode(['prgm_limit' => $prgm_limit + $utilizedLimit , 'prgm_data' => $prgm_data]);
    }
    
     public function getProgramSingleList(Request $request)
     {
         $anchorRemainLimit = 0;
         $anchorData = Anchor::getAnchorById($request['anchor_id']);
         $anchorApproveInvoiceAmt    = InvoiceTrait::anchorInvoiceApproveAmount($request['anchor_id']);
         $get_program = $this->invRepo->getLimitProgram($request['anchor_id']);
         $get_program_limit = $this->invRepo->geAnchortLimitProgram($request['anchor_id']);
         $anchorRemainLimit = round(($get_program_limit->anchor_limit-$anchorApproveInvoiceAmt),2);
         $get_supplier = [];
            foreach($get_program as $v){
                $program_id = $v->program->prgm_id;
                $supplierData = $this->invRepo->getActiveProgramOfferByPrgmId($program_id);
                foreach ($supplierData as $v1){
                    $get_supplierD['user_id'] = $v1->user_id;
                    $get_supplierD['app_id'] = $v1->app_id;
                    $get_supplierD['prgm_offer_id'] = $v1->prgm_offer_id;
                    $get_supplierD['biz_entity_name'] = $v1->biz_entity_name;
                    $get_supplierD['customer_id'] = $v1->customer_id;
                    $get_supplier[$program_id][] = $get_supplierD; 
                }
            }
         return response()->json(['status' => 1,'limit' => $get_program_limit,'get_program' =>$get_program,'get_supplier'=>$get_supplier,'anchorData' => $anchorData,'anchorRemainLimit' => $anchorRemainLimit]);
     }
       public function getProgramLmsSingleList(Request $request)
     {
       
         $get_program = $this->invRepo->getProgramLmsSingleList($request['anchor_id']);
         $get_program_limit = $this->invRepo->geAnchortLimitProgram($request['anchor_id']);
         return response()->json(['status' => 1,'limit' => $get_program_limit,'get_program' =>$get_program]);
     }
      public function getSupplierList(Request $request)
     {
        
        $result  =  explode(",",$request['program_id']);
        $request['program_id']  = $result[0];
        $request['prgm_offer_id']  = $result[1];
        $getOfferProgramLimit =   $this->invRepo->getOfferForLimit($request['prgm_offer_id']);
        $getProgramLimit =   $this->invRepo->getProgramForLimit($request['program_id']);
        if($request['user']==1)
        {   
            $id = Auth::user()->user_id;
            $get_supplier = $this->invRepo->getUserProgramOfferByPrgmId($request['program_id'],$id);
       
        }
        else
        { 
           $get_supplier = $this->invRepo->getProgramOfferByPrgmId($request['program_id']);
         
        }
        $roles = $this->userRepo->getRolesByType(2);
        $rolesDataArray = [];
        foreach($roles as $role) {
            $rolesDataArray[] = $role->id;
        }
        
        $getPrgm  = $this->application->getProgram($request['program_id']);
        $chkUser  = $this->application->chkUser();
        if($chkUser)
        {     
            //if( $chkUser->id==1)
            if($chkUser->id==1 || in_array($chkUser->id, $rolesDataArray))
            {
                 $customer  = 1;
            }
            else if( $chkUser->id==11)
            {
                 $customer  = 2;
            }
            else
            {
                $customer  = 3;
            }
        }
        else
        {
             $customer  = 3;
        }
        if($request['bulk']==1)
        {
             $expl  =  explode(",",$getPrgm->bulk_invoice_upload); 
        }
        else
        {
             $expl  =  explode(",",$getPrgm->invoice_upload);
        }
        if(in_array($customer, $expl))
        {
           $uploadAcess  = 1; 
        }
        else
        {
          $uploadAcess  = 0;   
        }
        $all_supplier=[];
        foreach($get_supplier as $supplier) {
            $supplier->appCode = \Helpers::formatIdWithPrefix($supplier->app_id, 'APP');
            $all_supplier[] =  $supplier;       
        }
        return response()->json(['status' => 1,'limit' => $getProgramLimit,'offer_id' => $getOfferProgramLimit->prgm_offer_id,'tenor' => $getOfferProgramLimit->tenor,'tenor_old_invoice' =>$getOfferProgramLimit->tenor_old_invoice,'get_supplier' =>$get_supplier,'uploadAcess' =>$uploadAcess]);
     }
      

    public function getTenor(Request $request)
    {       
        $result  =  explode(",",$request['program_id']);
        $supplier_id  =  explode(",",$request['supplier_id']);
        $res['prgm_id']  = $result[0];
        $res['app_prgm_limit_id']  = $result[1];
        $res['user_id']  = $supplier_id[0];
        $res['app_id']  = $supplier_id[1];
        $res['prgm_offer_id']  = $supplier_id[2];
        $res['anchor_id']  = $request['anchor_id'];
        $res['program_id']  = $res['prgm_id'];
        $getTenor   =  $this->invRepo->getTenor($res);
        $limit =   InvoiceTrait::ProgramLimit($res);
        $customerLimitArray = $this->application->getUserLimit($supplier_id[0]);
        $customerLimit      = ($customerLimitArray) ? $customerLimitArray->tot_limit_amt : 0;
        //dd($customerLimit->tot_limit_amt);
        // $sum   =   InvoiceTrait::invoiceApproveLimit($res);
        // $sum   =   Helpers::anchorSupplierUtilizedLimitByInvoice($res['user_id'], $res['anchor_id']);
        $sum   =   Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($res);
        ///////////
        $getUserProgramLimit = $this->application->getUserProgramLimit($supplier_id[0]);
        $getUserActiveProgramLimit = $this->application->getUserActiveProgramLimit($supplier_id[0]);
        $inv_Total_limit = 0; $customberOfferLimit = 0; $consumeLimitByPrgm = 0; $customberOfferLimit = 0;
        foreach($getUserProgramLimit as $uLimit) {
        foreach($uLimit->supplyProgramLimit as $Plimit){ 
            foreach($Plimit->offer as $val) {
                    $obj =  new \App\Helpers\Helper;
                    $val['user_id']  = $uLimit->app->user_id;
                    $inv_Total_limit +=  $obj->anchorSupplierUtilizedLimitByInvoiceByPrgm($val);
                }
            }
        }
        foreach($getUserActiveProgramLimit as $uLimit) {
            foreach($uLimit->supplyProgramLimit as $Plimit){ 
                foreach($Plimit->offer as $val) {
                        $obj =  new \App\Helpers\Helper;
                        $val['user_id']  = $uLimit->app->user_id;
                        if($val['prgm_id'] == $res['program_id'] ) {
                            $consumeLimitByPrgm +=  $obj->anchorSupplierUtilizedLimitByInvoiceByPrgm($val);
                        }
                        if($val['anchor_id'] ==  $res['anchor_id']) {
                         $customberOfferLimit += $val->prgm_limit_amt;
                        }
                    }
                }
            }
        /////////
        $adhocData   =  $this->invRepo->checkUserAdhoc($res);
        $is_adhoc   = $adhocData['amount'];
        $remainAmount = round(($customberOfferLimit - $inv_Total_limit), 2);
        $remainPrgmLimit = round(($limit - $consumeLimitByPrgm), 2);
        $offer = AppProgramOffer::getAppPrgmOfferById($res['prgm_offer_id']);
        $margin = $offer && $offer->margin ? $offer->margin : 0;
        $offerDataJson = null;
        if ($offer){
            $currentDate =  \Helpers::getSysStartDate();
            $curDate = Carbon::parse($currentDate)->setTimezone(config('common.timezone'))->format('Y-m-d');
            $offerData = [
                'interest_rate' => $offer->interest_rate ?? 0,
                'payment_frequency' => $offer->payment_frequency,
                'benchmark_date' => $offer->benchmark_date,
                'margin' => $margin,
                'currentDate' => $curDate,
              ];
              //Convert the "$offerData" object to a JSON string
              $offerDataJson = base64_encode(json_encode($offerData));
        }
        return response()->json(['status' => 1,'tenor' => $getTenor['tenor'],'tenor_old_invoice' =>$getTenor['tenor_old_invoice'],'limit' => $remainPrgmLimit,'remain_limit' =>$remainAmount,'is_adhoc' => $is_adhoc,'margin' => $margin,'offerData' => $offerDataJson]);
    }
    
    public function getAdhoc(Request $request)
    {
      
        $result  =  explode(",",$request['program_id']);
        $supplier_id  =  explode(",",$request['supplier_id']);
        $res['prgm_id']  = $result[0];
        $res['app_prgm_limit_id']  = $result[1];
        $res['user_id']  = $supplier_id[0];
        $res['app_id']  = $supplier_id[1];
        $res['prgm_offer_id']  = $supplier_id[2];
        $res['anchor_id']  = $request['anchor_id'];
        $res['program_id']  = $res['prgm_id'];
        $getTenor   =  $this->invRepo->getTenor($res);
    
       if($request->is_adhoc=='true') 
       { 
        $adhocData   =  $this->invRepo->checkUserAdhoc($res);
        $limit   = $adhocData['amount'];
        $res['app_offer_adhoc_limit_id'] = $adhocData['ids'];
        $sum     = InvoiceTrait::adhocLimit($res);
        $remainAmount = $limit -$sum;
        $is_adhoc = 1;
       }
       else
       {
        $limit =   InvoiceTrait::ProgramLimit($res);
        // $sum   =   InvoiceTrait::invoiceApproveLimit($res);
        // $sum   =   Helpers::anchorSupplierUtilizedLimitByInvoice($res['user_id'], $res['anchor_id']);
        $sum   =   Helpers::anchorSupplierPrgmUtilizedLimitByInvoice($res);
        $remainAmount = $limit-$sum;
        $is_adhoc = 0;
       }
        return response()->json(['status' => 1,'is_adhoc' => $is_adhoc,'tenor' => $getTenor['tenor'],'tenor_old_invoice' =>$getTenor['tenor_old_invoice'],'limit' => $limit,'remain_limit' =>$remainAmount]);
    }
    
    
    /**
     * change program status
     * 
     * @param Request $request
     * @return type mixed
     */
    public function changeProgramStatus(Request $request)
    {
        $program_id = $request->get('program_id');
        $status = $request->get('status');
        $result = $this->application->updateProgramData(['status' => $status], ['prgm_id' => $program_id]);
        return \Response::json(['success' => $result]);
    }
   
     /**
     * change Doa status
     * 
     * @param Request $request
     * @return type mixed
     */
    public function changeDoaStatus(Request $request)
    {
        $doa_level_id = $request->get('doa_level_id');
        $status = $request->get('is_active');
        $result = $this->application->updateDoaData(['is_active' => $status], ['doa_level_id' => $doa_level_id]);
        return \Response::json(['success' => $result]);
    }
 

      function uploadInvoice(Request $request)
      {
        $res  = explode(',',$request->id);
        foreach($res as $key=>$val)
        {

                $attr =   $this->invRepo->getSingleBulkInvoice($val);
                if($attr)
                {
                    $invoice_id = NULL;  
                    if($attr->status==0)
                    {
                        $userLimit = InvoiceTrait::ProgramLimit($attr);
                        $attr['user_id'] = $attr['supplier_id'];
                        $attr['prgm_id'] = $attr['program_id'];
                        $marginAmt = Helpers::getOfferMarginAmtOfInvoiceAmt($attr['prgm_offer_id'], $attr['invoice_approve_amount']);
                        $sum   =   InvoiceTrait::invoiceApproveLimit($attr);
                        $remainAmount = $userLimit - $sum;
                        /*if ($marginAmt > $remainAmount) {
                            return response()->json(['status' => 0,'message' => 'Invoice amount should not be greater than the remaining limit amount after excluding the margin amount for invoice no. '.$attr['invoice_no']]); 
                        }*/
                        $updateInvoice=  InvoiceTrait::updateBulkLimit($userLimit,$attr->invoice_approve_amount,$attr);  
                        $attr['comm_txt'] = $updateInvoice['comm_txt'];
                        $attr['status_id'] = $updateInvoice['status_id'];
                        $attr['invoice_margin_amount'] = $updateInvoice['invoice_margin_amount'];
                        $res  =  $this->invRepo->saveFinalInvoice($attr);
                        InvoiceStatusLog::saveInvoiceStatusLog($res->invoice_id,$attr['status_id']);
                         if($res['status_id']==8)
                        {
                            $inv_apprv_margin_amount = InvoiceTrait::invoiceMargin($res);
                            $is_margin_deduct =  1;  
                            $this->invRepo->updateFileId(['invoice_margin_amount'=>$inv_apprv_margin_amount,'is_margin_deduct' =>1],$res['invoice_id']);
                        }
                   }
                  
                  $attribute['invoice_id'] = $invoice_id;
                  $attribute['status'] = 1;
                  $attribute['invoice_bulk_upload_id'] = $attr->invoice_bulk_upload_id;
                  $attribute['status_id'] = $attr->status_id;
                  $updateBulk  =  $this->invRepo->updateBulkUpload($attribute);  
                }
        }
            if($updateBulk)
            {
                    $whereActivi['activity_code'] = 'upload_invoice_csv';
                    $activity = $this->masterRepo->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Upload Bulk Invoice, Final Submit (Manage Invoice)';
                        $arrActivity['app_id'] = null;
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($res), $arrActivity);
                    }

                     return response()->json(['status' => 1,'message' => 'Invoice successfully saved']); 

            }
            else
            {
                      return response()->json(['status' => 0,'message' => 'Something went wrong, Please try again']); 

            }
        }

    function twoDateDiff($fdate,$tdate)
    {
            $curdate=strtotime($fdate);
            $mydate=strtotime($tdate);

            if($curdate > $mydate)
            {
               return 1;
            }
            else
            {
                return 0;
            }
    }
    function validateDate($date, $format = 'd/m/Y')
    { 
       
       return  $d = DateTime::createFromFormat($format, $date);
      // return $d && $d->format($format) === $date;
     }
     
    function saveInvoiceDoc(Request $request)
    {
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $attributes  = $request->all();
        $uploadData = Helpers::uploadAppFile($attributes, $attributes['app_id']);
        $userFile = $this->docRepo->saveFile($uploadData);
        $arr = array('file_id'  =>$userFile->file_id,
                    'updated_by' => $id,
                    'updated_at' => $date);
        $result = $this->invRepo->updateFileId($arr,$attributes['invoice_id']);
       if($result)
       {
           return response()->json(['status' => 1]); 
       }
       else
       {
           return response()->json(['status' => 0]); 
       }
       
    }
     
    function DeleteTempInvoice(Request $request) {
       
        $whr =  ['invoice_bulk_upload_id' => $request->invoice_bulk_upload_id];
        $res = $this->invRepo->DeleteSingleTempInvoice($whr);
        return response()->json(['status' => 1,'id' => $request->invoice_bulk_upload_id]); 
        
    }
    
   function updateBulkInvoice(Request $request)
   {
       \DB::beginTransaction();
       try {
       $invoiceSubmitID = InvoiceTrait::getInvoiceStatusByIds($request['invoice_id'],$request->currentstatus)->count();
       if(count($request['invoice_id']) > $invoiceSubmitID) {
            \DB::rollback();
            return \response()->json(['duplicatestatus' => 0,'msg' => 'We are unable to process the selected Invoice as some Invoice has been already processed.']);
        }
       $result = InvoiceTrait::checkInvoiceLimitExced($request);
       //$result = []; 
       foreach($request['invoice_id'] as $row)
       {  
          if($request->status==8)
          {
            $attr['invoice_id']=$row; 
           // $response =  InvoiceTrait::updateApproveStatus($attr);  
         
          }elseif($request->status==14)
          {
            $attr['invoice_id']=$row;
            $mytime = Carbon::now(); 
            $cDate   =  $mytime->toDateTimeString();
            $uid = Auth::user()->user_id;
            $response = InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],$request->status);
            BizInvoice::where(['invoice_id' =>$attr['invoice_id']])->update(['status_id' =>$request->status,'status_update_time' => $cDate,'updated_by' =>$uid]);
            // return redirect()->back()->with('message', 'Invoice move to reject tab successfully');
            // return redirect('http://admin.rent.local/lms/invoice/backend_get_reject_invoice')->with('message', 'Invoice move to reject tab successfully');
          }
          else
          {
             //$this->invRepo->updateInvoice($row,$request->status);
            //$result = '';
            $invoice_id = $row;
            $invData = $this->invRepo->getInvoiceData(['invoice_id' => $invoice_id],['supplier_id','app_id']);        
            $supplier_id = isset($invData[0]) ? $invData[0]->supplier_id : null;
            $app_id = isset($invData[0]) ? $invData[0]->app_id : null;                                
            $isLimitExpired = InvoiceTrait::limitExpire($supplier_id, $app_id);
            $isLimitExceed = InvoiceTrait::isLimitExceed($invoice_id);
            if ($isLimitExpired || $isLimitExceed) {
                if ($isLimitExpired) {
                    $remark = 'Customer limit has been expired';
                } else {
                    $remark = 'Limit Exceed';
                }
                $attr=[];
                $attr['invoice_id'] = $invoice_id;
                $attr['status'] = 28;
                $attr['remark'] = $remark;
                $attr['invoice_id'] = $invoice_id;
                InvoiceTrait::updateInvoiceData($attr);
            } else {
                $this->invRepo->updateInvoice($row,$request->status);
            }
                         
           }
       }

        $whereActivi['activity_code'] = 'update_bulk_invoice';
        $activity = $this->masterRepo->getActivity($whereActivi);
        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Update bulk and Disburse Invoice (Pending, Approved Tab), Approve (Manage Invoice)';
            $arrActivity['app_id'] = null;
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
        }
        \DB::commit(); 
      return \response()->json(['status' => 1,'msg' => substr($result['msg'],0,-1) ,'responseType' => $result['responseType']]);
    } catch (Exception $ex) {
        \DB::rollback();
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
    } 
       
   }  
   /**
    * get Bank account list
    * 
    * @param DataProviderInterface $dataProvider
    * @return type mixed
    */
    
    public function getBankAccountList(DataProviderInterface $dataProvider)
    {
        return $dataProvider->getBankAccountList($this->request, $this->application->getBankAccountList());
    }
    
    
    /**
     * set default account
     * 
     * @param Request $request
     * @return mixed
     */
    public function setDefaultAccount(Request $request)
    {
        $acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
        $userId = $this->application->getUserIdByBankAccId($acc_id);
        $updateBankAccount = $this->application->updateBankAccount(['is_default' => 0], ['user_id' => $userId]);
        $res = $this->application->updateBankAccount(['is_default' => 1], ['bank_account_id' => $acc_id]);

        $whereActivi['activity_code'] = 'set_default_account';
        $activity = $this->masterRepo->getActivity($whereActivi);

        if(!empty($activity)) {
            $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
            $activity_desc = 'Set Default Bank';
            $arrActivity['app_id'] = null;
            $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['request' => $request->all(), 'userId' => $userId]), $arrActivity);
        }
                
        
        return \response()->json(['success' => $res]);
    }
    
    
    
    /**
     * get user by Role
     * 
     * @param Request $request
     * @return mixed
     */
    public function getUserByRole(Request $request)
    {
        $role_id = (int) $request->get('role_id');
        if (empty($role_id)) {
            abort(400);
        }
        //$checkPer =  (int) Helpers::checkPermissionAssigntoRole(104, $role_id);
        //if(!$checkPer){
        //    return \response()->json(['success' => false , 'messges'=>'For this role you do not have permission to Approve the application.']);
        //}
        
        $roleUsers = Helpers::getAllUsersByRoleId($role_id);
        return \response()->json(['data' => $roleUsers]);
    }

    public function getColenderList(DataProviderInterface $dataProvider)
    {
        $getColenderList = $this->userRepo->getColenderList();
      return $dataProvider->getColenderList($this->request, $getColenderList);
        
    }
    
    
    /**
     * Get disbursal list
     * 
     * @param DataProviderInterface $dataProvider
     * @return mixed
     */
    public function getDisbursalList(DataProviderInterface $dataProvider)
    {
     $getDisList = $this->lmsRepo->getBatchDisbursalList();
     return $dataProvider->getDisbursalList($this->request , $getDisList);   
    }

    /**
     * get Group company 
     * 
     * @param Request $request
     * @return mixed
     */
    public function getGroupCompany(Request $request ){
      
        $data = Group::select(['id','name'])
                ->where("name","LIKE","%{$request->input('query')}%")
                ->get();
    
       return response()->json($data);
    }

    // GST List
    public function getGstLists(DataProviderInterface $dataProvider) 
    {
        $products = $this->masterRepo->getAllGST();
        $data = $dataProvider->getAllGST($this->request, $products);
        return $data;
    }

    // Segments List
    public function getSegmentLists(DataProviderInterface $dataProvider) 
    {
        $segments = $this->masterRepo->getSegmentLists();
        $data = $dataProvider->getSegmentLists($this->request, $segments);
        return $data;
    }

    // Constitution List
    public function getConstitutionLists(DataProviderInterface $dataProvider) 
    {
        $products = $this->masterRepo->getAllConstitution();
        $data = $dataProvider->getAllConstitution($this->request, $products);
        return $data;
    }

    /**
     * Get all customer Address
     *
     * @return json customer Address data
     */
    public function addressGetCustomer(DataProviderInterface $dataProvider)
    {
        $user_id =   (int) $this->request->get('user_id');
        $latestApp = $this->application->getUpdatedApp($user_id);
        $bizId = $latestApp->biz_id ? $latestApp->biz_id : null;
        $customersList = $this->application->addressGetCustomers($user_id, $bizId, [0,6]);
        $users = $dataProvider->addressGetCustomers($this->request, $customersList);
        return $users;
    }

    public function setDefaultAddress(Request $request)
    {
        $acc_id = ($request->get('biz_addr_id')) ? \Crypt::decrypt($request->get('biz_addr_id')) : null;
        $this->application->setDefaultAddress(['is_default' => 0]);
        $res = $this->application->setDefaultAddress(['is_default' => 1], ['biz_addr_id' => $acc_id]);
        return \response()->json(['success' => $res]);
    }

    /**
   * Get all transactions for Colender soa
   *
   * @return json transaction data
   */
    public function getColenderSoaList(DataProviderInterface $dataProvider) {
        $soa_for_userid = $this->request->get('user_id');
        $transactionList = $this->lmsRepo->getColenderSoaList();
        $colenderShare = $this->lmsRepo->getColenderShareWithUserId($soa_for_userid);
        $users = $dataProvider->getColenderSoaList($this->request, $transactionList, $colenderShare);
        return $users;
    }

    /**
   * Get all transactions for soa
   *
   * @return json transaction data
   */
    public function lmsGetSoaList(DataProviderInterface $dataProvider) {
        ini_set("memory_limit", "-1");
        $request = $this->request;
        $transactionList = $this->lmsRepo->getSoaList();

        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $transactionList = $transactionList->where(function ($query) use ($request) {
                $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                $query->WhereBetween('value_date', [$from_date, $to_date]);
            });
        }

        if($request->has('trans_entry_type')){
            if($request->trans_entry_type != ''){
                $trans_entry_type = explode('_',$request->trans_entry_type);
                $trans_type = $trans_entry_type[0];
                $entry_type = $trans_entry_type[1];
                if($trans_type){
                    $transactionList = $transactionList->where('trans_type',$trans_type);
                }
                if($entry_type != ''){
                    $transactionList->whereHas('transaction', function($query) use($entry_type) {
                        $query->where('entry_type', $entry_type);
                    });
                }
            }
        }
        $transactionList = $transactionList->with('transaction.invoiceDisbursed.disbursal','transaction.payment','transaction.refundTrans.refundReq')->whereHas('lmsUser',function ($query) use ($request) {
            $customer_id = trim($request->get('customer_id')) ?? null ;
            $query->where('customer_id', '=', "$customer_id");
        })
        ->get();
        $users = $dataProvider->getSoaList($this->request, $transactionList);
        return $users;
    }
    
    /**
   * Get all transactions for Consolidated soa 
   *
   * @return json transaction data
   */
    public function lmsGetConsolidatedSoaList(DataProviderInterface $dataProvider) {

        $transactionList = $this->lmsRepo->getConsolidatedSoaList();
        $users = $dataProvider->getConsolidatedSoaList($this->request, $transactionList);
        return $users;
    }

    
     /**
   * Get all getInvoiceDueList
   *
   * @return json transaction data
   */
    public function getInvoiceDueList(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('d/m/Y');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('d/m/Y');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'customer_id' => $this->request->get('customer_id'),
            'type' => 'excel',
        ];
        $transactionList = $this->invRepo->getReportAllInvoice();
        $users = $dataProvider->getReportAllInvoice($this->request, $transactionList);
        $users     = $users->getData(true);
        $users['excelUrl'] = route('pdf_invoice_due_url', $condArr);
        $condArr['type']  = 'pdf';
        $users['pdfUrl'] = route('pdf_invoice_due_url', $condArr);
        return new JsonResponse($users);
    }
    
     /**
   * Get all getInvoiceOverDueList
   *
   * @return json transaction data
   */
    public function getInvoiceOverDueList(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('d/m/Y');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('d/m/Y');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'customer_id' => $this->request->get('customer_id'),
            'type' => 'excel',
        ];
        $transactionList = $this->invRepo->getReportAllOverdueInvoice();
        $users = $dataProvider->getReportAllOverdueInvoice($this->request, $transactionList);
        $users     = $users->getData(true);
        $users['excelUrl'] = route('pdf_invoice_over_due_url', $condArr);
        $condArr['type']  = 'pdf';
        $users['pdfUrl'] = route('pdf_invoice_over_due_url', $condArr);
        return new JsonResponse($users);
    }
    
   public function getInvoiceRealisationList(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('d/m/Y');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('d/m/Y');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'customer_id' => $this->request->get('customer_id'),
            'type' => 'excel',
        ];
        $transactionList = $this->invRepo->getInvoiceRealisationList();
        $users = $dataProvider->getInvoiceRealisationList($this->request, $transactionList);
        $users     = $users->getData(true);
        $users['excelUrl'] = route('pdf_invoice_realisation_url', $condArr);
        $condArr['type']  = 'pdf';
        $users['pdfUrl'] = route('pdf_invoice_realisation_url', $condArr);
        return new JsonResponse($users);
    }  
        /**
     * Get all Equipment
     *
     * @return json customer Address data
     */
    public function getEquipmentLists(DataProviderInterface $dataProvider)
    {
        $equipment = $this->masterRepo->getEquipments();
        $data = $dataProvider->getEquipments($this->request, $equipment);
        return $data;
    }

    /** 
     * @Author: Rent Alpha
     * @Date: 2020-02-18 10:49:29 
     * @Desc:  
     */    
    public function getTableValByField(Request $request)
    {
        $tableName = $request->get('tableName');
        $whereId = $request->get('whereId');
        $fieldVal = $request->get('fieldVal');
        $column = $request->get('column');
        $getFieldVal= Helpers::getTableVal($tableName, $whereId, $fieldVal); 
        $columnVal= ($getFieldVal) ? $getFieldVal->$column : false;
        echo $columnVal;
    }

    public function getTransTypeList(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllTransType();
        $this->providerResult = $dataProvider->getTransTypeListByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getJournalList(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllJournal();
        $this->providerResult = $dataProvider->getJournalByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getAccountList(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllAccount();
        $this->providerResult = $dataProvider->getAccountByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getVariableList(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllVariable();
        $this->providerResult = $dataProvider->getVariableByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getJeConfigList(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllJeConfig();
        $this->providerResult = $dataProvider->getJeConfigByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getJiConfigList(DataProviderInterface $dataProvider) { 
        $jeConfigId = request()->get('je_config_id');
        $this->dataRecords = $this->finRepo->getAllJiConfig($jeConfigId);
        $this->providerResult = $dataProvider->getJiConfigByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }


    public function getGroupCompanyExposure(Request $request ){
        $groupId = $request->get('groupid');
        $arrData = AppGroupDetail::where(['group_id' => $groupId,'status' => 1])->groupBy('borrower')->get();
        return response()->json($arrData);
    }

    //////////////////// Use For Payment Advice List /////////////////
    public function getPaymentAdvice(DataProviderInterface $dataProvider) 
    {
        $trans_data = $this->invRepo->getPaymentAdvice(); //getAllManualTransaction
        $trans_data = $dataProvider->getPaymentAdvice($this->request, $trans_data); //getAllManualTransaction
        return   $trans_data;
    } 
    

    ///* check duplicate invoice  ***///////
    function  checkDuplicateInvoice(Request $request)
    {
        $invoice_no  =  $request->invoice;
        $user_id  =  $request->user_id;
        $res =  $this->invRepo->checkDuplicateInvoice($invoice_no,$user_id);
        if($res)
        {
            return response()->json(['status' => 1]); 
        }
        else
        {
            return response()->json(['status' => 0]); 
        }
    }
    
    /**
     * Get all customer list
     *
     * @return json customer data
     */
    public function lmsGetRefundList(DataProviderInterface $dataProvider) {
      $refundList = $this->lmsRepo->getAllRefundLmsUser();
      $data = $dataProvider->lmsGetRefundCustomers($this->request, $refundList);
      return $data;
    }
    
    public function updateGroupCompanyExposure(Request $request){
        $group_company_expo_id = (int) $request->get('app_group_detail_id');
        $status = false;

        $appGrpDetail = AppGroupDetail::where("app_group_detail_id", $group_company_expo_id)->first();
        if ($appGrpDetail) {
            $status = $appGrpDetail->update(['is_deleted' => 1]);
            $status = $appGrpDetail->delete();
        }
        return response()->json($status);
    }

    public function lmsCreateBatch(DataProviderInterface $dataProvider){
        $refundList = $this->lmsRepo->getCreateBatchData($this->request);
        $data = $dataProvider->getCreateBatchData($this->request, $refundList);
        return $data;   
    }
    
    public function lmsEditBatch(DataProviderInterface $dataProvider){
        $refundList = $this->lmsRepo->getEditBatchData($this->request);
        $data = $dataProvider->getEditBatchData($this->request, $refundList);
        return $data;   
    }

    public function lmsGetRequestList(DataProviderInterface $dataProvider){
        $requestData = $this->lmsRepo->getRequestList($this->request);
        if(in_array($this->request->status,[7,8])){
            $data = $dataProvider->getApprovedRefundList($this->request, $requestData);
        }else{
            $data = $dataProvider->getRequestList($this->request, $requestData);
        }
        return $data;
    }
    
    public function getAllBaseRateList(DataProviderInterface $dataProvider) { 
     $baseRateList = $this->masterRepo->getAllBaseRateList();
//     dd($baseRateList);
     $baserates = $dataProvider->getBaseRateList($this->request, $baseRateList);
     return $baserates;
    }

    public function getColenderAppList(DataProviderInterface $dataProvider) {
        // $appList = $this->application->getColenderApplications();
        // $applications = $dataProvider->getColenderAppList($this->request, $appList);
        $customerList = $this->lmsRepo->getColenderApplications();
        $customers = $dataProvider->lmsColenderCustomers($this->request, $customerList);
        return $customers;
    }
    public function lmsGetInvoiceByUser(Request $request ){
        $userId = $request->get('user_id');
        $invoiceIds = $this->lmsRepo->getUserInvoiceIds($userId)->toArray();
        return response()->json($invoiceIds);
    }
    public function getBizAnchor(Request $request) {
        $attributes = $request->all();
        $get_user = $this->invRepo->getBizAnchor($attributes);
        return response()->json(['status' => 1, 'userList' => $get_user]);
    }
   public function getUserBizAnchor(Request $request) {
        $attributes = $request->all();
        $get_user = $this->invRepo->getUserBizAnchor($attributes);
        return response()->json(['status' => 1, 'userList' => $get_user]);
    } 
    
      /* get suplier & program b behalf of anchor id */

    public function getUserProgramSupplier(Request $request) {
        $attributes = $request->all();
        $get_user = $this->invRepo->getLmsUserBehalfApplication($attributes);
        return response()->json(['status' => 1, 'userList' => $get_user]);
    }

    
    /**
     * Get Repayment Amount
     * 
     * @param Request $request
     * @return mixed
     */
    public function getRepaymentAmount(Request $request)
    {
        $userId    = $request->get('user_id');
        $transType = $request->get('trans_type');
        $repaymentAmtData = $this->lmsRepo->getRepaymentAmount($userId, $transType);
        $repaymentAmtData = ((float)$repaymentAmtData<0)?0:$repaymentAmtData;
        return response()->json(['repayment_amount' => round($repaymentAmtData, 2)]);
    }
    
    ////////////*  get business */////
    public function searchBusiness(Request $request)
    {
      $result =   $this->lmsRepo->searchBusiness($request->search);    
      if(count( $result) > 0)
      {
         return response()->json(['status' => 1,'result' => $result]);
      }
      else
      {
           return response()->json(['status' => 0,'result' => $result]);
      }
    }

    public function getAjaxBankInvoice(Request $request, DataProviderInterface $dataProvider) { 
        $from_date    = $request->get('from_date');
        $to_date    = $request->get('to_date');
        $this->dataRecords = $this->invRepo->getAllBankInvoice($from_date, $to_date);
        $this->providerResult = $dataProvider->getBankInvoiceByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }


    public function getAjaxBankInvoiceCustomers(Request $request, DataProviderInterface $dataProvider) { 
        $batch_id    = $request->get('batch_id');
        $this->dataRecords = $this->invRepo->getAllBankInvoiceCustomers($batch_id);
        $this->providerResult = $dataProvider->getBankInvoiceCustomersByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }
    
    public function getAjaxViewDisburseInvoice(Request $request, DataProviderInterface $dataProvider) { 
        $batch_id = $request->get('batch_id');
        $disbursed_user_id = $request->get('disbursed_user_id');
        $this->dataRecords = $this->invRepo->getAllDisburseInvoice($batch_id, $disbursed_user_id);
        $this->providerResult = $dataProvider->getDisburseInvoiceByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getExistEmailStatus(Request $req){
        $response = [
            'status' => false
        ];
        $email = $req->get('email');
        $status = $this->userRepo->getUserByEmail(trim($email));
        
        if($status == false){
            $status1 = $this->userRepo->getExistEmailStatus(trim($email));
            if($status1 != false){
                $response['status'] = 'false';
            }else{
                $response['status'] = 'true';
            }
        }else{
           $response['status'] = 'false'; 
        }
        
        return response()->json( $response );
   }

    public function checkUniqueCharge(Request $request) 
    {        
        $chargeName = $request->get('chrg_name');
        $chargeId = $request->has('chrg_id') ? $request->get('chrg_id'): null ;
        $result = $this->lmsRepo->checkChargeName($chargeName, $chargeId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }

    // check email status of anchor
    public function getExistEmailStatusAnchor(Request $req){
        $response = [
            'status' => false,
            'message' => 'Some error occured. Please try again'
        ];
        $comp_email = $req->get('email');
        if (!filter_var($comp_email, FILTER_VALIDATE_EMAIL)) {
           $response['message'] =  'Email Id is not valid';
           return $response;
        }
        $status = $this->userRepo->getExistEmailStatusAnchor($comp_email);
        if($status != false){
           $response['status'] = false;
           $response['message'] =  'Sorry! Email is already in use.';
        }else{
            $response['status'] = true;
            $response['message'] =  '';
        }
        return $response;
    }

    // check email status of anchor
    public function getExistUserEmailStatusAnchor(Request $req){
        $response = [
            'status' => false,
            'message' => 'Some error occured. Please try again'
        ];
        $comp_email = $req->get('email');
        $anchor_id = $req->get('anchor_id');
        if (!filter_var($comp_email, FILTER_VALIDATE_EMAIL)) {
           $response['message'] =  'Email Id is not valid';
           return $response;
        }
        
        $status = $this->userRepo->getExistUserEmailStatusAnchor($anchor_id,$comp_email);
        if($status != false){
           $response['status'] = false;
           $response['message'] =  'Sorry! Email is already in use.';
        }else{
            $response['status'] = true;
            $response['message'] =  '';
        }
        return $response;
    }

    public function getSoaClientDetails(DataProviderInterface $dataProvider){
        $user_id = $this->request->get('user_id');
        $biz_id = $this->request->get('biz_id');

        $res = [
            'client_name' => '',
            'datetime' => \Helpers::convertDateTimeFormat(now(), 'Y-m-d H:i:s', 'j F, Y h:i A'),
            'address' => '',
            'currency' => 'INR',
            'limit_amt' => '',
            'prepayment' => '',
            'discount' => '',
        ];

        $bizAddress = $this->application->addressGetCustomers($user_id,$biz_id);
        $bizAddress = $bizAddress->first();
        if($bizAddress->count()>0){
            $res['address'] = $bizAddress['Address'].', '.$bizAddress['City'].', '.$bizAddress['State'].', Pin-'.$bizAddress['Pincode'];
        }

        $businessDetails = $this->userRepo->getBusinessDetails($biz_id);
        if($businessDetails->count()>0){
            $res['client_name'] = $businessDetails->biz_entity_name;
        }

        $res['limit_amt'] = $this->application->getTotalLimit($biz_id,1);

        return response()->json($res);
    }

    public function getRemainingCharges(Request $request){
        $user_id = $request->get('user_id');
        $trans_type = $request->get('trans_type');
        $charges = Transactions::getAllChargesApplied(['user_id' => $user_id,'trans_type'=>$trans_type]);
        $res = [];
        foreach($charges as $trans){
            $res[] = [
                'trans_date' => Carbon::parse($trans->trans_date)->format('d-m-Y'),
                'trans_id' => $trans->trans_id,
                'parent_trans_id' => $trans->parent_trans_id,
                'trans_name' => $trans->transName,
                'trans_desc' => $trans->transName,
                'user_id' => $trans->user_id,
                'entry_type' => $trans->entry_type,
                'debit_amount' => (float) $trans->amount,
                'credit_amount' => (float) ($trans->amount - $trans->outstanding),
                'remaining' => (float) $trans->outstanding,
                'tds_amount'=> (float) $trans->TDSAmount,
            ];
        }
        $data['result'] = $res;
        if (!empty($res)) {
            $data['status'] = 'success';
        }else{
            $data['status'] = 'empty';
        }
        return response()->json($data);
    }

    public function getInterestPaidAmount(Request $request){
        $user_id = $request->get('user_id');
        $trans_type = $request->get('trans_type');
        $interestPaid = Transactions::where('user_id','=',$user_id)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_PAID'))
        ->sum('amount');
        $interestDue = Transactions::where('user_id','=',$user_id)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->sum('amount');
        $data['amount'] = $interestDue-$interestPaid;
        if ($data['amount']>0) {
            $data['status'] = 'success';
        }else{
            $data['status'] = 'empty';
        }
        return response()->json($data);   
    }

    public function getAllUnsettledTransType(Request $request){
        $user_id = $request->get('user_id');
        $action_type = $request->get('action_type');
        $res = TransType::getAllUnsettledTransTypes(['user_id' => $user_id],$action_type);
        $data['result'] = $res;
        if (!empty($res)) {
            $data['status'] = 'success';
        }else{
            $data['status'] = 'empty';
        }
        return response()->json($data);
    }
    
    public function getVoucherLists(DataProviderInterface $dataProvider) {
         $vouchersList = $this->masterRepo->getAllVouchers();
         $vouchers = $dataProvider->getVouchersList($this->request, $vouchersList);
         return $vouchers;
    }

    public function getTransactions(DataProviderInterface $dataProvider) { 
        $latestBatchData = $this->finRepo->getLatestBatch();
        $latest_batch_no = NULL;
        if (!empty($latestBatchData)) {
            $latest_batch_no = $latestBatchData->batch_no;
        }
        $this->dataRecords = $this->finRepo->getTallyTxns(['batch_no' => $latest_batch_no]);
        $this->providerResult = $dataProvider->getTallyData($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getBatches(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getAllBatches();
        $this->providerResult = $dataProvider->getTallyBatchData($this->request, $this->dataRecords);
        return $this->providerResult;
    }
    
    public function checkAppliedCharge(Request $request) {
        $chargeId = $request->get('chrg_id');
        $chargeData = $this->lmsRepo->getChargeData(['charge_id' => $chargeId]);        
        $result = $chargeData && isset($chargeData[0]) ? 1 : 0;         
        return response()->json(['is_active' => $result]);         
    }

    public function getToSettlePayments(DataProviderInterface $dataProvider) {
        $user_id = $this->request->user_id;
        $dataRecords = Payment::whereIn('is_settled', [Payment::PAYMENT_SETTLED_PENDING,Payment::PAYMENT_SETTLED_PROCESSING,Payment::PAYMENT_SETTLED_PROCESSED])->orderBy('created_at', 'desc');
        if (!empty($user_id)) {
            $dataRecords->where('user_id', $user_id);
        }
        $this->providerResult = $dataProvider->getToSettlePayments($this->request, $dataRecords);
        return $this->providerResult;
    }

    public function getSettledPayments(DataProviderInterface $dataProvider) {
        $user_id = $this->request->user_id;
        $dataRecords = [];
        if (!empty($user_id)) {
            $dataRecords = Payment::getPayments(['is_settled' => 1, 'user_id' => $user_id],['updated_at'=>'desc']);
        } else {
            $dataRecords = Payment::getPayments(['is_settled' => 1],['updated_at'=>'desc']);
        }
        return $dataProvider->getToSettlePayments($this->request, $dataRecords);
    }
    
    public function checkBankAccExist(Request $req){
        
        $response['status'] = false;
        $acc_no = $req->get('acc_no');
        $comp_id = (int)\Crypt::decrypt($req->get('comp_id'));
        $acc_id = $req->get('acc_id');
        $status = $this->application->getBankAccByCompany(['acc_no' => $acc_no, 'comp_addr_id' => $comp_id]);
       if($status == false){
                $response['status'] = 'true';
        }else{
           $response['status'] = 'false';
           if($acc_id != null){
               $response['status'] = 'true';
           }
        }
        
        return response()->json( $response );
   }
   
   public function checkCompAddExist(Request $req){
        
        $response['status'] = false;
        $cmp_name = $req->get('cmp_name');
        $comp_add = $req->get('comp_add');
        $comp_id = $req->get('comp_id');
//        dd($comp_name, $comp_add, $comp_id);
        $status = $this->masterRepo->getCompAddByCompanyName(['cmp_name' => $cmp_name, 'cmp_add' => $comp_add]);
//        dd($status);
       if($status == false){
                $response['status'] = 'true';
        }else{
           $response['status'] = 'false';
           if($comp_id != null){
               $response['status'] = 'true';
           }
        }
        
        return response()->json( $response );
   }

    // get user invoice list
    public function getUserInvoiceList(DataProviderInterface $dataProvider) {
        $user_id =  (int) $this->request->get('user_id');
        $userInvoice = $this->UserInvRepo->getUserInvoiceList($user_id);
        $data = $dataProvider->getUserInvoiceList($this->request, $userInvoice);
        return $data;
    }

    // get user invoice list
    public function getCustAndCapsLoc(DataProviderInterface $dataProvider) {
        $user_id =  (int) $this->request->get('user_id');
        $cusCapLoc = $this->UserInvRepo->getCustAndCapsLoc($user_id);
        $data = $dataProvider->getCustAndCapsLoc($this->request, $cusCapLoc);
        return $data;
    }
    
    public function getRenewalAppList(DataProviderInterface $dataProvider) {
        $appList = $this->application->getAllRenewalApps();
        $applications = $dataProvider->getRenewalAppList($this->request, $appList);
        return $applications;
    }

    
    public function checkEodProcess(Request $request)
    {
        $data = ['eod_process' => \Helpers::checkEodProcess()];
        $response = $data + ['message' => trans('backend_messages.lms_eod_process_msg')];
        return response()->json($response);  
    }

    public function updateEodProcessStatus(Request $request)
    { 
        $eod_process_id = $request->eod_process_id;

        if(!\Helpers::getInterestAccrualCronStatus()){
            return response()->json(['status' => 3 , 'message'=>'Interest Accrual has not been calculated till date.']);
        }
        if(\Helpers::getEodProcessCronStatus()){
            return response()->json(['status' => 4 , 'message'=>'EOD is already run today.']);
        }
        if($eod_process_id){
            if(\App::make('App\Http\Controllers\Lms\EodProcessController')->process($eod_process_id)){
                return response()->json(['status' => 1, 'message'=>'Eod completed successfully!']);
            }
            return response()->json(['status' => 2, 'message'=>'Eod process failed!']);
        }
        return response()->json(['status' => 0, 'message'=>'Eod detail missing! Please try again!']);
    }    

    public function startEodSystem(Request $request){
        $eod_process_id = $request->eod_process_id;

        if($eod_process_id){
            if(\App::make('App\Http\Controllers\Lms\EodProcessController')->startSystem($eod_process_id)){
                return response()->json(['status' => 1, 'message'=>'System Start successfully!']);
            }
            return response()->json(['status' => 2, 'message'=>'System Start process failed!']);
        }
        return response()->json(['status' => 0, 'message'=>'Eod detail missing! Please try again!']);
    }

    public function getAllCustomers(DataProviderInterface $dataProvider) {
        $usersList = $this->userRepo->getAllUsers();
        $customers = $dataProvider->getAllCustomers($this->request, $usersList);
        return $customers;  
    }

    public function leaseRegister(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('Y-m-d 00:00:00');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d 23:59:59');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'type' => 'excel',
        ];
        $leaseRegistersList = $this->reportsRepo->leaseRegisters();
        $leaseRegisters = $dataProvider->leaseRegister($this->request, $leaseRegistersList);
        $leaseRegisters = $leaseRegisters->getData(true);
        $leaseRegisters['excelUrl'] = route('download_reports', $condArr);
        $condArr['type']  = 'pdf';
        $leaseRegisters['pdfUrl'] = route('download_reports', $condArr);
        return new JsonResponse($leaseRegisters);
    }    

    public function interestBreakup(DataProviderInterface $dataProvider){
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('Y-m-d 00:00:00');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d 23:59:59');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
        ];

        $interestBreakupList = $this->reportsRepo->getInterestBreakupReport($condArr);
        $interestBreakup = $dataProvider->interestBreakup($this->request, $interestBreakupList);
        $interestBreakup = $interestBreakup->getData(true);

        $condArr['type']  = 'excel';
        $interestBreakup['excelUrl'] = route('download_interest_breakup', $condArr);
        
        return new JsonResponse($interestBreakup);
    }

    public function chargeBreakup(DataProviderInterface $dataProvider){
        $rowWhere = null;
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('Y-m-d 00:00:00');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d 23:59:59');
            $rowWhere = "trans_date between '".$from_date."' AND '". $to_date."'";
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
        ];
        
        $interestBreakupList = $this->reportsRepo->getChargeBreakupReport([], $rowWhere);
        $interestBreakup = $dataProvider->chargeBreakup($this->request, $interestBreakupList);
        $interestBreakup = $interestBreakup->getData(true);

        $condArr['type']  = 'excel';
        $interestBreakup['excelUrl'] = route('download_charge_breakup', $condArr);
        
        return new JsonResponse($interestBreakup);
    }

    public function tdsBreakup(DataProviderInterface $dataProvider){
        $rowWhere = null;
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('Y-m-d 00:00:00');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d 23:59:59');
            $rowWhere = "trans_date between '".$from_date."' AND '". $to_date."'";
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
        ];
        
        $interestBreakupList = $this->reportsRepo->gettdsBreakupReport([], $rowWhere);
        $interestBreakup = $dataProvider->tdsBreakup($this->request, $interestBreakupList);
        $interestBreakup = $interestBreakup->getData(true);

        $condArr['type']  = 'excel';
        $interestBreakup['excelUrl'] = route('download_tds_breakup', $condArr);
        
        return new JsonResponse($interestBreakup);
    }

    public function unsettledPayments(Request $request){
        $userId = $request->user_id;
        $chrgId = $request->chrg_id;
        $paymentType = config('lms.CHARGE_PAYMENT_TYPE_MAP.'.$chrgId);
        $dataRecords = [];
        if ($userId) {
            $payments = Payment::getPayments(['is_settled' => 0, 'user_id' => $userId, 'payment_type' => $paymentType]);
            $payments = $payments->get();
            foreach ($payments as $payment) {
                $dataRecords[] =[
                    'id'=>Crypt::encryptString($payment->payment_id),
                    'amount'=>number_format($payment->amount),
                    'paymentmode'=>$payment->paymentmode,
                    'transactionno'=>$payment->transactionno,
                    'date_of_payment'=>Carbon::parse($payment->date_of_payment)->format('d-m-Y')
                ];
            }
        }
        if(!empty($dataRecords)){
            return response()->json(['status' => 1,'res' => $dataRecords]);
        }else{
            return response()->json(['status' => 0,'res' => $dataRecords]);
        }
        
    }

    public function getEodList(DataProviderInterface $dataProvider){
        $eodList = $this->lmsRepo->getEodList();
        $eod = $dataProvider->getEodList($this->request, $eodList);
        return $eod;
    }

    public function getEodProcessList(Request $request){
        $eod_process_id = $request->eod_process_id;
        $eodLog = $this->lmsRepo->getEodProcessLog(['eod_process_id'=>$eod_process_id]);
        if($eodLog){
            $icon0 = '';
            $icon1 = '<i class="fa fa-check" title="Pass" style="color:green" aria-hidden="true"></i>';
            $icon2 = '<i class="fa fa-times" title="Fail" style="color:red" aria-hidden="true"></i>';

            $html = '<table class="table  table-td-right"> <tbody> <tr> <td class="text-left" width="30%"><b>Tally Posting Status</b></td> <td>'.${'icon'.$eodLog->tally_status}.'</td> <td class="text-left" width="30%"><b>Interest Accrual Status</b></td> <td>'.${'icon'.$eodLog->int_accrual_status}.'</td> </tr> <tr> <td class="text-left" width="30%"><b>Repayment Status</b></td> <td>'.${'icon'.$eodLog->repayment_status}.'</td> <td class="text-left" width="30%"><b>Disbursal Status</b></td> <td>'.${'icon'.$eodLog->disbursal_status}.'</td> </tr> <tr> <td class="text-left" width="30%"><b>Charge Posting Status</b></td> <td>'.${'icon'.$eodLog->charge_post_status}.'</td> <td class="text-left" width="30%"><b>Overdue Interest Accrual Status</b></td> <td>'.${'icon'.$eodLog->overdue_int_accrual_status}.'</td> </tr> <tr> <td class="text-left" width="30%"><b>Disbursal Block Status</b></td> <td>'.${'icon'.$eodLog->disbursal_block_status}.'</td> <td class="text-left" width="30%"><b>Manually Posted Running Transaction Status</b></td> <td>'.${'icon'.$eodLog->is_running_trans_settled}.'</td> </tr> </tbody> </table>';
        }else{
            $html = "EOD report not Found.";
        }

        return new JsonResponse(['html'=>$html]);
    }
    public function checkExistAnchorLead(Request $request)
    {
        $email = $request->get('email'); 
        $data = $request->all();       
        $assocAnchId = $request->get('anchor_id');
        $result = [];
        $result['message'] = '';
        $result['status'] = true;        
        
        //$getAnchorId = $this->userRepo->getUserDetail(Auth::user()->user_id);
        //if ($getAnchorId && $getAnchorId->anchor_id!=''){
        if (!empty($assocAnchId)) {
            $anchorId = $assocAnchId;
        } else {
            $anchorId = Auth::user()->anchor_id;
        }
        
        if (!empty($anchorId)) {
            $whereCond=[];
            $whereCond[] = ['email', '=', trim($email)];
            $whereCond[] = ['anchor_id', '=', $anchorId];
            //$whereCond[] = ['is_registered', '!=', '1'];
            $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
            $Anchorstatus = $this->userRepo->getExistEmailStatusAnchor(trim($email));
            if (!empty($anchUserData->toArray())) {
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
            }else{

                if($Anchorstatus != false){
                    $result['status'] = false;
                    $result['message'] = trans('success_messages.existing_email');
                }
            }

        }else{
            $whereCond=[];
            $whereCond[] = ['email', '=', trim($email)];
            if(isset($data['anchor_user_id'])){
                $whereCond[] = ['anchor_user_id', '!=', $data['anchor_user_id']];
            }
            
            $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
            $Anchorstatus = $this->userRepo->getExistEmailStatusAnchor(trim($email));
            if(!empty($anchUserData->toArray())){
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
            }elseif($Anchorstatus != false){
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
            }else {
                $userData = $this->userRepo->getBackendUserByEmail(trim($email));
                if ($userData) {
                    $result['status'] = false;
                    $result['message'] = trans('success_messages.existing_email');
                }
            }
        }
        return response()->json($result);
    }    

    public function checkBankAccWithIfscExist(Request $req){
        
        $response['status'] = false;
        $acc_no = trim($req->get('acc_no'));
        $ifsc_code = trim($req->get('ifsc'));
        $acc_id = $req->get('acc_id');
        $status = $this->application->getBankAccByCompany(['acc_no' => $acc_no, 'ifsc_code' => $ifsc_code]);
       if($status == false){
                $response['status'] = 'true';
        }else{
           $response['status'] = 'false';
           if($acc_id != null){
               $response['status'] = 'true';
           }
        }
        
        return response()->json( $response );
   }

    public function getCibilReportLms(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('Y-m-d 00:00:00');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d 23:59:59');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'search_keyword' => $this->request->get('search_keyword'),
            'type' => 'excel',
        ];
        $cibilReports = $this->lmsRepo->getCibilReports();
        $reportsList = $dataProvider->getCibilReportLms($this->request, $cibilReports);
        $reportsList     = $reportsList->getData(true);
        $reportsList['excelUrl'] = route('download_lms_cibil_reports', $condArr);
        $condArr['type']  = 'pdf';
        $reportsList['pdfUrl'] = route('download_lms_cibil_reports', $condArr);
        return new JsonResponse($reportsList);
    }
    
    /**
     * Get all TDS
     * 
     * @param DataProviderInterface $dataProvider
     * @return JsonResponse
     */
    public function Tds(DataProviderInterface $dataProvider) {
        $condArr = [
            'user_id' => $this->request->get('user_id'),
            'type' => 'excel',
        ];
        $tdsList = $this->reportsRepo->tds();
        $tds = $dataProvider->tds($this->request, $tdsList);
        $tds = $tds->getData(true);
        $tds['excelUrl'] = route('tds_download_reports', $condArr);
        $condArr['type']  = 'pdf';
        $tds['pdfUrl'] = route('tds_download_reports', $condArr);
        return new JsonResponse($tds);
    }


     /**
     * change Agency User status
     *
     * @param Request $request
     * @return type mixed
     */
    public function changeAgencyStatus(Request $request)
    {
        $agency_id = $request->get('agency_id');
        $is_active = $request->get('is_active');
        $result = $this->userRepo->updateAgencyStatus(['is_active' => $is_active], ['agency_id' => $agency_id]);
        return \Response::json(['success' => $result]);
    }

        
    /**
     * change Agency User status
     * 
     * @param Request $request
     * @return type mixed
     */
    public function changeUsersAgencyStatus(Request $request)
    {
        $user_id = $request->get('user_id');
        $is_active = $request->get('is_active');
        $result = $this->userRepo->updateUserStatus(['is_active' => $is_active], ['user_id' => $user_id]);
        return \Response::json(['success' => $result]);
    }

    // TDS List in master
    public function getTDSList(DataProviderInterface $dataProvider) 
    {
        $tdsList = $this->masterRepo->getTDSLists();
        $data = $dataProvider->getTDSLists($this->request, $tdsList);
        return $data;
    }

    // borrower limit List in master
    public function getLimitList(DataProviderInterface $dataProvider) 
    {
        $limitList = $this->masterRepo->getAllLimit();
        $data = $dataProvider->getAllLimit($this->request, $limitList);
        return $data;
    }

    public function expirePastLimit(Request $request){

        $expirePastLimit = $this->masterRepo->expirePastLimit();
        if($expirePastLimit){
            $response['status'] = 1;
            $response['message'] = 'Limit expired successfully.';
            return json_encode($response);
        }else{
            $response['status'] = 0;
            $response['message'] = 'Something went wrong!';
            return json_encode($response);
        }
        
    }

    public function getBackendDisbursalBatchRequest(DataProviderInterface $dataProvider) {
        $disbursalBatchRequest = $this->lmsRepo->lmsGetDisbursalBatchRequest();
        $data = $dataProvider->lmsGetDisbursalBatchRequest($this->request, $disbursalBatchRequest);
        return $data;
    } 
    
    public function getBackendRefundBatchRequest(DataProviderInterface $dataProvider) {
        $disbursalBatchRequest = $this->lmsRepo->lmsGetRefundBatchRequest();
        $data = $dataProvider->lmsGetRefundBatchRequest($this->request, $disbursalBatchRequest);
        return $data;
    }
    
    /**
     * Get all NACH Request
     * 
     * @param DataProviderInterface $dataProvider
     * @return JsonResponse
     */
    public function getAllNach(DataProviderInterface $dataProvider) {
        $whereCond = [2,3];
        $nachList = $this->lmsRepo->getAllNach($whereCond);
        $nach = $dataProvider->getNach($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function frontAjaxUserNachList(DataProviderInterface $dataProvider) {
        $whereCondition = ['user_id' => Auth::user()->user_id];
        $nachList = $this->application->getUserNACH($whereCondition);
        $nach = $dataProvider->getUserNACH($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function anchorAjaxUserNachList(DataProviderInterface $dataProvider) {
        $whereCondition = ['user_id' => Auth::user()->user_id];
        $nachList = $this->application->getUserNACH($whereCondition);
        $nach = $dataProvider->getAnchorUserNACH($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function backendAjaxUserNachList(DataProviderInterface $dataProvider) {
        $whereCondition = [];
        $nachList = $this->application->getUserNACH($whereCondition);
        $nach = $dataProvider->getBackendUserNACH($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function backendNachUserList(Request $request){
        $roleType = $request->role_type;
        if ($roleType) {
            $users = $this->application->getNachUserList($roleType);
            
        }
        if(!empty($users)){
            return response()->json(['status' => 1,'users' => $users]);
        }else{
            return response()->json(['status' => 0,'users' => $users]);
        }
        
    }

    public function backendNachUserBankList(Request $request){
        $userId = $request->customer_id;
        $roleType = $request->role_type;
        if ($userId) {
            if($roleType == 3) {
                $userData = $this->userRepo->getCustomerDetail($userId);
                $BankList = $this->application->getUserBankNACH(['anchor_id' => $userData->anchor_id]);
            } else {
                $BankList = $this->application->getUserBankNACH(['user_id' => $userId]);

            }
        }
        if(!empty($BankList)){
            return response()->json(['status' => 1,'BankList' => $BankList]);
        }else{
            return response()->json(['status' => 0,'BankList' => $BankList]);
        }
        
    }

    public function lmsGetNachRepaymentList(DataProviderInterface $dataProvider) {
        $whereCondition = ['is_active' => 1, 'nach_status' => 4];
        $nachList = $this->application->getUserRepaymentNACH($whereCondition);
        foreach ($nachList as $key => $value) {
            $value->outstandingAmt = number_format($this->lmsRepo->getNACHUnsettledTrans($value->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);

            $value->ids = [];
            $transAr = [];
            foreach ($this->lmsRepo->getNACHUnsettledTrans($value->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ]) as $key1 => $value1) {
                $transArray['trans_id'] = $value1->trans_id;
                $transArray['amount'] = $value1->outstanding;
                array_push($transAr, $transArray);

            }
            $value->ids = $transAr;
            if ($value->outstandingAmt == 0.00) {
                $nachList->forget($key);
            }
        }
        $nach = $dataProvider->getNachRepaymentList($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }
    
    public function lmsGetNachRepaymentTransList(DataProviderInterface $dataProvider) {
        $whereCondition = ['is_active' => 1, 'nach_status' => 7];
        $nachList = $this->application->getUserNACH($whereCondition);
        $nach = $dataProvider->getNachRepaymentTransList($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function backendAjaxNachSTBList(DataProviderInterface $dataProvider) {
        $whereCondition = [];
        $nachList = $this->lmsRepo->getNachRepaymentReq($whereCondition);
        $nach = $dataProvider->getNachRepaymentReq($this->request, $nachList);
        $nach = $nach->getData(true);
        return new JsonResponse($nach);
    }

    public function getAllBankList(DataProviderInterface $dataProvider) { 
        $bankList = $this->masterRepo->getAllBankList();
        $banks = $dataProvider->getBankList($this->request, $bankList);
        return $banks;
    }
    public function chkAnchorPhyInvReq(Request $request) {
        $anchorId = $request->get('anchorID');
        $getAnchor = $this->userRepo->getAnchorById($anchorId);
        if(isset($getAnchor->is_phy_inv_req) && $getAnchor->is_phy_inv_req === '1') {
            return $respose = ['status'=>'1'];
        } else {
            return $respose = ['status'=>'0'];
        }
    }

    public function chkAnchorPhyBlkInvReq(Request $request) {
        $anchorId = $request->get('anchorID');
        $getAnchor = $this->userRepo->getAnchorById($anchorId);
        if(isset($getAnchor->is_phy_blk_inv_req) && $getAnchor->is_phy_blk_inv_req === '1') {
            return $respose = ['status'=>'1'];
        } else {
            return $respose = ['status'=>'0'];
        }
    }

    // Check frontend PAN validation
    public function checkAnchorPanAjax(Request $request) {
        $data = $request->all();

        $anchrUserDataByPan = $this->userRepo->getAnchorByPan($data['pan_no']);
        $anchorDataByPan = $this->userRepo->getAnchorData(['pan_no' => $data['pan_no']]);
        
        // if($anchrUserDataByPan) {
        //     return 'false';
        // } 
        if(count($anchorDataByPan) > 0) {
            return 'false';
        } else {
            return 'true';
        }
    }    

    // Check frontend GST validation
    public function checkAnchorGstAjax(Request $request) {
        $data = $request->all();
        
        $anchorDataByPan = $this->userRepo->getAnchorData(['gst_no' => $data['gst_no']]);
        if(count($anchorDataByPan) > 0) {
            return 'false';
        } else {
            return 'true';
        }
    }    

    public function backendGetInvoiceProcessingGstAmount(Request $request) {
        $invoiceId = $request->get('invoice_id');
        $typeFlag = $request->get('chrg_type');
        $valueAmt = $request->get('chrg_value');
        $invoiceData = $this->invRepo->getInvoiceById($invoiceId);
        $chargeData = $this->invRepo->getInvoiceProcessingFee(['invoice_id' =>$invoiceId]);
        // $offerData = $this->appRepo->getOfferData(['prgm_offer_id' =>$invoiceData->prgm_offer_id]);
        $chrgData = $this->application->getInvoiceProcessingFeeCharge();
        $getPercentage  = $this->lmsRepo->getLastGSTRecord();

        $tax_value  =0;
        $marginAmt = $this->calMargin($invoiceData->invoice_approve_amount, $invoiceData->program_offer->margin);
        $principleAmt = $invoiceData->invoice_approve_amount - $marginAmt;
        if (isset($typeFlag) && $typeFlag == 2) {
            $processingFee = $this->calPercentage($principleAmt, $valueAmt);
        } else {
            $processingFee = $valueAmt;
        }

        if($chrgData->is_gst_applicable == 1) {
            if($getPercentage)
            {
                $tax_value  = $getPercentage['tax_value'];
            }
            else
            {
                $tax_value  =0; 
            }
        }

        $fWGst = round((($processingFee*$tax_value)/100),2);
        $gstChrgValue = round($processingFee + $fWGst,2);
        return new JsonResponse(
            [
                'gstChrgValue' => $gstChrgValue,
                'processingFee' => $processingFee,
                'fWGst' => $fWGst
            ]);
        // return [
        // 'status' => 
        // 'gstChrgValue' => $gstChrgValue];
    }
    // Manage Document
    public function checkDocumentNametAjax(Request $request) {
        $data = $request->all();
        $where = [
            'doc_name' => $data['doc_name'],
        ];
        $checkDocName = $this->masterRepo->checkDocumentExist($where); 
        if($checkDocName > 0) {
            return 'false';
        } else {
            return 'true';
        }      
    }

    public function checkDocumentNameEdittAjax(Request $request) {
        $data = $request->all();
        $where = [
            'doc_name' => $data['doc_name'],
        ];
        $id = [
            'id' => $data['id']
        ];
        $checkDocName = $this->masterRepo->checkDocumentExistEditCase($where, $id); 
        if($checkDocName > 0) {
            return 'false';
        } else {
            return 'true';
        }      
    }

    // Manage DOA Level
    public function checkDOANametAjax(Request $request) {
        $data = $request->all();
        $where = [
            'level_name' => $data['level_name']
        ];
        $doaCheckNameExists = $this->masterRepo->getDoaNameExists($where);
        if($doaCheckNameExists > 0) {
            return 'false';
        } else {
            return 'true';
        }      
    }

    public function checkDOANametEditAjax(Request $request) {
        $data = $request->all();
        $where = [
            'level_name' => $data['doa_name']
        ];
        $doa_id = [
            'doa_level_id' => $data['doa_id']
        ];  
        $doaCheckNameExists = $this->masterRepo->getDoaNameEditCaseExists($where, $doa_id);
        if($doaCheckNameExists > 0) {
            return 'false';
        } else {
            return 'true';
        }      
    }
    
    // Check Unique Industry
    public function checkUniqueIndustries(Request $request) 
    {        
        $IndustryName = $request->get('name');
        $industryId = $request->has('industry_id') ? $request->get('industry_id'): null ;
        $result = $this->masterRepo->checkIndustryName(['name' => $IndustryName], $industryId);
        dd($result);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Industry code
    public function checkUniqueIndustriesCode(Request $request) 
    {        
        $IndustryName = $request->get('cibil_indus_code');
        $industryId = $request->has('industry_id') ? $request->get('industry_id'): null ;
        $result = $this->masterRepo->checkIndustryName(['cibil_indus_code' => $IndustryName], $industryId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Voucher
    public function checkUniqueVoucher(Request $request) 
    {        
        $voucherName = $request->get('voucher_name');
        $result = $this->masterRepo->checkVoucherName($voucherName);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Segment
    public function checkUniqueSegment(Request $request) 
    {        
        $segmentName = $request->get('name');
        $segmentId = $request->has('id') ? $request->get('id'): null ;
        $result = $this->masterRepo->checkSegmentName($segmentName, $segmentId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Entity
    public function checkUniqueEntity(Request $request) 
    {        
        $entityName = $request->get('entity_name');
        $entitytId = $request->has('id') ? $request->get('id'): null ;
        $result = $this->masterRepo->checkEntityName($entityName, $entitytId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Consitution
    public function checkUniqueConstitution(Request $request) 
    {        
        $constiName = $request->get('name');
        $constitId = $request->has('id') ? $request->get('id'): null ;
        $result = $this->masterRepo->checkConsitutionName(['name' => $constiName], $constitId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Consitution Code
    public function checkUniqueConstitutionCode(Request $request) 
    {        
        $constiName = $request->get('cibil_lc_code');
        $constitId = $request->has('id') ? $request->get('id'): null ;
        $result = $this->masterRepo->checkConsitutionName(['cibil_lc_code' => $constiName], $constitId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Equipment
    public function checkUniqueEquipment(Request $request) 
    {        
        $equipmentName = $request->get('equipment_name');
        $equipmentId = $request->has('id') ? $request->get('id'): null ;
        $result = $this->masterRepo->checkEquipmentName($equipmentName, $equipmentId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }    
    
    // Check Unique Bank name
    public function checkUniqueBankMaster(Request $request) 
    {        
        $bankName = $request->get('bank_name');
        $banktId = $request->has('bank_id') ? \Crypt::decrypt($request->get('bank_id')) : null ;
        $result = $this->masterRepo->checkBankName($bankName, $banktId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }
    
    // Check Unique Entity
    public function checkUniqueTdsCertificate(Request $request) 
    {        
        $tdsCertificate = $request->get('tds_certificate_no');
        $id = $request->has('payment_id') ? $request->get('payment_id') : null ;
        $result =  Payment::checkTdsCertificate($tdsCertificate, $id);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }

    public function getTDSOutstatingAmount(Request $request)
    {
        $userId    = $request->get('user_id');
        $whereCon = $request->filled('payment_date') ? ['due_date' => Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d')] : [];
        $TDSOutstating = $this->lmsRepo->getTDSOutstatingAmount($userId, $whereCon);
        $TDSOutstating = ((float)$TDSOutstating<0) ? 0 : $TDSOutstating;
        return response()->json(['tds_amount' => round($TDSOutstating, 2)]);
    }

    public function getLocationTypeLists(DataProviderInterface $dataProvider) { 
     $industriesList = $this->masterRepo->getAllLocationType();
     $industries = $dataProvider->getLocationTypeLists($this->request, $industriesList);
     return $industries;
    } 
    
    // Check Unique Location
    public function checkUniqueLocationType(Request $request) 
    {        
        $locationType = $request->get('name');
        $locationId = $request->has('location_id') ? $request->get('location_id'): null ;
        $result = $this->masterRepo->checkLocationType(['name' => $locationType], $locationId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }      
    // Check Unique Location Code
    public function checkUniqueLocationCode(Request $request) 
    {        
        $locationType = $request->get('location_code');
        $locationId = $request->has('location_id') ? $request->get('location_id'): null ;
        $result = $this->masterRepo->checkLocationType(['location_code' => $locationType], $locationId);
        
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }
    
    public function frontAjaxUserSoaConsolidatedList(DataProviderInterface $dataProvider)
    {
        $request = $this->request;
        $transactionList = $this->lmsRepo->getSoaList();

        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $transactionList = $transactionList->where(function ($query) use ($request) {
                $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                $query->WhereBetween('value_date', [$from_date, $to_date]);
            });
        }

        if($request->has('trans_entry_type')){
            if($request->trans_entry_type != ''){
                $trans_entry_type = explode('_',$request->trans_entry_type);
                $trans_type = $trans_entry_type[0];
                $entry_type = $trans_entry_type[1];
                if($trans_type){
                    $transactionList = $transactionList->where('trans_type',$trans_type);
                }
            }
        }
        $transactionList = $transactionList->with('transaction.invoiceDisbursed.disbursal','transaction.payment')->whereHas('lmsUser',function ($query) use ($request) {
            $customer_id = trim($request->get('customer_id')) ?? null ;
            $query->where('customer_id', '=', "$customer_id");
        })
        ->get();

        $users = $dataProvider->getFrontSoaConsolidatedList($this->request, $transactionList);
        return $users;
    }

    public function frontAjaxUserSoaList(DataProviderInterface $dataProvider)
    {
        $request = $this->request;
        $transactionList = $this->lmsRepo->getConsolidatedSoaList();
        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $transactionList = $transactionList->where(function ($query) use ($request) {
                $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                $query->WhereBetween('value_date', [$from_date, $to_date]);
            });
        }

        if($request->has('trans_entry_type')){
            if($request->trans_entry_type != ''){
                $trans_entry_type = explode('_',$request->trans_entry_type);
                $trans_type = $trans_entry_type[0];
                $entry_type = $trans_entry_type[1];
                if($trans_type){
                    $transactionList = $transactionList->where('trans_type', $trans_type);
                }
            }
        }

        $transactionList = $transactionList->with('transaction.invoiceDisbursed.disbursal','transaction.payment')->whereHas('lmsUser',function ($query) use ($request) {
            $customer_id = trim($request->get('customer_id')) ?? null ;
            $query->where('customer_id', '=', "$customer_id");
        })
        ->get();
        $users = $dataProvider->getFrontSoaList($this->request, $transactionList);
        return $users;
    }

    /**
   * send overdue report by mail and get all reports
   *
   * @return json transaction data
   */
    public function sendInvoiceOverdueReportByMail(DataProviderInterface $dataProvider)
    {
        if ($this->request->get('to_date')) {
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d');
            $userId  = $this->request->get('user_id') ?? 'all';
            \Artisan::call("report:overdueManual", ['user' => $userId, 'date' => $to_date]);
        }
    
        $overdueReportLogs = OverdueReportLog::orderBy('id','desc')->get();
        return $dataProvider->getOverdueReportLogs($this->request, $overdueReportLogs);
    }
    
    public function reqForChargeDeletion(Request $request)
    {
        try {
            $request->validate([
                'chrg_id' => 'required'
            ],['chrg_id.required' => 'Please select atleast one checked']);

            $attr = is_array($request->chrg_id) && count($request->chrg_id) ? $request->chrg_id : [$request->chrg_id];
            $chrgTrans = ChargesTransactions::whereIn('chrg_trans_id', $attr)->get();

            if (count($chrgTrans) > 1) {
                $chrgTransIds = $chrgTrans->pluck('trans_id')->toArray();
                $valResult = Helpers::validateInvoiceTypes($chrgTransIds, $specificMsg = false);
                if ($valResult && isset($valResult['status']) && $valResult['status'] == false) {
                    return response()->json(['status' => 0,'msg' => $valResult['message']]);
                }
            }

            \DB::beginTransaction();
            foreach($chrgTrans as $chrgTran) {

                if ($chrgTran->transaction && $chrgTran->transaction->amount != $chrgTran->transaction->outstanding) {
                    \DB::rollback();
                    return response()->json(['status' => 0,'msg' => "Selected Charges can't be requested for cancellation because charge transaction is already settled."]);
                }

                $query  = ChargeTransactionDeleteLog::where('chrg_trans_id', $chrgTran->chrg_trans_id);
                $newQuery = clone $query;
                $isExistChrgTranReqLog     = $query->reqForDeletion()->first();
                $isExistChrgTranApproveLog = $newQuery->approveForDeletion()->first();
                
                if (!$isExistChrgTranReqLog && !$isExistChrgTranApproveLog) {
                    $attr = [
                        'chrg_trans_id' => $chrgTran->chrg_trans_id,
                        'status'        => 1,
                        'created_at'    => now(),
                        'created_by'    => auth()->user()->user_id
                    ];
    
                    $this->lmsRepo->saveChargeTransDeleteLog($attr);
                }
            }
            $roles = $this->userRepo->getActiveChrgDeleteEmailAllowedRoles();
            $sendEmailRoleIds = [];
            foreach($roles as $role) {
                $isPermission = Permission::checkRolePermission('lms_approve_chrg_deletion', $role->id);
                if ($isPermission && !in_array($role->id, $sendEmailRoleIds)) {
                    array_push($sendEmailRoleIds, $role->id);
                }
            }
            $users = $this->lmsRepo->getRoleActiveUsers($sendEmailRoleIds);
            $allEmailData = [];
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            foreach($users as $user) {
                $emailData['receiver_user_name'] = $user->f_name .' '. $user->m_name .' '. $user->l_name;
                $emailData['receiver_email']     = isset($user->email) ? $user->email : '';
                array_push($allEmailData, $emailData);
            }
            $allEmailData['business_name']      = $userInfo->biz->biz_entity_name;
            if (count($sendEmailRoleIds)) {
                \Event::dispatch("CHARGE_DELETION_REQUEST_MAIL", serialize($allEmailData));
            }
            \DB::commit();
            return response()->json(['status' => 1,'msg' => "Charge cancellation request sent for approval successfully ."]);
        } catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function approveChargeDeletion(Request $request)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'chrg_id' => 'required'
            ],['chrg_id.required' => 'Please select atleast one checked']);
    
            $chrgIds = is_array($request->chrg_id) && count($request->chrg_id) ? $request->chrg_id : [$request->chrg_id];
            $chrgTrans = ChargesTransactions::with('chargePrgm:prgm_id,interest_borne_by')->whereIn('chrg_trans_id', $chrgIds)->get();
            $chrgTranReqDltLogs = ChargeTransactionDeleteLog::whereIn('chrg_trans_id', $chrgIds)
                                            ->reqForDeletion()
                                            ->get();

            if (count($chrgTrans) != count($chrgTranReqDltLogs)) {
                return response()->json(['status' => 0,'msg' => "Please request for cancellation before approve the charge."]);
            }

            if (count($chrgTrans) > 1) {
                $chrgTransIds = $chrgTrans->pluck('trans_id')->toArray();
                $valResult = Helpers::validateInvoiceTypes($chrgTransIds, false);
                if ($valResult && isset($valResult['status']) && $valResult['status'] == false) {
                    return response()->json(['status' => 0,'msg' => $valResult['message']]);
                }
            }
           
            $userId = '';
            $cancelTransList =  [];
            $cancelTran = [];
            foreach($chrgTrans as $chrgTran) {
                $chrgTransaction = $chrgTran->transaction; 
                if ($chrgTransaction && $chrgTransaction->amount != $chrgTransaction->outstanding) {
                    \DB::rollback();
                    return response()->json(['status' => 0,'msg' => "Selected Charges can't be cancelled because charge transaction is already settled."]);
                }

                $attr = [
                    'chrg_trans_id' => $chrgTran->chrg_trans_id,
                    'status'        => 2,
                    'created_at'    => now(),
                    'created_by'    => auth()->user()->user_id
                ];
                
                $this->lmsRepo->saveChargeTransDeleteLog($attr);
                $cancelTran[] = $chrgTransaction;
            }
            $cancelTransIds = Transactions::processChrgTransDeletion($cancelTran);
            
            $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');

            $creditData = [];
            $userInvoices = [];
            $cancelTransList = Transactions::whereIn('trans_id',$cancelTransIds)->get();
            foreach($cancelTransList as $trans){
                $billType = null;

                // Generate Debit Note for Parent Transaction 
                $parentTransaction = $trans->parentTransactions; 
                if($parentTransaction->is_invoice_generated == 0){
                    $billType = null;
                    if($parentTransaction->trans_type >= 50){
                        if(isset($parentTransaction->ChargesTransactions->chargePrgm)){
                            if($parentTransaction->ChargesTransactions->chargePrgm->interest_borne_by == 2){
                                $billType = 'CC';
                            }else{
                                $billType = 'CA';
                            }
                        }
                    }
                    $controller->generateDebitNote([$parentTransaction->trans_id], $parentTransaction->user_id, $billType, null, null, 1);
                }
                
                $userInvoiceTrans = $trans->userInvParentTrans;
                if(isset($userInvoiceTrans)){
                    $userInvoices[$userInvoiceTrans->user_invoice_id] = isset($userInvoices[$userInvoiceTrans->user_invoice_id]) ? $userInvoices[$userInvoiceTrans->user_invoice_id] : $userInvoiceTrans->getUserInvoice;
                    
                    if(isset($userInvoices[$userInvoiceTrans->user_invoice_id])){
                        $invTypeName = $userInvoices[$userInvoiceTrans->user_invoice_id]->invoice_type_name == 1 ? 'C' : 'I';
                        $invBorneBy = $userInvoices[$userInvoiceTrans->user_invoice_id]->invoice_borne_by == 1 ? 'A' : 'C';
                        $billType = $invTypeName.$invBorneBy;
                    }
                    
                    $creditData[$trans->user_id][$billType][$trans->gst.'_'.$userInvoices[$userInvoiceTrans->user_invoice_id]->user_invoice_rel_id][$trans->trans_id] = $trans->trans_id;
                }
            }
            
            foreach($creditData as $userId => $transTypes){
                foreach($transTypes as $billType => $gstRelation){
                    foreach ($gstRelation as $trans){
                        $transIds = array_keys($trans);
                        if(!empty($transIds)){
                            $controller->generateCreditNote($transIds, $userId, $billType, null, null, 1);
                        }
                    }
                }
            }
            \DB::commit();

            return response()->json(['status' => 1,'msg' => "Charge cancellation approved successfully."]);
        } catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function deleteManagementInfo(Request $request)
    {        
        try {
            if ($request->has('biz_owner_id') && $request->biz_owner_id) {
                \DB::beginTransaction();
                $bizOwner = $this->application->getBizOwnerDataByOwnerId($request->biz_owner_id);
                if ($bizOwner) {
                    $bizOwner->deleted_by = auth::user()->user_id;
                    $bizOwner->save();
                    $bizOwner->delete();
                    \DB::commit();
                    return response()->json(['status' => 1,'message' => "Management info deleted successfully."]);
                } else {
                    return response()->json(['status' => 0,'message' => 'Something went wrong.']); 
                }
            } else {
                return response()->json(['status' => 0,'message' => 'Something went wrong.']);
            }
        } catch (Exception $ex) {
            \DB::rollback();
            return response()->json(['status' => 0,'message' => Helpers::getExceptionMessage($ex)]);
        }
    }    

    public function getNonAnchorLeads(DataProviderInterface $dataProvider) {
        $leadsList = $this->userRepo->getAllNonAnchorLeads();
        $leads = $dataProvider->getAllNonAnchorLeadsList($this->request, $leadsList);
        return $leads;
    }
    
    public function uploadApprovalMailCopy(Request $request)
    {
        \DB::beginTransaction();
        try {
            //dd($request->all());
            $attributes = $request->all();
            $request->validate([
                'approval_doc_file' => 'required',
                'app_appr_status_id' => 'required',
                'app_id' => 'required'
            ],['approval_doc_file.required' => 'Please select atleast one document']);
            $app_id = $request->app_id;
            $file_id = false;
            $addl_data = [];
            $currStage = Helpers::getCurrentWfStage($app_id);
            $isFinalSubmit = 0;
            $appData = $this->application->getAppData($app_id);
            if ($currStage->stage_code == 'approver') {
                $whereCondition = ['app_id' => $app_id, 'status' => null];
                $offerData = $this->application->getOfferData($whereCondition);
                if (!$offerData) {
                    return response()->json(['status' => 0,'msg' =>'You cannot move this application to next stage as offer still not created.']);
                }
            if ($request->approval_doc_file) {
                $date = Carbon::now();
                $supplier_id = $appData->user_id;
                $uploadApprovalDocData = Helpers::uploadUserApprovalFile($attributes, $supplier_id, $app_id);
                $userFile = $this->docRepo->saveFile($uploadApprovalDocData);
                $file_id = $userFile->file_id;
            }
            if($file_id){
                $update = AppApprover::where('app_appr_status_id', '=', $attributes['app_appr_status_id'])
                ->where('app_id', '=', $attributes['app_id'])
                ->where('is_active', '=', 1)
                ->update(['approval_file_id'=>$file_id,'status' => 1]);
                $msg = 'Approval mail copy has been successfully uploaded.';
                if($update){
                    $appApprData = AppApprover::getAppApprovers($app_id);
                    if (isset($appApprData[0])) {
                        $isFinalUpload = Helpers::isAppApprByAuthority($app_id);
                        if($isFinalUpload){
                            if ($currStage->stage_code == 'approver') {
                                $this->application->updateActiveOfferByAppId($app_id, ['is_approve' => 1]);
                            }
                            $wf_order_no = $currStage->order_no;
                            $nextStage = Helpers::getNextWfStage($wf_order_no);
                            $roleArr = [$nextStage->role_id];
                            $roles = $this->application->getBackStageUsers($app_id, $roleArr);
                            $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;
                            $addl_data['sharing_comment'] = 'Automatically Assigned to Sales Manager from Approver List';
                            $assign = true;
                            $wf_status = 1;
                            if ($nextStage->stage_code == 'sales_queue') {
                                Helpers::updateWfStage($currStage->stage_code, $app_id, $wf_status, $assign, $addl_data);
                                $application = $this->application->updateAppDetails($app_id, ['is_assigned'=>1]);
                                //update approve status in offer table after all approver approve the offer.
                                $this->application->changeOfferApprove((int)$app_id);
                                Helpers::updateAppCurrentStatus($app_id, config('common.mst_status_id.OFFER_LIMIT_APPROVED'));
                                $msg = 'Approval mail copy has been successfully uploaded and moved the next stage (Sales).';
                                $isFinalSubmit = 1;

                                $appId = $request->app_id;
                                //Start Update UCIC Data for when OFFER_LIMIT_APPROVED
                                if(Helper::isAppApprByAuthorityForGroup($appId)) {
                                    $groupId = $this->appRepo->getGroupIdByAppId((int) $appId);
                                    
                                    $pan_no = $appData->business->pan->pan_gst_hash;
                                    $ucicData = $this->ucicuser_repo->getUcicData(['pan_no' => $pan_no]);
                                    if ($ucicData) {
                                        $userUcicId = $ucicData->user_ucic_id ?? null;
                                        if ($userUcicId) {
                                            $product_ids = [];
                                            $attr = [];
                                            $business_info = $this->appRepo->getApplicationById($appData->biz_id);
                                            if (!empty($appData->products)) {
                                                foreach ($appData->products as $product) {
                                                    $product_ids[$product->pivot->product_id]= array(
                                                    "loan_amount" => $product->pivot->loan_amount,
                                                    "tenor_days" => $product->pivot->tenor_days
                                                    );
                                                }
                                            }
                                            $businessInfo = $this->ucicuser_repo->formatBusinessInfoDb($business_info, $product_ids);
                                            $ownerPanApi = $this->userRepo->getOwnerApiDetail(['biz_id' => $appData->biz_id]);
                                            $documentData = \Helpers::makeManagementInfoDocumentArrayData($ownerPanApi);
                                            $managementData = $this->ucicuser_repo->formatManagementInfoDb($ownerPanApi, null);
                                            $managementInfo = array_merge($managementData, $documentData);
                                            $this->ucicuser_repo->saveApplicationInfofinal($userUcicId, $businessInfo, $managementInfo, $appData->app_id, $appData->user_id);
                                            $attr['user_id'] = $appData->user_id;
                                            $attr['app_id'] = $appData->app_id;
                                            $results = $this->ucicuser_repo->getUcicData($attr);
                                            if ($results) {
                                                $attr['is_sync'] = 0;
                                                $this->ucicuser_repo->update($attr, $userUcicId);
                                                $whereucic['user_id'] = $appData->user_id;
                                                $whereucic['app_id']  = $appData->app_id;
                                                $ucicuserData = UcicUserUcic::getUcicUserData($whereucic);
                                                if (!$ucicuserData) {
                                                    $ucicNewDataucic['ucic_id'] = $userUcicId;
                                                    $ucicNewDataucic['user_id'] = $appData->user_id;
                                                    $ucicNewDataucic['app_id'] = $appData->app_id;
                                                    $ucicNewDataucic['group_id'] = $groupId;
                                                    $ucicuserucicData = UcicUserUcic::create($ucicNewDataucic);
                                                }
                                                Helpers::approveAppGroupDetails((int) $groupId, (int) $appData->app_id);
                                                Helpers::saveGroupDetailsToUcic((int) $appData->user_id, (int) $appData->app_id, (int) $groupId);
                                            }
                                        }
                                    }
                                }
                                //End Update UCIC Data for when OFFER_LIMIT_APPROVED
                            }
                        }
                    }
                }
            }
        }else{
            return response()->json(['status' => 0,'msg' => 'No Authority']); 
        }
        \DB::commit();
        return response()->json(['status' => 1,'msg' => $msg, 'isFinalSubmit' =>$isFinalSubmit]);
        } catch (Exception $ex) {
            \DB::rollback();
            return response()->json(['status' => 0,'msg' => Helpers::getExceptionMessage($ex)]);
        }
    }

    public function getBackendUsers(Request $request){

        try{

            $data = $request->all();
            $validator =Validator::make($request->all(),[
                'role_id' => 'required'
            ],['role_id.required' => 'Please select role.']);
            
            if ($validator->fails()) {
                $getErrorVar = $validator->messages()->get('*');
                return response()->json(['status' => '2','message' => 'Please select role.','data'=>$getErrorVar]);
            }

            $role_id = (int)$data['role_id'];
            $userData = Helpers::getAllUsersByRoleId($role_id);
            if(!empty($userData))
                return response()->json(['status' => '1','message' => 'User data according to role.','data'=>$userData]);
            else
                return response()->json(['status' => '0','message' => 'Users not found','data'=>array()]);
            

        } catch (Exception $ex) {
            
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }

    }

    public function getUsersLeads(Request $request){

        try{ 

            $validator =Validator::make($request->all(),[
                'role_id' => 'required',
                'user_id' => 'required'
            ],['role_id.required' => 'Please select role.','user_id.required'=>'Please select user.']);
            $data = $request->all();
            if ($validator->fails()) {

                $getErrorVar = $validator->messages()->get('*');
                return response()->json(['status' => '2','message' => 'validation error','data'=>$getErrorVar]);
            }
            
            return response()->json(['status' => '1','message' => 'validation done','data'=>array()]);
            
        } catch (Exception $ex) {
            
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }    
    }

    public function checkUniqueUtrNo(Request $request) 
    {        
        $utrNumber = $request->get('utr_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->checkUtrNo(['utr_no' => $utrNumber,'user_id'=>$userId]);
        // dd($result);
        if (isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }  
    
    public function checkUniqueUtrAlert(Request $request) 
    {    
        $utrNumber = $request->get('utr_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->checkUtrAlert($utrNumber,$userId);
        if (!isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    } 
    
    public function checkUniqueChequeNo(Request $request) 
    {        
        $chequeNumber = $request->get('cheque_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->checkUtrNo(['cheque_no' => $chequeNumber,'user_id'=>$userId]);
        // dd($result);
        if (isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    } 
    public function checkUniqueChequeAlert(Request $request) 
    {        
        $chequeNumber = $request->get('cheque_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->chequeAlert( $chequeNumber,$userId);
        if (!isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    }

    public function getSecurityDocumentLists(DataProviderInterface $dataProvider) { 
        $securityDocList = $this->masterRepo->getAllSecurityDocument();
        $securityDoc = $dataProvider->getSecurityDocumentLists($this->request, $securityDocList);
        return $securityDoc;
    } 
       
    // Check Security Document Name
    public function checkUniqueSecurityDocumentName(Request $request) 
    {        
        $securityDocumentName = $request->get('name');
        $securityDocId = $request->has('security_doc_id') ? $request->get('security_doc_id'): null ;
        $result = $this->masterRepo->checkSecurityDocument(['name' => $securityDocumentName], $securityDocId);
        if (isset($result[0])) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    } 
    public function checkUniqueUnrNo(Request $request) 
    {        
        $unrNumber = $request->get('unr_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->checkUtrNo(['unr_no' => $unrNumber,'user_id'=>$userId]);
        // dd($result);
        if (isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    } 
    public function checkUniqueUnrAlert(Request $request) 
    {        
        $unrNumber = $request->get('unr_no');
        $userId = $request->has('user_id') ? $request->get('user_id'): null ;
        $result = $this->lmsRepo->checkUnrAlert( $unrNumber,$userId);
        if (!isset($result)) {
            $result = ['status' => 1];
        } else {
            $result = ['status' => 0];
        }
        return response()->json($result); 
    } 

    public function getInvoiceOutstandingList(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('d/m/Y');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('d/m/Y');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'customer_id' => $this->request->get('customer_id'),
            'type' => 'excel',
        ];
        $transactionList = $this->invRepo->getReportAllOutstandingInvoice();
        $users = $dataProvider->getReportAllOverdueInvoice($this->request, $transactionList);
        $users     = $users->getData(true);
        $users['excelUrl'] = route('pdf_invoice_over_due_url', $condArr);
        $condArr['type']  = 'pdf';
        $users['pdfUrl'] = route('pdf_invoice_over_due_url', $condArr);
        return new JsonResponse($users);
    }
    
    public function sendInvoiceOutstandingReportByMail()
    {
        if ($this->request->get('to_date') && $this->request->get('generate_report')) {
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y/m/d');
            $userId  = $this->request->get('user_id') ?? 'all';
            $odReportLog = OutstandingReportLog::create([ 'user_id' => $userId, 'to_date' => $to_date]);
            \Artisan::call("report:outstandingManual", ['user' => $userId, 'date' => $to_date, 'logId' => $odReportLog->id ?? NULL]);
        }
        return \Response::json(['status' => 1, 'logId' => $odReportLog->id]); 
    }

    public function getInvoiceOutstandingReport(DataProviderInterface $dataProvider)
    {
        $overdueReportLogs = OutstandingReportLog::orderBy('id','desc')->get();
        return $dataProvider->getOutstandingReportLogs($this->request, $overdueReportLogs);
    }
    //Get new sanction letter
    public function getNewSanctionLetterList(DataProviderInterface $dataProvider) {
        $app_id =  (int) $this->request->get('app_id');
        $whereCondition=[];
        $whereCondition['app_id'] = $app_id;
        $sanctionLetterdata = $this->application->getOfferNewSanctionLetterData($whereCondition);
        $data = $dataProvider->getNewSanctionLetterList($this->request, $sanctionLetterdata);
        return $data;
    }
    //Update sanction regenerate letter
    public function updateRegenerateSanctionLetter(Request $request) {
       $sanction_id =  (int) $this->request->get('sanction_id');
       $app_id =  (int) $this->request->get('app_id');
       $result = AppSanctionLetter::whereNotIn('sanction_letter_id',[$sanction_id])->where('app_id',$app_id)->update(["status" => 4]);
       $result = AppSanctionLetter::where('sanction_letter_id',$sanction_id)->where('app_id',$app_id)->update(["status" => 3, "is_regenerated" => 0]);
       if($result)
       {
            $appData = $this->application->getAppDataByAppId($app_id);
            //if($appData->curr_status_id == config('common.mst_status_id.SANCTION_LETTER_GENERATED')){
               //Helpers::updateAppCurrentStatus($app_id, config('common.mst_status_id.APP_SANCTIONED'));
            //}
           return response()->json(['status' => 1]); 
       }
       else
       {
           return response()->json(['status' => 0]); 
       }
    }

    public function updateAppSecurityDoc(Request $request ){
        $app_security_doc_id = $request->get('app_security_doc_id');
        $appSecDocData = AppSecurityDoc::where(['app_security_doc_id'=> $app_security_doc_id,'is_non_editable'=>0])->first();
        if($appSecDocData){
            $arrData = AppSecurityDoc::where('app_security_doc_id', $app_security_doc_id)->update(['is_active' => 0]);
            if($arrData){
                if($appSecDocData){
                    $oldFileId = UserFile::deletes($appSecDocData->file_id);
                }
                $status = true; 
            }else{
            $status = false;
            }
        }else{
            $status = false;
        }
        
        return response()->json($status);
    }

     // Check Unique Security Document Number
     public function checkUniqueSecurityDocNumber(Request $request) 
     {        
         $docNumber = $request->get('doc_number');
         $appSecurityId = $request->has('id') ? $request->get('id'): null ;
         $appId = $request->has('app_id') ? $request->get('app_id'): null ;
         if($appSecurityId){
            $result = AppSecurityDoc::where('document_number', $docNumber)->where('app_id', $appId)->where(['is_active'=>1])->where('app_security_doc_id','!=', $appSecurityId)->where('is_non_editable',1)->first();
         }else{
            $result = AppSecurityDoc::where('document_number', $docNumber)->where('app_id', $appId)->where(['is_active'=> 1,'is_non_editable'=>1])->first(); 
         }
         if (isset($result)) {
             $result = ['status' => 1];
         } else {
             $result = ['status' => 0];
         }
         return response()->json($result); 
     }

     public function getCustAndCapsLocApp(DataProviderInterface $dataProvider) {
        $user_id =  (int) $this->request->get('user_id');
        $cusCapLoc = $this->UserInvRepo->getCustAndCapsLocApp($user_id);
        $data = $dataProvider->getCustAndCapsLocApp($this->request, $cusCapLoc);
        return $data;
    }

    public function getReconReportByMail(DataProviderInterface $dataProvider) {
        if($this->request->get('from_date')!= '' && $this->request->get('to_date')!=''){
            $from_date = Carbon::createFromFormat('d/m/Y', $this->request->get('from_date'))->format('d/m/Y');
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('d/m/Y');
        }
        $condArr = [
            'from_date' => $from_date ?? NULL,
            'to_date' => $to_date ?? NULL,
            'user_id' => $this->request->get('user_id'),
            'customer_id' => $this->request->get('customer_id'),
            'type' => 'excel',
        ];
        $transactionList = $this->invRepo->getReportAllOutstandingInvoice();
        $users = $dataProvider->getReportAllOverdueInvoice($this->request, $transactionList);
        $users     = $users->getData(true);
        $users['excelUrl'] = route('pdf_invoice_over_due_url', $condArr);
        $condArr['type']  = 'pdf';
        $users['pdfUrl'] = route('pdf_invoice_over_due_url', $condArr);
        return new JsonResponse($users);
    }

    public function sendReconReportByMail(DataProviderInterface $dataProvider)
    {
        if ($this->request->get('to_date') && $this->request->get('generate_report')) {
            $to_date = Carbon::createFromFormat('d/m/Y', $this->request->get('to_date'))->format('Y-m-d');
            $userId  = $this->request->get('user_id') ?? 'all';
            $odReportLog = ReconReportLog::create([ 'user_id' => $userId, 'to_date' => $to_date]);
            \Artisan::call("report:reconReport", ['user' => $userId, 'date' => $to_date, 'logId' => $odReportLog->id ?? NULL]);
        }
    
        $overdueReportLogs = ReconReportLog::orderBy('id','desc')->get();
        return $dataProvider->getReconReportLogs($this->request, $overdueReportLogs);
    }
    
    public function getData(DataProviderInterface $dataProvider) {
        $ucicList = $this->ucicuser_repo->getUcicUserApp();
        $data = $dataProvider->getUcicList($this->request, $ucicList);
        return $data;
    }

    //Master Group List
    public function getAllGroupList(DataProviderInterface $dataProvider) 
    {
        $groups = $this->masterRepo->getAllNewGroup();
        $data = $dataProvider->getAllGroupsData($this->request, $groups);
        return $data;
    }

    public function checkUniqueGroupName(Request $request)
    {
        $groupName = $request->get('name');
        $groupId = $request->has('group_id') ? $request->get('group_id') : null;
        $result = $this->masterRepo->checkGroupName($groupName, $groupId);
        $result = isset($result[0]) ? ['status' => 1] : ['status' => 0];
        return response()->json($result); 
    }
    
    public function checkGroupNameSuggestions(Request $request)
    {
        $result = false;
        if ($request->has('group_name')) {
            $groupName = substr(trim($request->get('group_name')), 0, 3);
            $result = $this->masterRepo->checkGroupNameSuggestions($groupName);
        }
        $resultData = $result ? ['status' => 1, 'data' => $result] : ['status' => 0];
        return response()->json($resultData); 
    }

    public function getAllGroupUcicList(DataProviderInterface $dataProvider, Request $request)
    {
        $groupId = $request->group_id;
        $groupUcicData = \Helpers::getGroupAppList($groupId);

        $total_sanction_amt = 0;
        $total_outstanding_amt = 0;
        foreach ($groupUcicData as $key => $value) {
            $total_sanction_amt += ($value->sanction > 0) ? $value->sanction : 0;
            $total_outstanding_amt += ($value->outstanding > 0) ? $value->outstanding : 0;
        }
      
        $data = $dataProvider->getGroupUcicData($this->request, $groupUcicData);
        $data = $data->getData(true);
        $data['total_sanction_amt'] = "₹ ".number_format($total_sanction_amt);
        $data['total_outstanding_amt'] = "₹ ".number_format($total_outstanding_amt);
        return new JsonResponse($data);
    }

    public function getGroupUcicUsersData(DataProviderInterface $dataProvider, Request $request)
    {
        $groupId = (int) $request->group_id;
        $appId = (int) $request->app_id;
        $results = [];
        $totalExposureAmt = 0;
        $groupSaveStatus = true;
        if ($groupId && $appId) {
            $isAppApprovedBy = \Helpers::isAppApprByAuthorityForGroup($appId);
            $resultData = \Helpers::getGroupBorrowers($groupId, $appId, $isAppApprovedBy);
            $results = $resultData['results'];
            $totalExposureAmt = $resultData['totalExposureAmt'];
            $groupSaveStatus = $resultData['groupSaveStatus'];
        }
        $data = $dataProvider->getGroupUcicBorrowerData($this->request, $results);
       // dd($data,$appId);
        $data = $data->getData(true);
        $data['total_exposure_amt'] = $totalExposureAmt;
        $data['groupSaveStatus'] = $groupSaveStatus;
        return new JsonResponse($data);
    }
    
    public function getUcicCodeData(Request $request)
    {
       // dd($request->returnurl);
        $ucicCode = $request->ucic;
        $data = ['status' => 0];
        
        if (!$ucicCode) {
            return new JsonResponse($data);
        }
        
        $ucicDetails = UcicUser::where('ucic_code', $ucicCode)->first();
        
        
        if (!$ucicDetails) {
            return new JsonResponse($data);
        }
        
        $ucicId = $ucicDetails->user_ucic_id;
        $appId = $ucicDetails->app_id;
        $userId = $ucicDetails->user_id;
        if($appId == '') {
            return new JsonResponse($data);
        } else {
            $appData = $this->application->getAppData($appId);
        }
        if (!$ucicId) {
            return new JsonResponse($data);
        }
        
        $result = $this->copyApplicationUcic($userId, $appId, $appData->biz_id, 0, $ucicId, false, $request->user_id);
        //$this->ucicuser_repo->copyUcicData($userId, $appId, $appData->biz_id, null, null, 0);
        if (!isset($result['new_app_id'])) {
            return new JsonResponse($data);
        }

        // Uncomment the following code if needed.
        
        $newAppId = $result['new_app_id'];
        $newBizId = $result['new_biz_id'];
        $newUserId = $result['new_user_id'];

        if(isset($ucicDetails) && isset($newAppId)){
            UcicUserUcic::firstOrCreate(
                [
                'app_id' => $newAppId, 
                'ucic_id' => $ucicDetails->user_ucic_id
            ],[
                'ucic_id' => $ucicDetails->user_ucic_id,
                'user_id' => $newUserId ?? NULL,
                'app_id' => $newAppId  ?? NULL,
                'group_id' => $ucicDetails->group_id
                ]       
            );
        }

        if($request->returnurl == "frontendurl") {
            $redirectUrl = route('business_information_open', ['user_id' => $userId,'app_id' => $newAppId, 'biz_id' => $newBizId]);
        } else {
            $redirectUrl = route('company_details', ['user_id' => $userId,'app_id' => $newAppId, 'biz_id' => $newBizId]);
        }

        $data = [
            'status' => 1,
            'redirectUrl' => $redirectUrl,
        ];
        /*
        if ($ucicData && $newAppId) {
            $ucicData->update(['app_id' => $newAppId]);
        }
        */
        return new JsonResponse($data);
    }

    //CHECKING BANKING STATEMENT API STATUS
    public function checkBankingStatementStatus(Request $request) {
        $appId =  (int) $this->request->get('appId');
        $perfios_log_id =  $this->request->get('perfios_log_id');
        $pending_rec = $this->finRepo->getBsaFsaData($appId,'1007', 1);
        if($pending_rec)
        {
            $perfiosLogId = $pending_rec->perfios_log_id ?? NULL;
            $callBackMessage = '';
            if (isset($perfiosLogId)) {
                $callbackResp = $this->finRepo->getBsaFsaCallBackResponse($perfiosLogId);
                if (!empty($callbackResp)) {
                    $callBackMessage = base64_decode($callbackResp->res_file);
                }
            }
            if (($pending_rec->status == 'success' || $pending_rec->status == 'fail') && !empty($callBackMessage)){
                $resStatus =($pending_rec->status == 'success')  ? 1 : 0;
                $controller = app()->make('App\Http\Controllers\Backend\CamController');
                $nameArr = $controller->getLatestFileName($appId, 'banking', 'xlsx');
                $file_name = $nameArr['curr_file'];
                //$file_path = "storage/user/docs/$appId/banking/$file_name";
                if(!empty($file_name) && Storage::exists("public/user/docs/$appId/banking/".$file_name)) {
                    $final_res['file_url'] = Storage::url('user/docs/'.$appId.'/banking/'.$file_name);
                    return response()->json(['status' => 1, 'value'=>$final_res, 'response_status'=>$resStatus]);
                } else {
                    return response()->json(['status' => 0,'value'=>'File is not generated yet.','response_status'=>'404']);
                }
            }else{
                return response()->json(['status' => 0,'value'=>'Perfios response is empty.','response_status'=>'404']);
            } 
        }
        else
        {
            return response()->json(['status' => 0,'value'=>'Perfios data is not found.','response_status'=>'404']); 
        }
     }

     //CHECKING Financial STATEMENT API STATUS
    public function checkFinancialStatementStatus(Request $request) {
        $appId =  (int) $this->request->get('appId');
        $perfios_log_id =  $this->request->get('perfios_log_id');
        $pending_rec = $this->finRepo->getBsaFsaData($appId,'1005', 2);
        if($pending_rec)
        {
            $perfiosLogId = $pending_rec->perfios_log_id ?? NULL;
            $callBackMessage = '';
            if (isset($perfiosLogId)) {
                $callbackResp = $this->finRepo->getBsaFsaCallBackResponse($perfiosLogId);
                if (!empty($callbackResp)) {
                    $callBackMessage = base64_decode($callbackResp->res_file);
                }
            }
            if (($pending_rec->status == 'success' || $pending_rec->status == 'fail') && !empty($callBackMessage)){
                $resStatus =($pending_rec->status == 'success')  ? 1 : 0;
                $controller = app()->make('App\Http\Controllers\Backend\CamController');
                $nameArr = $controller->getLatestFileName($appId, 'finance', 'xlsx');
                $file_name = $nameArr['curr_file'];
                //$file_path = "storage/user/docs/$appId/finance/$file_name";
                if(!empty($file_name) && Storage::exists("public/user/docs/$appId/finance/".$file_name)) {
                    $final_res['file_url'] = Storage::url('user/docs/'.$appId.'/finance/'.$file_name);
                    return response()->json(['status' => 1, 'value'=>$final_res, 'response_status'=>$resStatus]);
                } else {
                    return response()->json(['status' => 0,'value'=>'File is not generated yet.','response_status'=>'404']);
                }
            }else{
                return response()->json(['status' => 0,'value'=>'Perfios response is empty.','response_status'=>'404']);
            } 
        }
        else
        {
            return response()->json(['status' => 0,'value'=>'Perfios data is not found.','response_status'=>'404']); 
        }
     }
}
