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
use App\Inv\Repositories\Models\RightCommission;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Http\Requests\Company\ShareholderFormRequest;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\UserReqDoc;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\Master\Group;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Inv\Repositories\Models\GroupCompanyExposure;
use App\Inv\Repositories\Models\Lms\Transactions;

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


    function __construct(Request $request, InvUserRepoInterface $user, InvAppRepoInterface $application,InvMasterRepoInterface $master, InvoiceInterface $invRepo,InvDocumentRepoInterface $docRepo, FinanceInterface $finRepo, InvLmsRepoInterface $lms_repo) {
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
                                
                                ////
                                
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
        $appList = $this->application->getApplicationPoolData()->get();
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
     $anchUsersList = $this->userRepo->getAllAnchor();
     $users = $dataProvider->getAnchorList($this->request, $anchUsersList);
     return $users;
    }
    
    public function getAnchorLeadLists(DataProviderInterface $dataProvider){
      $anchLeadList = $this->userRepo->getAllAnchorUsers();
        $users = $dataProvider->getAnchorLeadList($this->request, $anchLeadList);
        return $users; 
    }

    public function checkExistUser(Request $request) {
        dd($request);
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
        $invoice_data = $this->invRepo->getAllInvoice($this->request,7);
       /// dd($invoice_data);
        $invoice = $dataProvider->getBackendInvoiceList($this->request, $invoice_data);
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
       
        $invoice_data = $this->invRepo->getAllInvoice($this->request,10);
        $invoice = $dataProvider->getBackendInvoiceListBank($this->request, $invoice_data);
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
     return  $this->invRepo->updateInvoice($request->invoice_id,$request->status);
   
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
      $status = $this->application->changeAgentFiStatus($request);
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
     $chargesTransList = $this->lmsRepo->getAllTransCharges();
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
              $request['chrg_applicable_id']  = $getamount->chrg_applicable_id; 
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
                     $limitAmount  = $limitAmount[0];
                   
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
                 'amount' => number_format($getamount->chrg_calculation_amt),
                 'id' => $getamount->id,
                 'limit' => $limitAmount,
                 'type' => $getamount->chrg_calculation_type,
                 'is_gst_applicable' => $getamount->is_gst_applicable,
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
        return json_encode(['prgm_limit' => $prgm_limit, 'prgm_data' => $prgm_data]);
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
        //$get_supplier = $this->invRepo->getLimitSupplier($request['program_id']);
        $get_supplier = $this->invRepo->getProgramOfferByPrgmId($request['program_id']);
        $all_supplier=[];
        foreach($get_supplier as $supplier) {
            $supplier->appCode = \Helpers::formatIdWithPrefix($supplier->app_id, 'APP');
            $all_supplier[] =  $supplier;       
        }
        return response()->json(['status' => 1,'limit' => $getProgramLimit,'offer_id' => $getOfferProgramLimit->prgm_offer_id,'tenor' => $getOfferProgramLimit->tenor,'tenor_old_invoice' =>$getOfferProgramLimit->tenor_old_invoice,'get_supplier' =>$get_supplier]);
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
 
    function uploadInvoice(Request $request) {
      
       $extension = $request['doc_file']->getClientOriginalExtension();
       if($extension!="csv" || $extension!="csv")
       {
            return response()->json(['status' => 2]); 
       }
        $date = Carbon::now();
        $data = array();
        $id = Auth::user()->user_id;
        $explode  =  explode(',',$request['supplier_bulk_id']);
        $attributes['supplier_bulk_id']      =    $explode[0];
        $userId     =    $attributes['supplier_bulk_id'];
        $appId   = $explode[1]; 
        if ($request['doc_file']) {
            if (!Storage::exists('/public/user/' . $userId . '/invoice')) {
                Storage::makeDirectory('/public/user/' . $userId . '/invoice', 0775, true);
            }
            $path = Storage::disk('public')->put('/user/' . $userId . '/invoice', $request['doc_file'], null);
            $inputArr['file_path'] = $path;
        }
        $batch_id =  $this->invRepo->saveBatchNo($path); 
        $csvFilePath = storage_path("app/public/" . $inputArr['file_path']);
        $file = fopen($csvFilePath, "r");
        while (!feof($file)) {
          
            $rowData[] = explode(",",fgets($file));
          }
       
        $i=0;
      
        $res =  $this->invRepo->getSingleAnchorDataByAppId($appId);
        $biz_id  = $res->biz_id;
       
        $rowcount = count($rowData) -1;
        foreach($rowData as $key=>$row)
        {
        
          if($i > 0 && $i < $rowcount)  
          {
               
                $whr  = ['status' =>0,'anchor_id' =>$request['anchor_bulk_id'],'supplier_id' => $request['supplier_bulk_id'], 'program_id' => $request['program_bulk_id']];
                $invoice_no  = $row[0];
                $invoice_date  = $row[1];
                $invoice_due_date  = $row[2];
                $invoice_amount  = $row[3];
                $invoice_amount  = str_replace("\n","",$invoice_amount);
                $invoice_due_date_validate  = $this->validateDate($invoice_due_date, $format = 'd/m/Y');
                $invoice_date_validate  = $this->validateDate($invoice_date, $format = 'd/m/Y');
                $res =  $this->invRepo->checkDuplicateInvoice($invoice_no,$attributes['supplier_bulk_id']);
                if(strlen($invoice_date) < 10)
               {
                    return response()->json(['status' => 0,'message' => 'Please check the  invoice date, It Should be "dd/mm/yy" format']); 
               } 
               else if(strlen($invoice_due_date) < 10)
               {
                    return response()->json(['status' => 0,'message' => 'Please check the due invoice date, It Should be "dd/mm/yy" format']); 
               } 
               else if($invoice_no=='')
               {
                    return response()->json(['status' => 0,'message' => 'Please check invoice , Invoice should not be null']); 
               } 
               else if( $invoice_due_date_validate==false)
               {
                    return response()->json(['status' => 0,'message' => 'Please check the invoice date, It should be "dd/mm/yy" format']); 
               }
              else if( $invoice_date_validate==false)
               {
                    return response()->json(['status' => 0,'message' => 'Please check the due invoice date, It Should be "dd/mm/yy" format']); 
               } 
               
               else if(strtotime(Carbon::createFromFormat('d/m/Y', $invoice_due_date)) < strtotime(Carbon::parse($date)->format('d-m-Y')))
               {
                   return response()->json(['status' => 0,'message' => 'Please check the due invoice date, It should be greater than current date']); 
               }
               else if(strtotime(Carbon::createFromFormat('d/m/Y', $invoice_date)) > strtotime(Carbon::parse($date)->format('d-m-Y')))
               {
                   return response()->json(['status' => 0,'message' => 'Please check the  invoice date, It should be less than current date']); 
               }
               else if($invoice_amount=='')
               {
                    return response()->json(['status' => 0,'message' => 'Please check invoice amount, Amount should not be null']); 
               } 
               else if(!is_numeric($invoice_amount))
               {
                    return response()->json(['status' => 0,'message' => 'Please check invoice amount, string value not allowed']); 
               } 
               else if($res)
               {
                   return response()->json(['status' => 0,'message' => 'Please check invoice no, some one Invoice No already exists']); 
               }
                $invoice_amount =  $invoice_amount;
                $data[$i]['anchor_id'] =  $request['anchor_bulk_id'];
                $data[$i]['supplier_id'] = $request['supplier_bulk_id']; 
                $data[$i]['program_id'] = $request['program_bulk_id'];
                $data[$i]['app_id']    = $appId;
                $data[$i]['biz_id']  = $biz_id;
                $data[$i]['invoice_no'] = $invoice_no;
                $data[$i]['tenor'] =  $request['tenor']; 
                $data[$i]['invoice_due_date'] = ($invoice_due_date) ? Carbon::createFromFormat('d/m/Y', $invoice_due_date)->format('Y-m-d') : '';
                $data[$i]['invoice_date'] = ($invoice_date) ? Carbon::createFromFormat('d/m/Y', $invoice_date)->format('Y-m-d') : '';
                $data[$i]['invoice_approve_amount'] =  $invoice_amount;
                $data[$i]['is_bulk_upload'] = 1;
                $data[$i]['batch_id'] = $batch_id;
                $data[$i]['created_by'] =  $id;
                $data[$i]['created_at'] =  $date;
          }
           $i++;
        }
            
              
                if(count($data) > 0)
               {
                  $res = $this->invRepo->DeleteTempInvoice($whr);  
                  $result = $this->invRepo->saveBulkTempInvoice($data);
                  if( $result)
                  {
                      $getTempInvoice =  $this->invRepo->getTempInvoiceData($whr);
                      return response()->json(['status' => 1,'data' =>$getTempInvoice]); 
                  }
                    else {
                        return response()->json(['status' => 0,'message' => 'Something wrong, Please try again']); 
                    }
                }  
                  else {
                        return response()->json(['status' => 0,'message' => 'Something wrong, Please try again']); 
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
       
        $whr =  ['invoice_id' => $request->temp_id];
        $res = $this->invRepo->DeleteSingleTempInvoice($whr);
        return response()->json(['status' => 1,'id' => $request->temp_id]); 
        
    }
    
   function updateBulkInvoice(Request $request)
   {
       foreach($request['invoice_id'] as $row) {  
        $res =   $this->invRepo->updateInvoice($row,$request->status);
       }
       return  $res;
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
        $checkPer =  (int) Helpers::checkPermissionAssigntoRole(104, $role_id);
        if(!$checkPer){
            return \response()->json(['success' => false , 'messges'=>'For this role you do not have permission to Approve the application.']);
        }
        
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
     $getDisList = $this->userRepo->getDisbursalList();
     //dd($getDisList->get());
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
        $customersList = $this->application->addressGetCustomers($user_id, $bizId);
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
   * Get all transactions for soa
   *
   * @return json transaction data
   */
    public function lmsGetSoaList(DataProviderInterface $dataProvider) {

        $transactionList = $this->lmsRepo->getSoaList();
        $users = $dataProvider->getSoaList($this->request, $transactionList);
        return $users;
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
        $data = $dataProvider->getRequestList($this->request, $requestData);
        return $data;
    }
    
    public function getAllBaseRateList(DataProviderInterface $dataProvider) { 
     $baseRateList = $this->masterRepo->getAllBaseRateList();
//     dd($baseRateList);
     $baserates = $dataProvider->getBaseRateList($this->request, $baseRateList);
     return $baserates;
    }

    public function getColenderAppList(DataProviderInterface $dataProvider) {
        $appList = $this->application->getColenderApplications();
        $applications = $dataProvider->getColenderAppList($this->request, $appList);
        return $applications;
    }

    public function getTransactions(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->finRepo->getTransactions();
        $this->providerResult = $dataProvider->getTransactionsByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
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
        
        $debitAmt = 0;
        $creditAmt = 0;
        
        if (isset($repaymentAmtData['debitAmtData']['amount'])) {
            $debitAmt = $repaymentAmtData['debitAmtData']['amount']; //+ $repaymentAmtData['debitAmtData']['cgst'] + $repaymentAmtData['debitAmtData']['sgst'] + $repaymentAmtData['debitAmtData']['igst'];
        }
        if (isset($repaymentAmtData['creditAmtData']['amount'])) {
            $creditAmt = $repaymentAmtData['creditAmtData']['amount']; //+ $repaymentAmtData['creditAmtData']['cgst'] + $repaymentAmtData['creditAmtData']['sgst'] + $repaymentAmtData['creditAmtData']['igst'];
        }        
        $repaymentAmount = $debitAmt >= $creditAmt ? $debitAmt - $creditAmt : 0;
        return response()->json(['repayment_amount' => number_format($repaymentAmount, 2)]);
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

    public function getAjaxBankInvoice(DataProviderInterface $dataProvider) { 
        $this->dataRecords = $this->invRepo->getAllBankInvoice();
        $this->providerResult = $dataProvider->getBankInvoiceByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }


    public function getAjaxBankInvoiceCustomers(Request $request, DataProviderInterface $dataProvider) { 
        $batch_id    = $request->get('batch_id');
        $this->dataRecords = $this->invRepo->getAllBankInvoiceCustomers($batch_id);
        $this->providerResult = $dataProvider->getBankInvoiceCustomersByDataProvider($this->request, $this->dataRecords);
        return $this->providerResult;
    }
    
}
