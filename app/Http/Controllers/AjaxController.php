<?php
namespace App\Http\Controllers;
use Auth;
use Helpers;
use Session;
use Mail;
use Carbon\Carbon;
use Event;
use Datetime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Ui\DataProviderInterface;
use App\imports\UserImport;
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
use App\Inv\Repositories\Contracts\Traits\InvoiceTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use App\Inv\Repositories\Models\AppAssignment;

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


    function __construct(Request $request, InvUserRepoInterface $user, InvAppRepoInterface $application,InvMasterRepoInterface $master, InvoiceInterface $invRepo,InvDocumentRepoInterface $docRepo, FinanceInterface $finRepo, InvLmsRepoInterface $lms_repo, InvUserInvRepoInterface $UserInvRepo, ReportInterface $reportsRepo) {
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

    /**
     * fetch rights comments
     *
     * @param Request $request
     */
    public function globalSearch(Request $request) {
        try {
            $input = $request->get('input');


            if ($input === null && Session::has('search_input')) {
                
            } else {
                Session::put('search_input', $input);
            }



            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }

    /**
     * Delete rights type
     *
     * @return int
     */
    public function deleteRights(Request $request) {
        $c_id = $request->get('id');
        return Rights::deleteRight($c_id);
    }

//ShareholderFormRequest
    public function saveShareholder(Request $request) {

        try {

            $requestVar = $request->all();
            // dd($requestVar);
            $userId = Auth()->user()->user_id;
            $shareParentIds = @$requestVar['share_parent_id'];
            $shareLevels = @$requestVar['share_level'];


            $rules = [];
            foreach ($shareParentIds as $id) {
                $rows = @$requestVar['rows' . $id];

                for ($key = 0; $key < $rows; $key++) {
                    $rules['shareType' . $id . '_' . $key] = 'required';
                    $rules['companyName' . $id . '_' . $key] = 'required';
                    $rules['passportNo' . $id . '_' . $key] = 'required|regex:/^[a-z0-9 \s]+$/i|max:20';
                    $rules['sharePercentage' . $id . '_' . $key] = 'required|regex:/^\d+(\.\d{1,2})?$/|max:5';
                    $rules['shareValue' . $id . '_' . $key] = 'required|regex:/^\d+(\.\d{1,2})?$/|max:12';
                }
            }


            $messages = [];
            foreach ($shareParentIds as $id) {
                $rows = @$requestVar['rows' . $id];
                for ($key = 0; $key < $rows; $key++) {
                    $messages['shareType' . $id . '_' . $key . '.required'] = 'Individual/Company is required.';
                    $messages['companyName' . $id . '_' . $key . '.required'] = 'Individual name/company name is required.';
                    $messages['passportNo' . $id . '_' . $key . '.required'] = 'Passport No./ License No. is required.';
                    $messages['passportNo' . $id . '_' . $key . '.regex'] = 'Passport No./ License No. must contain only letters, numbers, or space.';
                    $messages['passportNo' . $id . '_' . $key . '.max'] = 'Passport No./ License No. must contain max 20 characters';
                    $messages['sharePercentage' . $id . '_' . $key . '.required'] = 'Sharingholding Percentage is required.';
                    $messages['sharePercentage' . $id . '_' . $key . '.regex'] = 'Invalid value.';
                    $messages['sharePercentage' . $id . '_' . $key . '.max'] = 'Sharingholding Percentage must contain max 5 digit.';
                    $messages['shareValue' . $id . '_' . $key . '.required'] = 'Value in USD is required.';
                    $messages['shareValue' . $id . '_' . $key . '.regex'] = 'Invalid value.';
                    $messages['shareValue' . $id . '_' . $key . '.max'] = 'Value in USD must contain max 20 digit';
                }
            }
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $resData['status'] = 'error';
                $error_msg = [];
                //dd($validator->customMessages);
                $i = 0;
                $customMessages = $validator->getMessageBag()->toArray();
                foreach ($customMessages as $key => $msg) {
                    $idx = explode('.', $key);

                    $error_msg[$i] = $msg;
                    $error_index[$i] = 'error' . $idx[0];
                    $i++;
                }

                $resData['message'] = $error_msg;
                $resData['messagekey'] = $error_index;
                $resData['otherMessage'] = '';
                return json_encode($resData);
            } else {
                $next = 0;
                foreach ($shareParentIds as $pkey => $parent_id) {
                    $shareLevel = $shareLevels[$pkey];
                    $shareTypes = @$requestVar['share_type_' . $parent_id];
                    $rows = @$requestVar['rows' . $parent_id];
                    $parent_actual_share  =   0;
                    if($parent_id>0){
                        $parentInfo             =   $this->application->getShareHolderInfo($parent_id);
                        if($parentInfo){
                           $parent_actual_share    =   $parentInfo->actual_share_percent; 
                        }
                        
                    }
                    
                    for($key = 0; $key < $rows; $key++){
                        $shareData['user_id'] = $userId;
                        $type = @$requestVar['shareType' . $parent_id . '_' . $key];
                        $shareData['share_type'] = @$requestVar['shareType' . $parent_id . '_' . $key];
                        $shareData['company_name'] = @$requestVar['companyName' . $parent_id . '_' . $key];
                        $shareData['passport_no'] = @$requestVar['passportNo' . $parent_id . '_' . $key];
                        $shareData['share_percentage'] = @$requestVar['sharePercentage' . $parent_id . '_' . $key];
                        $shareData["share_value"] = @$requestVar['shareValue' . $parent_id . '_' . $key];
                        $shareData["share_parent_id"] = $parent_id;
                        $shareData["share_level"] = (int) $shareLevel;
                        if($parent_id>0){
                            $actual_share=$parent_actual_share*$shareData['share_percentage']/100;
                            $shareData["actual_share_percent"] = (float)$actual_share;
                        }else{
                            $actual_share=$shareData['share_percentage'];
                            $shareData["actual_share_percent"] = (float)$actual_share;
                        }

                        $response = $this->application->saveShareHoldingForm($shareData, null);
                        if($response){
                            if($shareData["actual_share_percent"]>=5 && $shareData['share_type']==1){
                                $arrKyc['user_id'] = $userId;
                                $arrKyc['is_approve'] = 0;
                                $arrKyc['is_kyc_completed'] = 0;
                                $arrKyc['is_api_pulled'] = 0;
                                $arrKyc['is_by_company']=1;
                                $kycDetail = $this->userRepo->saveKycDetails($arrKyc);
                                $updateData['owner_kyc_id'] = $kycDetail->kyc_id;
                                ////
                                $userKycid  =   $kycDetail->kyc_id;//get user kyc id
                                $corpdata   =   UserReqDoc::where('user_kyc_id',$userKycid)->first();
                                $doc_for = 1;
                                  if(empty($corpdata)){
                                       $doc=DocumentMaster::where('doc_for',$doc_for)->get();
                                       UserReqDoc::createCorpDocRequired($doc,$userKycid, $userId);
                                  }
                                
                                $this->application->saveShareHoldingForm($updateData,$response->corp_shareholding_id);
                            }
                        }    

                        if ($type == '2') {
                            $next++;
                        } else {
                            
                        }
                    }
                }

                $resData['status'] = 'success';
                $resData['message'] = trans('success_messages.UpdateShareHolderSuccessfully');
                if ($next > 0) {
                    Session::flash('message', trans('success_messages.UpdateShareHolderSuccessfully'));
                    $redirect = route('shareholding_structure');
                } else {


                    $redirect = route('shareholding_structure');
                }
                $resData['redirect'] = $redirect;
                return json_encode($resData);
            }
        } catch (Exception $ex) {

            $resData['otherMessage'] = Helpers::getExceptionMessage($ex);
            $resData['status'] = 'error';
            return json_encode($resData);
        }
    }

    ////make userName
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
     * Get all Similar records
     *
     * @return json user data
     */
    public function getUsersListAPI11(Request $request) {

        $authSignature = $request->get('authorisation');
        $date = $request->get('currentDate');
        ;
        $Signature = $request->get('Signature');
        $ContentLength = $request->get('ContentLength');
        $Content = $request->get('content');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://rms-world-check-one-api-pilot.thomsonreuters.com/v1/cases/screeningRequest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"groupId\":\"0a3687cf-68e5-171f-9a3a-1654000000d5\",\n  \"entityType\": \"INDIVIDUAL\",\n  \"providerTypes\": [\n    \"WATCHLIST\"\n  ],\n  \"name\": \"putin\",\n  \"secondaryFields\":[{\"typeId\": \"SFCT_2\",\"dateTimeValue\":\"1952-07-10\"}],\n  \"customFields\":[]\n}",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: " . $authSignature,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: " . $ContentLength,
                "Content-Type: application/json",
                "Date: " . $date,
                "Host: rms-world-check-one-api-pilot.thomsonreuters.com",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
    }

    /**
     * Get all Similar records
     *
     * @return json user data
     */
    public function getUsersListAPI(Request $request) {


            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
              $dataArray = json_decode($response);


             // $dataArray = json_decode($data);
           // echo "<pre>";
            //print_r($dataArray->results);

           // echo count($dataArray->results);
            $i = 0;
            foreach($dataArray->results as $resultArray) {
                $name = $resultArray->matchStrength;
                if($resultArray->matchStrength == 'STRONG') {
                   $primaryName = $resultArray->primaryName;
                   $category    = $resultArray->category;
                   $gender      = $resultArray->gender;
                   $referenceId = $resultArray->referenceId;
                   $providerTypes = $resultArray->providerType;
                   ////Events DOB
                   $eventsDataDOB = "";
                   $countryLinksName = "";
                   $identityDocumentsNumber = "";
                   $identityDocumentsType = "";

                   $eventsArray = $resultArray->events;
                   if(count($eventsArray) > 0) {
                       foreach($eventsArray as $eventsData)
                          {
                           if($eventsData->type=="BIRTH") {
                              $eventsDataDOB = $eventsData->fullDate;
                           }
                          }

                   }
                   ////Country Events
                   $countryLinksArray = $resultArray->countryLinks;
                   if(count($countryLinksArray) > 0) {
                       foreach($countryLinksArray as $countryLinksData)
                          {
                           if($countryLinksData->type=="NATIONALITY") {
                              $countryLinksName = $countryLinksData->country->name;
                           }
                          }
                   }
                   ///Identity
                   $identityDocumentsArray = $resultArray->identityDocuments;
                   if(count($identityDocumentsArray) > 0) {
                       foreach($identityDocumentsArray as $identityDocumentsData)
                          {
                           $identityDocumentsType = $identityDocumentsData->type;
                           $identityDocumentsNumber = $identityDocumentsData->number;
                          }
                   }


                   echo "ReferenceId : ".$referenceId;
                   echo "<br>Name : ".$primaryName;
                   echo "<br>Category : ".$category;
                   echo "<br>providerTypes : ".$providerTypes;
                   echo "<br>Gender : ".$gender;
                   echo "<br>DOB : ".$eventsDataDOB;
                   echo "<br>Country : ".$countryLinksName;

                   if($identityDocumentsType!="") {
                    echo "<br>Identity Type : ".$identityDocumentsType;
                    echo "<br>identityDocumentsNumber : ".$identityDocumentsNumber;

                   }
                   $BindData = '';
                   //$BindData = $referenceId."#".$primaryName."#".$category."#".$providerTypes."#".$gender."#".$eventsDataDOB."#".$countryLinksName."#".$identityDocumentsType."#".$identityDocumentsNumber;
                   $BindData = $referenceId."#".$primaryName;
                   ?>

                <table>
                <tr>
                    <td>
                        <input type="radio" name="kycdetailID" id="kycdetailID" value="<?php echo $BindData;?>">
                        <input type="hidden" name="hiddenval" value="<?php echo $BindData;?>">
                        <input type="button" value="getDetail" id="getfullDetail_<?php echo $i;?>" name="getfullDetail" class="getfullDetail">
                    </td>
                </tr>



               <div id="profileDetail_<?php echo $i;?>"></div>



                </table>

            <?php
            $i++;
                   echo  "<br>===========================<br>";
                   echo  "<br>===========================<br>";
                }


        $curl = curl_init();


        }



        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $rowData = json_decode($response);
            print_r($rowData);
        }
    }

    }
   /**
     * Get all Similar records
     *
     * @return json user data
     */
    public function getUsersListAPIDummy(Request $request)
    {

        $data = '{
    "caseId": "2152f2c2-dce9-4cee-9371-bedb359f65fb",
    "results": [
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114657",
            "referenceId": "e_tr_wci_2133069",
            "matchStrength": "STRONG",
            "matchedTerm": "ФАРУТИН,Владимир",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_RUPGO"
            ],
            "categories": [
                "Law Enforcement"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Vladimir FARUTIN",
            "events": [
                {
                    "day": null,
                    "month": null,
                    "year": 1975,
                    "address": null,
                    "fullDate": "1975",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                },
                {
                    "day": null,
                    "month": null,
                    "year": 1976,
                    "address": null,
                    "fullDate": "1976",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "CRIME - NARCOTICS",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011464b",
            "referenceId": "e_tr_wci_1724038",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Андрей",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP SN"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Andrey PUTIN",
            "events": [
                {
                    "day": 17,
                    "month": 1,
                    "year": 1979,
                    "address": null,
                    "fullDate": "1979-01-17",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "POLITICAL INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114643",
            "referenceId": "e_tr_wci_3553462",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Юрий А",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_4"
            ],
            "categories": [
                "Other Bodies"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Iurii PUTIN",
            "events": [],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114645",
            "referenceId": "e_tr_wci_1858593",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Алексей Андреевич",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP SN"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Aleksey PUTIN",
            "events": [
                {
                    "day": 29,
                    "month": 6,
                    "year": 1968,
                    "address": null,
                    "fullDate": "1968-06-29",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "POLITICAL INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114649",
            "referenceId": "e_tr_wci_2692",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Владимир Владимирович",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP N"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Vladimir PUTIN",
            "events": [
                {
                    "day": 7,
                    "month": 10,
                    "year": 1952,
                    "address": null,
                    "fullDate": "1952-10-07",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [
                {
                    "entity": null,
                    "number": "XX-AK 525818",
                    "issueDate": null,
                    "expiryDate": null,
                    "issuer": "RUSSIAN FEDERATION",
                    "type": "Passport",
                    "locationType": null
                }
            ],
            "category": "POLITICAL INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011464d",
            "referenceId": "e_tr_wci_1592168",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Александр Михайлович",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PAICIJ-WC",
                "b_trwc_PEP N-R"
            ],
            "categories": [
                "Other Bodies",
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Aleksandr PUTIN",
            "events": [
                {
                    "day": 18,
                    "month": 10,
                    "year": 1953,
                    "address": null,
                    "fullDate": "1953-10-18",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "VIRGIN ISLANDS (BRITISH)",
                    "country": {
                        "code": "VGB",
                        "name": "VIRGIN ISLANDS, BRITISH"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114659",
            "referenceId": "e_tr_wci_3695591",
            "matchStrength": "STRONG",
            "matchedTerm": "ПУТИН,Василий Викторович",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_4"
            ],
            "categories": [
                "Other Bodies"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Vasiliy PUTIN",
            "events": [
                {
                    "day": 22,
                    "month": 8,
                    "year": 1974,
                    "address": null,
                    "fullDate": "1974-08-22",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011465b",
            "referenceId": "e_tr_wci_1247281",
            "matchStrength": "MEDIUM",
            "matchedTerm": "ПУТИН,Александр Валентинович",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP SN"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Aleksandr PUTIN",
            "events": [
                {
                    "day": 16,
                    "month": 6,
                    "year": 1963,
                    "address": null,
                    "fullDate": "1963-06-16",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "CRIME - FINANCIAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011464f",
            "referenceId": "e_tr_wci_514158",
            "matchStrength": "MEDIUM",
            "matchedTerm": "ПУТИН,Игорь Александрович",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PAICIJ-WC",
                "b_trwc_PEP N-R"
            ],
            "categories": [
                "Other Bodies",
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Igor PUTIN",
            "events": [
                {
                    "day": 30,
                    "month": 3,
                    "year": 1953,
                    "address": null,
                    "fullDate": "1953-03-30",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "VIRGIN ISLANDS (BRITISH)",
                    "country": {
                        "code": "VGB",
                        "name": "VIRGIN ISLANDS, BRITISH"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114655",
            "referenceId": "e_tr_wci_1592262",
            "matchStrength": "MEDIUM",
            "matchedTerm": "ПУТИН,Роман Игоревич",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP SN"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Roman PUTIN",
            "events": [
                {
                    "day": 10,
                    "month": 11,
                    "year": 1977,
                    "address": null,
                    "fullDate": "1977-11-10",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "UNITED KINGDOM",
                    "country": {
                        "code": "GBR",
                        "name": "UNITED KINGDOM"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114661",
            "referenceId": "e_tr_wci_1826682",
            "matchStrength": "MEDIUM",
            "matchedTerm": "布丁",
            "submittedTerm": "putin",
            "matchedNameType": "NATIVE_AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_4"
            ],
            "categories": [
                "Other Bodies"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Yu Jen LIN",
            "events": [
                {
                    "day": null,
                    "month": null,
                    "year": 1987,
                    "address": null,
                    "fullDate": "1987",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                },
                {
                    "day": null,
                    "month": null,
                    "year": 1988,
                    "address": null,
                    "fullDate": "1988",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "TAIWAN",
                    "country": {
                        "code": "TWN",
                        "name": "TAIWAN, PROVINCE OF CHINA"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "TAIWAN",
                    "country": {
                        "code": "TWN",
                        "name": "TAIWAN, PROVINCE OF CHINA"
                    },
                    "type": "NATIONALITY"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011465d",
            "referenceId": "e_tr_wci_164905",
            "matchStrength": "WEAK",
            "matchedTerm": "PUTINA,Maria",
            "submittedTerm": "putin",
            "matchedNameType": "LANG_VARIATION",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP N-R"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Mariya PUTINA",
            "events": [
                {
                    "day": 28,
                    "month": 4,
                    "year": 1985,
                    "address": null,
                    "fullDate": "1985-04-28",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "NETHERLANDS",
                    "country": {
                        "code": "NLD",
                        "name": "NETHERLANDS"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "FEMALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114647",
            "referenceId": "e_tr_wci_3487074",
            "matchStrength": "WEAK",
            "matchedTerm": "Tommaso PUTIN",
            "submittedTerm": "putin",
            "matchedNameType": "PRIMARY",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_145"
            ],
            "categories": [
                "Regulatory Enforcement"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Tommaso PUTIN",
            "events": [],
            "countryLinks": [
                {
                    "countryText": "ITALY",
                    "country": {
                        "code": "ITA",
                        "name": "ITALY"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "ITALY",
                    "country": {
                        "code": "ITA",
                        "name": "ITALY"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "MALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114653",
            "referenceId": "e_tr_wci_164887",
            "matchStrength": "WEAK",
            "matchedTerm": "PUTIN,Katya",
            "submittedTerm": "putin",
            "matchedNameType": "AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_CAATSA228-WC",
                "b_trwc_PEP N"
            ],
            "categories": [
                "Other Bodies",
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Yekaterina PUTINA",
            "events": [
                {
                    "day": 31,
                    "month": 8,
                    "year": 1986,
                    "address": null,
                    "fullDate": "1986-08-31",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                },
                {
                    "countryText": "GERMANY",
                    "country": {
                        "code": "DEU",
                        "name": "GERMANY"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "UNITED KINGDOM",
                    "country": {
                        "code": "GBR",
                        "name": "UNITED KINGDOM"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [
                {
                    "entity": null,
                    "number": "503227394158",
                    "issueDate": null,
                    "expiryDate": null,
                    "issuer": null,
                    "type": null,
                    "locationType": {
                        "type": "RU-INN",
                        "country": {
                            "code": "RUS",
                            "name": "RUSSIAN FEDERATION"
                        },
                        "name": "IDENTIFIKATSIONNYY NOMER NALOGOPLATELSHCHIKA - TAXPAYERS IDENTIFICATION NUMBER"
                    }
                }
            ],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "FEMALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b00114651",
            "referenceId": "e_tr_wci_1592194",
            "matchStrength": "WEAK",
            "matchedTerm": "Vera PUTINA",
            "submittedTerm": "putin",
            "matchedNameType": "PRIMARY",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP N-R"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Vera PUTINA",
            "events": [
                {
                    "day": null,
                    "month": null,
                    "year": 1984,
                    "address": null,
                    "fullDate": "1984",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "FEMALE"
        },
        {
            "resultId": "0a3687cf-6cb9-1a5a-9b2f-758b0011465f",
            "referenceId": "e_tr_wci_11380",
            "matchStrength": "WEAK",
            "matchedTerm": "PUTIN,Lyudmila",
            "submittedTerm": "putin",
            "matchedNameType": "AKA",
            "secondaryFieldResults": [],
            "sources": [
                "b_trwc_PEP N-R"
            ],
            "categories": [
                "PEP"
            ],
            "creationDate": "2019-08-23T09:37:05.535Z",
            "modificationDate": "2019-08-23T09:37:05.535Z",
            "primaryName": "Lyudmila PUTINA",
            "events": [
                {
                    "day": 6,
                    "month": 1,
                    "year": 1958,
                    "address": null,
                    "fullDate": "1958-01-06",
                    "allegedAddresses": [],
                    "type": "BIRTH"
                }
            ],
            "countryLinks": [
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "NATIONALITY"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "POB"
                },
                {
                    "countryText": "RUSSIAN FEDERATION",
                    "country": {
                        "code": "RUS",
                        "name": "RUSSIAN FEDERATION"
                    },
                    "type": "LOCATION"
                }
            ],
            "identityDocuments": [
                {
                    "entity": null,
                    "number": "780107177667",
                    "issueDate": null,
                    "expiryDate": null,
                    "issuer": null,
                    "type": null,
                    "locationType": {
                        "type": "RU-INN",
                        "country": {
                            "code": "RUS",
                            "name": "RUSSIAN FEDERATION"
                        },
                        "name": "IDENTIFIKATSIONNYY NOMER NALOGOPLATELSHCHIKA - TAXPAYERS IDENTIFICATION NUMBER"
                    }
                }
            ],
            "category": "INDIVIDUAL",
            "providerType": "WATCHLIST",
            "gender": "FEMALE"
        }
    ]
}';
              $dataArray = json_decode($data);


             // $dataArray = json_decode($data);
           // echo "<pre>";
            //print_r($dataArray->results);

           // echo count($dataArray->results);
            $i = 0;
            foreach($dataArray->results as $resultArray) {
                $name = $resultArray->matchStrength;
                if($resultArray->matchStrength == 'STRONG') {
                   $primaryName = $resultArray->primaryName;
                   $category    = $resultArray->category;
                   $gender      = $resultArray->gender;
                   $referenceId = $resultArray->referenceId;
                   $providerTypes = $resultArray->providerType;
                   ////Events DOB
                   $eventsDataDOB = "";
                   $countryLinksName = "";
                   $identityDocumentsNumber = "";
                   $identityDocumentsType = "";

                   $eventsArray = $resultArray->events;
                   if(count($eventsArray) > 0) {
                       foreach($eventsArray as $eventsData)
                          {
                           if($eventsData->type=="BIRTH") {
                              $eventsDataDOB = $eventsData->fullDate;
                           }
                          }

                   }
                   ////Country Events
                   $countryLinksArray = $resultArray->countryLinks;
                   if(count($countryLinksArray) > 0) {
                       foreach($countryLinksArray as $countryLinksData)
                          {
                           if($countryLinksData->type=="NATIONALITY") {
                              $countryLinksName = $countryLinksData->country->name;
                           }
                          }
                   }
                   ///Identity
                   $identityDocumentsArray = $resultArray->identityDocuments;
                   if(count($identityDocumentsArray) > 0) {
                       foreach($identityDocumentsArray as $identityDocumentsData)
                          {
                           $identityDocumentsType = $identityDocumentsData->type;
                           $identityDocumentsNumber = $identityDocumentsData->number;
                          }
                   }


                   echo "ReferenceId : ".$referenceId;
                   echo "<br>Name : ".$primaryName;
                   echo "<br>Category : ".$category;
                   echo "<br>providerTypes : ".$providerTypes;
                   echo "<br>Gender : ".$gender;
                   echo "<br>DOB : ".$eventsDataDOB;
                   echo "<br>Country : ".$countryLinksName;

                   if($identityDocumentsType!="") {
                    echo "<br>Identity Type : ".$identityDocumentsType;
                    echo "<br>identityDocumentsNumber : ".$identityDocumentsNumber;

                   }
                   $BindData = '';
                   //$BindData = $referenceId."#".$primaryName."#".$category."#".$providerTypes."#".$gender."#".$eventsDataDOB."#".$countryLinksName."#".$identityDocumentsType."#".$identityDocumentsNumber;
                   $BindData = $referenceId."#".$primaryName;
                   ?>

                <table>
                <tr>
                    <td>
                        <input type="radio" name="kycdetailID" id="kycdetailID" value="<?php echo $BindData;?>">
                        <input type="hidden" name="hiddenval" value="<?php echo $BindData;?>">
                        <input type="button" value="getDetail" id="getfullDetail_<?php echo $i;?>" name="getfullDetail" class="getfullDetail">
                    </td>
                </tr>



               <div id="profileDetail_<?php echo $i;?>"></div>



                </table>

            <?php
            $i++;
                   echo  "<br>===========================<br>";
                   echo  "<br>===========================<br>";
                }

            }

        


    }


    public function getUsersDetailAPIDummy(Request $request)
    {




/*
        $data = '{
    "entityType": "INDIVIDUAL",
    "actions": [],
    "active": true,
    "addresses": [
        {
            "city": "Moscow",
            "country": {
                "code": "RUS",
                "name": "RUSSIAN FEDERATION"
            },
            "postCode": null,
            "region": "Moscow Region",
            "street": null
        },
        {
            "city": "Saint Petersburg",
            "country": {
                "code": "RUS",
                "name": "RUSSIAN FEDERATION"
            },
            "postCode": null,
            "region": "Leningrad Region",
            "street": null
        }
    ],
    "associates": [
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_1791713",
            "targetExternalImportId": "ei_trwc_1791713",
            "type": "AFFILIATED_COMPANY"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_957042",
            "targetExternalImportId": "ei_trwc_957042",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_2264120",
            "targetExternalImportId": "ei_trwc_2264120",
            "type": "AFFILIATED_COMPANY"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_2470127",
            "targetExternalImportId": "ei_trwc_2470127",
            "type": "AFFILIATED_COMPANY"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_292372",
            "targetExternalImportId": "ei_trwc_292372",
            "type": "AFFILIATED_COMPANY"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_946746",
            "targetExternalImportId": "ei_trwc_946746",
            "type": "AFFILIATED_COMPANY"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_11048",
            "targetExternalImportId": "ei_trwc_11048",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_11136",
            "targetExternalImportId": "ei_trwc_11136",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_11380",
            "targetExternalImportId": "ei_trwc_11380",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1196120",
            "targetExternalImportId": "ei_trwc_1196120",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1201768",
            "targetExternalImportId": "ei_trwc_1201768",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1264",
            "targetExternalImportId": "ei_trwc_1264",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1432964",
            "targetExternalImportId": "ei_trwc_1432964",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_14390",
            "targetExternalImportId": "ei_trwc_14390",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_14928",
            "targetExternalImportId": "ei_trwc_14928",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1591",
            "targetExternalImportId": "ei_trwc_1591",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1592168",
            "targetExternalImportId": "ei_trwc_1592168",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1592194",
            "targetExternalImportId": "ei_trwc_1592194",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1592262",
            "targetExternalImportId": "ei_trwc_1592262",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_161038",
            "targetExternalImportId": "ei_trwc_161038",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1644990",
            "targetExternalImportId": "ei_trwc_1644990",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1645234",
            "targetExternalImportId": "ei_trwc_1645234",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_164887",
            "targetExternalImportId": "ei_trwc_164887",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_164905",
            "targetExternalImportId": "ei_trwc_164905",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1726",
            "targetExternalImportId": "ei_trwc_1726",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_187098",
            "targetExternalImportId": "ei_trwc_187098",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1874",
            "targetExternalImportId": "ei_trwc_1874",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_196213",
            "targetExternalImportId": "ei_trwc_196213",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_196573",
            "targetExternalImportId": "ei_trwc_196573",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_1994373",
            "targetExternalImportId": "ei_trwc_1994373",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_2002173",
            "targetExternalImportId": "ei_trwc_2002173",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_20594",
            "targetExternalImportId": "ei_trwc_20594",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_2370822",
            "targetExternalImportId": "ei_trwc_2370822",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_249429",
            "targetExternalImportId": "ei_trwc_249429",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_249742",
            "targetExternalImportId": "ei_trwc_249742",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_2733185",
            "targetExternalImportId": "ei_trwc_2733185",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_27687",
            "targetExternalImportId": "ei_trwc_27687",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_2784",
            "targetExternalImportId": "ei_trwc_2784",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_2980",
            "targetExternalImportId": "ei_trwc_2980",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_357171",
            "targetExternalImportId": "ei_trwc_357171",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_41711",
            "targetExternalImportId": "ei_trwc_41711",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_48876",
            "targetExternalImportId": "ei_trwc_48876",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_514158",
            "targetExternalImportId": "ei_trwc_514158",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_543570",
            "targetExternalImportId": "ei_trwc_543570",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_57662",
            "targetExternalImportId": "ei_trwc_57662",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_5804",
            "targetExternalImportId": "ei_trwc_5804",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_593350",
            "targetExternalImportId": "ei_trwc_593350",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_593351",
            "targetExternalImportId": "ei_trwc_593351",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_8692",
            "targetExternalImportId": "ei_trwc_8692",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_87039",
            "targetExternalImportId": "ei_trwc_87039",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_87084",
            "targetExternalImportId": "ei_trwc_87084",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wci_9459",
            "targetExternalImportId": "ei_trwc_9459",
            "type": "ASSOCIATE"
        },
        {
            "reversed": null,
            "targetEntityId": "e_tr_wco_1878726",
            "targetExternalImportId": "ei_trwc_1878726",
            "type": "AFFILIATED_COMPANY"
        }
    ],
    "category": "POLITICAL INDIVIDUAL",
    "comments": null,
    "contacts": [],
    "countryLinks": [
        {
            "country": {
                "code": "RUS",
                "name": "RUSSIAN FEDERATION"
            },
            "countryText": "RUSSIAN FEDERATION",
            "type": "LOCATION"
        },
        {
            "country": {
                "code": "RUS",
                "name": "RUSSIAN FEDERATION"
            },
            "countryText": "RUSSIAN FEDERATION",
            "type": "NATIONALITY"
        }
    ],
    "creationDate": "2000-11-05T00:00:00Z",
    "deletionDate": null,
    "description": null,
    "details": [
        {
            "detailType": "BIOGRAPHY",
            "text": " President of the Russian Federation (Mar 2000 - Mar 2008) (May 2012 - 2018) (Mar 2018 - ). Chairman of the Security Council of the Russian Federation. Chairman of the Supervisory Board of the Agency for Strategic Initiatives (SOE) (2011- ). Prime Minister (May 2008 - May 2012). Chairman of the Supervisory Board of Vnesheconombank (SOE) (2007 - May 2012). Honorary President of Yawara-Neva (reported 2014- ). Member of the Governing Council of The Shanghai Cooperation Organisation (reported 2012- ). Chairman of Edinaya Rossiya (Apr 2008 - Apr 2012). Co-founder of DPK Ozero (1996). ",
            "title": "BIOGRAPHY"
        },
        {
            "detailType": "IDENTIFICATION",
            "text": " Lyudmila Putina (former spouse). Yekaterina Putina (daughter). Mariya Putina (daughter). Aleksandr Putin (relative). Igor Putin (cousin). Viktor Medvedchuk (relative). Sergey Roldugin (close associate). Viktor Zolotov (PEP) (close associate). Kirill Shamalov (sanctioned individual) (PEP) (son-in-law). ",
            "title": "IDENTIFICATION"
        },
        {
            "detailType": "REPORTS",
            "text": " Dec 2014 - US strongly condemned the actions of the Russian Federation, under President Vladimir Putin, which has carried out a policy of aggression against neighbouring countries aimed at political and economic domination (H.Res.758). Apr 2015 - Verkhovna Rada of Ukraine adopted a Resolution recommending to the National Security and Defence Council of Ukraine (NSDC) to impose sanctions against persons responsible for illegal imprisonment of Ukrainian officer, Verkhovna Rada deputy and member of Permanent Delegation to Parliamentary Assembly of Council of Europe Nadiya Savchenko. Apr 2018 - no further information reported.",
            "title": "REPORTS"
        }
    ],
    "entityId": "e_tr_wci_2692",
    "externalImportId": "ei_trwc_2692",
    "files": [],
    "identityDocuments": [
        {
            "entity": null,
            "expiryDate": null,
            "issueDate": null,
            "issuer": "RUSSIAN FEDERATION",
            "locationType": null,
            "number": "XX-AK 525818",
            "type": "Passport"
        }
    ],
    "images": [],
    "lastAdjunctChangeDate": "2012-01-11T00:00:00Z",
    "modificationDate": "2019-05-14T00:00:00Z",
    "names": [
        {
            "fullName": "Vladimir PUTIN",
            "givenName": "Vladimir",
            "languageCode": null,
            "lastName": "PUTIN",
            "originalScript": "Vladimir PUTIN",
            "prefix": null,
            "suffix": null,
            "type": "PRIMARY"
        },
        {
            "fullName": "ПУТИН,Владимир Владимирович",
            "givenName": null,
            "languageCode": {
                "code": "rus",
                "name": "Russian"
            },
            "lastName": null,
            "originalScript": "ПУТИН,Владимир Владимирович",
            "prefix": null,
            "suffix": null,
            "type": "NATIVE_AKA"
        },
        {
            "fullName": "POUTINE,Vladimir Vladimirovich",
            "givenName": null,
            "languageCode": null,
            "lastName": null,
            "originalScript": "POUTINE,Vladimir Vladimirovich",
            "prefix": null,
            "suffix": null,
            "type": "AKA"
        },
        {
            "fullName": "PUTIN,Vladimir Vladimirovich",
            "givenName": null,
            "languageCode": null,
            "lastName": null,
            "originalScript": "PUTIN,Vladimir Vladimirovich",
            "prefix": null,
            "suffix": null,
            "type": "AKA"
        }
    ],
    "previousCountryLinks": [],
    "provider": {
        "code": "trwc",
        "identifier": "cnp_7",
        "master": true,
        "name": "World Check"
    },
    "sourceDescription": null,
    "sourceUris": [],
    "sources": [
        {
            "abbreviation": "PEP N",
            "creationDate": "2013-03-21T13:41:09Z",
            "identifier": "b_trwc_PEP N",
            "importIdentifier": null,
            "name": "PEP - National Government",
            "provider": null,
            "providerSourceStatus": "ACTIVE",
            "regionOfAuthority": null,
            "subscriptionCategory": "STANDARD",
            "type": {
                "category": {
                    "description": "This gives details of high-ranking government officials in over 200 countries. Although there may be no reason why you should not do business with these individuals, the Basle Committee on Banking supervision has stated that one should check these customers because without this due diligence, banks can become subject to reputational, operational, legal and concentration risks, which can result in significant financial cost.",
                    "identifier": "ec_4",
                    "name": "PEP",
                    "providerSourceTypes": []
                },
                "identifier": "t_trwc_8",
                "name": "National Government"
            }
        }
    ],
    "subCategory": "PEP N",
    "updateCategory": "C4",
    "updatedDates": {
        "ageUpdated": null,
        "aliasesUpdated": "2011-05-03T00:00:00Z",
        "alternativeSpellingUpdated": "2011-05-03T00:00:00Z",
        "asOfDateUpdated": null,
        "categoryUpdated": "2003-06-25T00:00:00Z",
        "citizenshipsUpdated": "2003-06-25T00:00:00Z",
        "companiesUpdated": "2014-11-18T00:00:00Z",
        "deceasedUpdated": null,
        "dobsUpdated": null,
        "eiUpdated": "2009-04-21T00:00:00Z",
        "enteredUpdated": null,
        "externalSourcesUpdated": "2018-03-20T00:00:00Z",
        "firstNameUpdated": "2011-05-03T00:00:00Z",
        "foreignAliasUpdated": "2012-01-11T00:00:00Z",
        "furtherInformationUpdated": "2018-04-20T00:00:00Z",
        "idNumbersUpdated": null,
        "keywordsUpdated": null,
        "lastNameUpdated": "2003-06-25T00:00:00Z",
        "linkedToUpdated": "2019-05-14T00:00:00Z",
        "locationsUpdated": "2016-09-16T00:00:00Z",
        "lowQualityAliasesUpdated": null,
        "passportsUpdated": "2005-08-25T00:00:00Z",
        "placeOfBirthUpdated": "2016-09-16T00:00:00Z",
        "positionUpdated": "2017-05-25T00:00:00Z",
        "ssnUpdated": null,
        "subCategoryUpdated": "2010-05-04T00:00:00Z",
        "titleUpdated": null,
        "updatecategoryUpdated": "2017-11-15T00:00:00Z"
    },
    "weblinks": [
        {
            "caption": null,
            "uri": "http://www.newsru.com/russia/19feb2008/verbizkaya.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.novayagazeta.ru/politics/49409.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.reuters.com/article/2014/04/02/us-russia-putin-divorce-idUSBREA311PG20140402",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.reuters.com/investigates/special-report/russia-capitalism-shamalov/",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://www.rferl.org/featuresarticle/2004/3/9C2F83FC-0F97-4A05-BA85-7000A893C99A.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.rferl.org/featuresarticle/2004/3/D4297553-D6A8-49C4-AE5F-47DAB7E2DEC7.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.rg.ru/2012/03/05/vibory.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.scrf.gov.ru/persons/sections/6/",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.sectsco.org/EN/Russia.asp",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.telegraph.co.uk/news/worldnews/europe/ukraine/10697986/Merkel-fury-after-Gerhard-Schroeder-backs-Putin-on-Ukraine.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.themoscowtimes.com/news/article/putin-quits-united-russia-and-urges-medvedev-to-take-control/457446.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.timesonline.co.uk/tol/news/world/europe/article2652774.ece",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.transparency.org/cgi-bin/dcn-read.pl?citID=35753",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.usrbc.org/Members-Only/visas-russia.asp",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.veb.ru/en/about/officials/nabl/",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.lobbying.ru/content/persons/id_3921_linkid_2.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.mk.ru/politics/russia/article/2013/09/20/918670-yakunin-rasskazal-o-kooperative-quotozeroquot-quotmyi-byili-predanyi-svoey-stranequot.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.newsru.com/russia/17oct2006/brother.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://sobesednik.ru/rassledovanie/20160412-ten-putina-tayny-biografii-glavy-nacgvardii-viktora-zolotov",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://premier.gov.ru/eng/premier/biography.html",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://premier.gov.ru/premier/biography.html",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://rada.gov.ua/en/news/News/108242.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.ceo.spb.ru/eng/capital/index.shtml",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.asi.ru/about_agency/supervisory_board.php",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.businessweek.com/2000/00_49/b3710137.htm",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.cbc.ca/news/indepth/background/putin.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.ceo.spb.ru/eng/capital/putin.v.v/index.shtml",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.epam.ru/index.php?id=21&id2=805&l=rus&printable=1",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.faz.net/aktuell/politik/schroeder-verteidigt-umarmung-mit-putin-12933831.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.fsk.ru/jan/obozr/index.htm",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.haaretz.com/hasen/spages/875313.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.haaretz.com/hasen/spages/875845.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.kavkaz.tv/ukr/content/2009/04/20/9225.shtml",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.kommersant.ru/doc.aspx?DocsID=890132",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.liveinternet.ru/users/4215838/post180161791/",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://er.ru/rubr.shtml?110085",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://gazeta.ua/ru/articles/politics/_zena-medvedchuka-rasskazala-pochemu-putin-stal-ih-kumom-foto/191192",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://grani.ru/Economy/m.103096.html",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://kremlin.ru/news/15224",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://lenta.ru/lib/14160711/",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://nbnews.com.ua/news/48780/",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://news.bbc.co.uk/2/hi/europe/7274001.stm",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://www.xs4all.nl/~eurodos/docu/russia/putin_cv.htm",
            "tags": []
        },
        {
            "caption": null,
            "uri": "http://yawara.spb.ru/about/category.php?ID=3",
            "tags": [
                "PHOTO"
            ]
        },
        {
            "caption": null,
            "uri": "http://zakon2.rada.gov.ua/laws/show/350-19",
            "tags": []
        },
        {
            "caption": null,
            "uri": "https://www.congress.gov/bill/113th-congress/house-resolution/758",
            "tags": []
        },
        {
            "caption": null,
            "uri": "https://www.vesti.ru/doc.html?id=2996858",
            "tags": [
                "PHOTO"
            ]
        }
    ],
    "gender": "MALE",
    "roles": [
        {
            "end": null,
            "location": null,
            "source": null,
            "start": null,
            "title": "Head of Government",
            "type": "Position"
        }
    ],
    "ageAsOfDate": null,
    "isDeceased": null,
    "events": [
        {
            "address": {
                "city": null,
                "country": {
                    "code": "RUS",
                    "name": "RUSSIAN FEDERATION"
                },
                "postCode": null,
                "region": "Leningrad (Saint Petersburg), Russian Federation",
                "street": null
            },
            "allegedAddresses": [],
            "day": 7,
            "fullDate": "1952-10-07",
            "month": 10,
            "type": "BIRTH",
            "year": 1952
        }
    ],
    "previousRoles": [],
    "age": null,
    "entityType": "INDIVIDUAL"
}';



$resultArray = json_decode($data);
*/
//echo "<pre>";
//print_r($resultArray);

//exit;

$authSignature  =  $_POST['authorisation'];
$date  =  $_POST['currentDate'];
$Signature = $_POST['Signature'];
$profileID = $_POST['profileID'];

$endPoint = 'https://rms-world-check-one-api-pilot.thomsonreuters.com/v1/reference/profile/'.$profileID;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $endPoint,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: ".$authSignature,
    "Date: ".$date,
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $resultArray = json_decode($response);

  //echo "<pre>";
  //print_r($rowData);

}





       $category    = $resultArray->category;
       $gender      = $resultArray->gender;
       $referenceId = '';
       if(isset($resultArray->referenceId)) {
        $referenceId = $resultArray->referenceId;
       }
       //$providerTypes = $resultArray->providerType;

       $fullName            = "";
       $roleTitle           = "";
       $roleTitle           = "";
       $eventsDataDOB       = "";
       $countryLinksName    = "";
       $sourcesName         = "";
       $sourcesDescription  = "";
       $webLinks            = "";
       $identityDocumentsNumber     = "";
       $identityDocumentsType       = "";
       $identityDocumentsissueDate  = "";
       $identityDocumentsexpiryDate = "";

       ///GET NAMES


       $nameArray = $resultArray->names;
        if(count($nameArray) > 0) {
           foreach($nameArray as $nameData)
              {
               if($nameData->type=="PRIMARY") {
                  $fullName = $nameData->fullName;
               }
              }

       }


       ////Events DOB
       $eventsArray = $resultArray->events;
       if(count($eventsArray) > 0) {
           foreach($eventsArray as $eventsData)
              {
               if($eventsData->type=="BIRTH") {
                  $eventsDataDOB = $eventsData->fullDate;
               }
              }

       }


       ////Role Title
       $rolesArray = $resultArray->roles;
       if(count($rolesArray) > 0) {
           foreach($rolesArray as $roleData)
              {
               if($roleData->type=="type") {
                  $roleTitle = $roleData->title;
               }
              }

       }


       ////weblinks
       $weblinksArray = $resultArray->weblinks;
       if(count($weblinksArray) > 0) {

           foreach($weblinksArray as $weblinksData)
              {
               if($weblinksData->uri!="") {
                  $webLinks.= $weblinksData->uri."<br>";
                }
              }

       }

       /// Source Links
       $sourcesArray = $resultArray->sources;
       if(count($sourcesArray) > 0) {

           foreach($sourcesArray as $sourcesData)
              {
               if($sourcesData->name!="") {
                  $sourcesName = $sourcesData->name;
                  $sourcesDescription = $sourcesData->type->category->description;
                }
              }

       }

       ////Country Events
       $countryLinksArray = $resultArray->countryLinks;
       if(count($countryLinksArray) > 0) {
           foreach($countryLinksArray as $countryLinksData)
              {
               if($countryLinksData->type=="NATIONALITY") {
                  $countryLinksName = $countryLinksData->country->name;
               }
              }
       }
       ///Identity
       $identityDocumentsArray = $resultArray->identityDocuments;
       if(count($identityDocumentsArray) > 0) {
           foreach($identityDocumentsArray as $identityDocumentsData)
              {
               $identityDocumentsType = $identityDocumentsData->type;
               $identityDocumentsNumber = $identityDocumentsData->number;
               $identityDocumentsissueDate = $identityDocumentsData->issueDate;
               $identityDocumentsexpiryDate = $identityDocumentsData->expiryDate;

              }
       }


       echo "ReferenceId : ".$referenceId;
       echo "<br>Name : ".$fullName;
       echo "<br>Role Title : ".$roleTitle;
       //echo "<br>Web Links : ".$webLinks;
       echo "<br>Source Links : ".$sourcesName;
       echo "<br>Source Description : ".$sourcesDescription;
       echo "<br>Gender : ".$gender;
       echo "<br>DOB : ".$eventsDataDOB;
       echo "<br>Country : ".$countryLinksName;
//$BindData = '';
//$BindData = $referenceId."#".$primaryName."#".$category."#".$providerTypes."#".$gender."#".$eventsDataDOB."#".$countryLinksName."#".$identityDocumentsType."#".$identityDocumentsNumber;
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
        $invoice_data = $this->invRepo->getAllInvoice($this->request,7);
        $invoice = $dataProvider->getInvoiceList($this->request, $invoice_data);
        return $invoice;
    }
   //////////////////// use for invoice list/////////////////
     public function getBackendInvoiceList(DataProviderInterface $dataProvider) {
        $invoice_data = $this->invRepo->getAllInvoice($this->request,7);
       /// dd($invoice_data);
        $invoice = $dataProvider->getBackendInvoiceList($this->request, $invoice_data);
        return $invoice;
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
        $invoice_data = $this->invRepo->getAllInvoice($this->request,8);
        $invoice = $dataProvider->getBackendInvoiceListApprove($this->request, $invoice_data);
        return $invoice;
    } 
        
    
     //////////////////// use for exception case invoice list/////////////////
     public function getBackendEpList(DataProviderInterface $dataProvider) {
        $invoice_data = $this->invRepo->getAllInvoice($this->request,28);
        $invoice = $dataProvider->getBackendEpList($this->request, $invoice_data);
        return $invoice;
    } 
      //////////////////// use for Invoice Disbursed Que list/////////////////
     public function getBackendInvoiceListDisbursedQue(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,9);
        $invoice = $dataProvider->getBackendInvoiceListDisbursedQue($this->request, $invoice_data);
        return $invoice;
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
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,11);
        $invoice = $dataProvider->getBackendInvoiceListFailedDisbursed($this->request, $invoice_data);
        return $invoice;
    } 
    
      //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListDisbursed(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,12);
        $invoice = $dataProvider->getBackendInvoiceListDisbursed($this->request, $invoice_data);
        return $invoice;
    } 
    
     //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListRepaid(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,13);
        $invoice = $dataProvider->getBackendInvoiceListRepaid($this->request, $invoice_data);
        return $invoice;
    } 
    
      //////////////////// use for Invoice Disbursed  list/////////////////
     public function getBackendInvoiceListReject(DataProviderInterface $dataProvider) {
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,14);
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
            $path = Storage::disk('public')->put('/user/' . $userId . '/excelpayment', $request['upload'], null);
            $inputArr['file_path'] = $path;
       }
        $csvFilePath = storage_path("app/public/" . $inputArr['file_path']);
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
           
           if($request->status==8)
           {
              return  InvoiceTrait::updateApproveStatus($request);
              /*
              if ($result == 2) {
                    $invoice_id = $request->invoice_id;
                    $attr=[];
                    $attr['invoice_id'] = $invoice_id;
                    $attr['status'] = 28;
                    $attr['remark'] = 'Limit Exceed';
                    $attr['invoice_id'] = $invoice_id;
                    InvoiceTrait::updateInvoiceData($attr);
              }
               * 
               */              
           }
           else
           {
                $invoice_id = $request->invoice_id;
                $invData = $this->invRepo->getInvoiceData(['invoice_id' => $invoice_id],['supplier_id']);        
                $supplier_id = isset($invData[0]) ? $invData[0]->supplier_id : null;                                
                $isLimitExpired = InvoiceTrait::limitExpire($supplier_id);
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
              return \Response::json(['status' => $res]);
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
        // $checkRoleUserCRCPA = $this->userRepo->getAllRoleDataByUserId($getFiAddData[0]->from_id);
        $checkRoleUserCRCPA = AppAssignment::getAllRoleDataByUserIdAppID($getFiAddData[0]->from_id, $app_id);
        if(!empty($checkRoleUserCRCPA[0])) {
            $triggerUserCreData = $this->userRepo->getUserDetail($getFiAddData[0]->to_id);
            $trigger_email = $triggerUserCreData->email;
        }
        foreach ($fiLists as $key => $fiList) {
            foreach($fiList->fiAddress as $fiAdd) {
                $agencyName = $fiAdd->agency->comp_name;
            }
        }

        if(!empty($assignees[0])) {
            foreach ($assignees as $key => $value) {
                $userCreData = $this->userRepo->getUserDetail($value->from_user_id);
                $currUserData = $this->userRepo->getUserDetail($roleData);

                $emailDatas['email'] = isset($userCreData) ? $userCreData->email : '';
                $emailDatas['name'] = isset($userCreData) ? $userCreData->f_name . ' ' . $userCreData->l_name : '';
                $emailDatas['curr_user'] = isset($currUserData) ? $currUserData->f_name . ' ' . $currUserData->l_name : '';
                $emailDatas['curr_email'] = isset($currUserData) ? $currUserData->email : '';
                $emailDatas['comment'] = isset($comment) ? $comment : '';
                $emailDatas['trigger_type'] = 'FI';
                $emailDatas['subject'] = 'Case Id '. $request_info .' of Agency ' . $agencyName .' updated the status';
                $emailDatas['agency_name'] = $agencyName;
                $emailDatas['trigger_email'] = isset($trigger_email) ? $trigger_email : '';
                $emailDatas['change_status'] = config('common.FI_RCU_STATUS')[$changeStatus];
                \Event::dispatch("AGENCY_UPDATE_MAIL_TO_CPA_CR", serialize($emailDatas));
            }
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
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
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
        // $checkRoleUserCRCPA = $this->userRepo->getAllRoleDataByUserId($getFiAddData[0]->from_id);
        $checkRoleUserCRCPA = AppAssignment::getAllRoleDataByUserIdAppID($getFiAddData[0]->from_id, $app_id);
        if(!empty($checkRoleUserCRCPA[0])) {
            $triggerUserCreData = $this->userRepo->getUserDetail($getFiAddData[0]->from_id);
            $trigger_email = $triggerUserCreData->email;
        }
        if(!empty($assignees[0])) {
            foreach ($assignees as $key => $value) {
                $userCreData = $this->userRepo->getUserDetail($value->from_user_id);
                $currUserData = $this->userRepo->getUserDetail($roleData);

                $emailDatas['email'] = isset($userCreData) ? $userCreData->email : '';
                $emailDatas['name'] = isset($userCreData) ? $userCreData->f_name . ' ' . $userCreData->l_name : '';
                $emailDatas['curr_user'] = isset($currUserData) ? $currUserData->f_name . ' ' . $currUserData->l_name : '';
                $emailDatas['curr_email'] = isset($currUserData) ? $currUserData->email : '';
                $emailDatas['comment'] = isset($comment) ? $comment : '';
                $emailDatas['trigger_type'] = 'RCU';
                $emailDatas['subject'] = 'Case Id '. $request_info .' of Agency ' . $fiLists[0]['agencies'][0]->agency->comp_name .' updated the status';
                $emailDatas['agency_name'] = $fiLists[0]['agencies'][0]->agency->comp_name;
                $emailDatas['trigger_email'] = isset($trigger_email) ? $trigger_email : '';
                $emailDatas['change_status'] = config('common.FI_RCU_STATUS')[$changeStatus];
                \Event::dispatch("AGENCY_UPDATE_MAIL_TO_CPA_CR", serialize($emailDatas));
            }
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
    public function getProgramBalanceLimit(Request $request)
    {
        $program_id = (int)$request->program_id;
        $prgm_limit =  $this->application->getProgramBalanceLimit($program_id);                
        $prgm_data =  $this->application->getProgramData(['prgm_id' => $program_id]);
        $utilizedLimit = 0;
        if ($prgm_data && $prgm_data->copied_prgm_id) {            
            $utilizedLimit = \Helpers::getPrgmBalLimit($prgm_data->copied_prgm_id);
        }
        return json_encode(['prgm_limit' => $prgm_limit + $utilizedLimit , 'prgm_data' => $prgm_data]);
    }
    
     public function getProgramSingleList(Request $request)
     {
       
         $get_program = $this->invRepo->getLimitProgram($request['anchor_id']);
         $get_program_limit = $this->invRepo->geAnchortLimitProgram($request['anchor_id']);
         return response()->json(['status' => 1,'limit' => $get_program_limit,'get_program' =>$get_program]);
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
        $sum   =   InvoiceTrait::invoiceApproveLimit($res);
        $is_adhoc   =  $this->invRepo->checkUserAdhoc($res);
        $remainAmount = $limit-$sum;
        return response()->json(['status' => 1,'tenor' => $getTenor['tenor'],'tenor_old_invoice' =>$getTenor['tenor_old_invoice'],'limit' => $limit,'remain_limit' =>$remainAmount,'is_adhoc' => $is_adhoc]);
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
        $limit   =  $this->invRepo->checkUserAdhoc($res);
        $sum     = InvoiceTrait::adhocLimit($res);
        $remainAmount = $limit-$sum;
        $is_adhoc = 1;
       }
       else
       {
        $limit =   InvoiceTrait::ProgramLimit($res);
        $sum   =   InvoiceTrait::invoiceApproveLimit($res);
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
      
       $result = InvoiceTrait::checkInvoiceLimitExced($request); 
       foreach($request['invoice_id'] as $row)
       {  
          if($request->status==8)
          {
            $attr['invoice_id']=$row; 
            $response =  InvoiceTrait::updateApproveStatus($attr);  
         
          }
          else
          {
             //$this->invRepo->updateInvoice($row,$request->status);
            //$result = '';
            $invoice_id = $row;
            $invData = $this->invRepo->getInvoiceData(['invoice_id' => $invoice_id],['supplier_id']);        
            $supplier_id = isset($invData[0]) ? $invData[0]->supplier_id : null;
            $isLimitExpired = InvoiceTrait::limitExpire($supplier_id);
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
       
      return \response()->json(['status' => 1,'msg' => substr($result,0,-1)]); 
       
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

        $request = $this->request;
        $transactionList = $this->lmsRepo->getSoaList();
        
        if($request->get('from_date')!= '' && $request->get('to_date')!=''){
            $transactionList = $transactionList->where(function ($query) use ($request) {
                $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                $query->WhereBetween('sys_created_at', [$from_date, $to_date]);
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
                    $transactionList = $transactionList->where('entry_type',$entry_type);
                }
            }
        }

        $transactionList = $transactionList->whereHas('lmsUser',function ($query) use ($request) {
            $customer_id = trim($request->get('customer_id')) ?? null ;
            $query->where('customer_id', '=', "$customer_id");
        })
        ->get()
        ->filter(function($item){
            return $item->IsTransaction;
        });

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
        $users = $dataProvider->getSoaList($this->request, $transactionList);
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
        $arrData = GroupCompanyExposure::where(['group_Id'=>$groupId, 'is_active'=>1])->groupBy('group_company_name')->get();
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
    
    public function updateGroupCompanyExposure(Request $request ){
        $group_company_expo_id = $request->get('group_company_expo_id');
        $arrData = GroupCompanyExposure::where("group_company_expo_id", $group_company_expo_id)->update(['is_active' => 2]);
        if($arrData){
            $status = true; 
        }else{
          $status = false;
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
        $this->dataRecords = [];
        if (!empty($user_id)) {
            $this->dataRecords = Payment::getPayments(['is_settled' => 0, 'user_id' => $user_id],['created_at'=>'desc']);
        } else {
            $this->dataRecords = Payment::getPayments(['is_settled' => 0],['created_at'=>'desc']);
        }
        $this->providerResult = $dataProvider->getToSettlePayments($this->request, $this->dataRecords);
        return $this->providerResult;
    }

    public function getSettledPayments(DataProviderInterface $dataProvider) {
        $user_id = $this->request->user_id;
        $this->dataRecords = [];
        if (!empty($user_id)) {
            $this->dataRecords = Payment::getPayments(['is_settled' => 1, 'user_id' => $user_id],['updated_at'=>'desc']);
        } else {
            $this->dataRecords = Payment::getPayments(['is_settled' => 1],['updated_at'=>'desc']);
        }
        $this->providerResult = $dataProvider->getToSettlePayments($this->request, $this->dataRecords);
        return $this->providerResult;
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
        $leaseRegisters     = $leaseRegisters->getData(true);
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

            if (isset($anchUserData[0])) {
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
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
        if($getAnchor->is_phy_inv_req === '1') {
            return $respose = ['status'=>'1'];
        } else {
            return $respose = ['status'=>'0'];
        }
    }
}