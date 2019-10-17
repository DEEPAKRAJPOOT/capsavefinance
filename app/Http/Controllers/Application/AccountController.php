<?php

namespace App\Http\Controllers\Application;

use Auth;
use File;
use Session;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalProfileFormRequest;
use App\Http\Requests\FamilyFormRequest;
use App\Http\Requests\ResidentialFormRequest;
use App\Http\Requests\ProdessionalFormRequest;
use App\Http\Requests\CommercialFormRequest;
use App\Http\Requests\FinancialFormRequest;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Models\UserReqDoc;
use App\Inv\Repositories\Models\Userkyc;
use App\Inv\Repositories\Models\Document;

class AccountController extends Controller {

    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Application repository
     *
     * @var object
     */
    protected $application;

    use StorageAccessTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $application, StorageManagerInterface $storage) {
        $this->middleware('auth');
        $this->userRepo = $user;
        $this->application = $application;
        $this->storage = $storage;
    }

    /**
     * Show the User KYC Personal Information
     *
     * @return Response
     */
    public function index(Request $request) {
        
        //if(@$request->get('corp_user_id')!=null && @$request->get('user_kyc_id')!=null){
            $corp_user_id = @$request->get('corp_user_id');
            $user_kyc_id = @$request->get('user_kyc_id');

            $recentRights = [];
            $benifinary = [];
            $userPersonalData = [];
            $userDocumentType = [];
            $userSocialMedia = [];
       
            if ($corp_user_id > 0 && $user_kyc_id > 0) {

                $benifinary['user_kyc_id'] = (int) $user_kyc_id;
                $benifinary['corp_user_id'] = (int) $corp_user_id;
                $benifinary['is_by_company'] = 1;
                $userKycId = (int) $user_kyc_id;
                $userId = null;
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
                $benifinary['corp_user_id'] = 0;
                $benifinary['is_by_company'] = 0;
            }
          
            $resData = $this->userRepo->getUseKycPersonalData($userKycId);
            $resDocumentType = $this->userRepo->getDocumentTypeInfo($userKycId);

            $resSocialMedia = $this->userRepo->getSocialmediaInfo($userKycId);


            if ($resDocumentType) {
                if ($resDocumentType->count()) {
                    $userDocumentType = $resDocumentType->toArray();
                }
            }

            if ($resSocialMedia) {
                if ($resSocialMedia->count()) {
                    $userSocialMedia = $resSocialMedia->toArray();
                }
            }


            if ($resData) {
                if ($resData->count()) {

                    $userPersonalData = $resData->toArray();
                }
            }
            

            return view('frontend.profile', compact('userPersonalData', 'benifinary', 'userDocumentType', 'userSocialMedia'));
        /*}else{
            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $corpUserId = 0;
            $isBycompany = 0;
            
            return redirect(route('profile', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            
        }*/
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    /* public function editPersonalProfile(Request $request) {
      $userId = Auth::user()->user_id;

      //get user detail
      $userData = [];
      $userId = (int) Auth::user()->user_id;
      $resData = $this->userRepo->getPersonalInfo($userId);

      if ($resData->count()) {
      $userData = $resData->toArray();
      }

      return view('frontend.profile', compact('userData'));
      } */

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function savePersonalProfile(PersonalProfileFormRequest $request) {
        try {

            $data = [];
            $userProfile = $request->file('user_photo');
            $requestVar = $request->all();
            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;

            $date_of_birth = null;
            $legal_maturity_date = null;

            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = (int) @$requestVar['corp_user_id'];
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }

            $requestVar = $request->all();
            $inputData = [];
            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;
            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;
            $inputData['title'] = @$requestVar['title'];
            $inputData['f_name'] = @$requestVar['f_name'];
            $inputData['m_name'] = @$requestVar['m_name'];
            $inputData['l_name'] = @$requestVar['l_name'];
            $inputData['gender'] = @$requestVar['gender'];
            $inputData['date_of_birth'] = isset($requestVar['date_of_birth']) ? Helpers::getDateByFormat(@$requestVar['date_of_birth'], 'd/m/Y', 'Y-m-d') : null;
            $inputData['birth_country_id'] = @$requestVar['birth_country_id'];
            $inputData['birth_city_id'] = @$requestVar['birth_city_id'];
            $inputData['birth_state_id'] = @$requestVar['birth_state_id'];
            $inputData['father_name'] = @$requestVar['father_name'];
            $inputData['mother_f_name'] = @$requestVar['mother_f_name'];
            $inputData['mother_m_name'] = @$requestVar['mother_m_name'];
            $inputData['reg_no'] = @$requestVar['reg_no'];
            $inputData['reg_place'] = @$requestVar['reg_place'];
            $inputData['f_nationality_id'] = @$requestVar['f_nationality_id'];
            $inputData['sec_nationality_id'] = @$requestVar['sec_nationality_id'];
            $inputData['residence_status'] = @$requestVar['residence_status'];
            $inputData['family_status'] = @$requestVar['family_status'];
            $inputData['guardian_name'] = @$requestVar['guardian_name'];
            $inputData['legal_maturity_date'] = isset($requestVar['legal_maturity_date']) ? Helpers::getDateByFormat(@$requestVar['legal_maturity_date'], 'd/m/Y', 'Y-m-d') : null;
            $inputData['educational_level'] = @$requestVar['educational_level'];
            $inputData['is_residency_card'] = @$requestVar['is_residency_card'];
            $inputData['political_position'] = @$requestVar['political_position'];
            $inputData['political_position_dec'] = @$requestVar['political_position_dec'];
            $inputData['related_political_position'] = @$requestVar['related_political_position'];
            $inputData['related_political_position_dec'] = @$requestVar['related_political_position_dec'];
            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;
            //dd($inputData);
            $this->userRepo->storeUseKycPersonalData($inputData, $id);

            $this->userRepo->deleteDocumentType($userKycId);

            $documentData = [];
            $documentIds = @$requestVar['document_type_id'];
            $documentNumbers = @$requestVar['document_number'];
            $issuanceDates = @$requestVar['issuance_date'];
            $expiryDates = @$requestVar['expiry_date'];

            foreach ($documentIds as $key => $docIds) {

                $documentData['document_type'] = $docIds;
                $documentData['document_number'] = $documentNumbers[$key];
                $documentData['issuance_date'] = isset($issuanceDates[$key]) ? Helpers::getDateByFormat($issuanceDates[$key], 'd/m/Y', 'Y-m-d') : null;
                $documentData['expire_date'] = isset($expiryDates[$key]) ? Helpers::getDateByFormat($expiryDates[$key], 'd/m/Y', 'Y-m-d') : null;

                $documentData["user_kyc_id"] = $userKycId;
                $documentData["created_by"] = $userId;
                $documentData["updated_by"] = $userId;

                $this->userRepo->storeUserKycDocumentTypeData($documentData);
            }

//==== update social media link

            $this->userRepo->deleteSocialmediaInfo($userKycId);
            $socialmediaData = [];
            $socialmediaIds = @$requestVar['social_media_id'];
            $socialmediaLinks = @$requestVar['social_media_link'];

            foreach ($socialmediaIds as $key => $docIds) {
                $socialmediaData['social_media'] = $docIds;
                $socialmediaData['social_media_link'] = $socialmediaLinks[$key];
                $socialmediaData["user_kyc_id"] = $userKycId;
                $socialmediaData["created_by"] = $userId;
                $socialmediaData["updated_by"] = $userId;
                $this->userRepo->storeUserKycSocialmediaData($socialmediaData);
            }

            Session::flash('message', trans('success_messages.update_personal_successfully'));

            if (@$request->save !== '' && @$request->save != null) {
                return redirect(route('profile', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }

            if (@$request->save_next !== '' && @$request->save_next != null) {
                return redirect(route('family_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Family Information
     *
     * @return Response
     */
    public function editFamilyInformation(Request $request) {

        $userData = [];
        $benifinary = [];
        $corp_user_id = @$request->get('corp_user_id');
        $user_kyc_id = @$request->get('user_kyc_id');

        if ($corp_user_id > 0 && $user_kyc_id > 0) {

            $benifinary['user_kyc_id'] = (int) $user_kyc_id;
            $benifinary['corp_user_id'] = (int) $corp_user_id;
            $benifinary['is_by_company'] = 1;
            $userKycId = (int) $user_kyc_id;
            $userId = null;
        } else {

            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
            $benifinary['corp_user_id'] = 0;
            $benifinary['is_by_company'] = 0;
        }

        $personalData = $this->userRepo->getUseKycPersonalData($userKycId);
        if ($personalData->family_status == '2') {
            $resData = $this->userRepo->getFamilyInfo($userKycId);


            if ($resData && $resData->count()) {
                $userData = $resData->toArray();
            }
            return view('frontend.family_information', compact('userData', 'benifinary'));
        } else {
            return redirect(route('residential_information', ['user_kyc_id' => $benifinary['user_kyc_id'], 'corp_user_id' => $benifinary['corp_user_id'], 'is_by_company' => $benifinary['is_by_company']]));
        }
    }

    /*     * FamilyFormRequest 
     * save Family Information
     *
     * @return Response
     */

    public function saveFamilyInformation(FamilyFormRequest $request) {
        try {
            $requestVar = $request->all();


            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = 0;
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }


            $inputData = [];

            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;


            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;

            $inputData['spouse_f_name'] = @$requestVar['spouse_f_name'];
            $inputData['spouse_m_name'] = @$requestVar['spouse_m_name'];
            $inputData['is_spouse_profession'] = @$requestVar['is_professional_status'];
            $inputData['spouse_profession'] = @$requestVar['spouse_profession'];
            $inputData['spouse_employer'] = @$requestVar['spouse_employer'];
            $inputData['is_child'] = @$requestVar['is_child'];

            $childInfo = [];

            if ($inputData['is_child'] != '1') {
                $childNames = @$requestVar['child_name'];
                $childDOBs = @$requestVar['child_dob'];
                foreach ($childNames as $key => $child_name) {
                    $inputData2 = [];
                    $inputData2['child_name'] = $child_name;
                    $inputData2['child_dob'] = $childDOBs[$key];
                    $childInfo[$key] = $inputData2;
                }
            }

            $inputData['spouce_child_info'] = json_encode($childInfo);
            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;


            $this->userRepo->storeUseKycFamilyData($inputData, $id);



            Session::flash('message', trans('success_messages.update_family_successfully'));

            if (@$request->save !== '' && @$request->save != null) {
                return redirect(route('family_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }

            if (@$request->save_next !== '' && @$request->save_next != null) {
                return redirect(route('residential_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Residential Information
     *
     * @return Response
     */
    public function editResidentialInformation(Request $request) {

        $corp_user_id = @$request->get('corp_user_id');
        $user_kyc_id = @$request->get('user_kyc_id');
        //echo $user_kyc_id."==".$corp_user_id;
        $userData = [];
        $benifinary = [];


        if ($corp_user_id > 0 && $user_kyc_id > 0) {

            $benifinary['user_kyc_id'] = (int) $user_kyc_id;
            $benifinary['corp_user_id'] = (int) $corp_user_id;
            $benifinary['is_by_company'] = 1;
            $userKycId = (int) $user_kyc_id;
            $userId = null;
        } else {
            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
            $benifinary['corp_user_id'] = 0;
            $benifinary['is_by_company'] = 0;
        }


        $resData = $this->userRepo->getResidentialInfo($userKycId);
        if ($resData && $resData->count()) {
            $userData = $resData->toArray();
        }
        return view('frontend.residential_information', compact('userData', 'benifinary'));
    }

    /**
     * save Residential Information
     *
     * @return Response
     */
    public function saveResidentialInformation(ResidentialFormRequest $request) {

        try {

            $requestVar = $request->all();

           // echo "<pre>";
           // print_r($requestVar);
          //  exit;
            $inputData = [];
            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = 0;
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }

            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;

            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;
            $inputData['country_id'] = @$requestVar['country_id'];
            $inputData['city_id'] = @$requestVar['city_id'];
            $inputData['region'] = @$requestVar['region'];
            $inputData['building_no'] = @$requestVar['building_no'];
            $inputData['floor_no'] = @$requestVar['floor_no'];
            $inputData['street_addr'] = @$requestVar['street_addr'];
            $inputData['postal_code'] = @$requestVar['postal_code'];
            $inputData['post_box'] = @$requestVar['post_box'];
            $inputData['addr_email'] = @$requestVar['addr_email'];
            $inputData['addr_phone_no'] = @$requestVar['addr_phone_no'];
            $inputData['addr_mobile_no'] = @$requestVar['addr_mobile_no'];
            $inputData['addr_fax_no'] = @$requestVar['addr_fax_no'];
            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;

            //dd($inputData);
            $rs=$this->userRepo->storeUserKycResidentialData($inputData, $id);
            //dd($rs);

            Session::flash('message', trans('success_messages.update_residential_successfully'));
            if (@$request->save !== '' && @$request->save != null) {
                return redirect(route('residential_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            } else if (@$request->save_next !== '' && @$request->save_next != null) {

                return redirect(route('professional_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Professional Information
     *
     * @return Response
     */
    public function editProfessionalInformation(Request $request) {

        $corp_user_id = @$request->get('corp_user_id');
        $user_kyc_id = @$request->get('user_kyc_id');

        $userData = [];
        $benifinary = [];


        if ($corp_user_id > 0 && $user_kyc_id > 0) {

            $benifinary['user_kyc_id'] = (int) $user_kyc_id;
            $benifinary['corp_user_id'] = (int) $corp_user_id;
            $benifinary['is_by_company'] = 1;
            $userKycId = (int) $user_kyc_id;
            $userId = null;
        } else {
            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
            $benifinary['corp_user_id'] = 0;
            $benifinary['is_by_company'] = 0;
        }

        $resData = $this->userRepo->getProfessionalInfo($userKycId);
        if ($resData && $resData->count()) {
            $userData = $resData->toArray();
        }
        return view('frontend.professional_information', compact('userData', 'benifinary'));
    }

    /**
     * save Professional Information
     *
     * @return Response
     */
    public function saveProfessionalInformation(ProdessionalFormRequest $request) {
        try {

            $requestVar = $request->all();
            $inputData = [];

            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = 0;
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }

            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;
            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;
            $inputData['prof_status'] = isset($requestVar['prof_status'])? $requestVar['prof_status'] :''; 
            $inputData['prof_detail'] = isset($requestVar['prof_detail'])? $requestVar['prof_detail'] :'';
            $inputData['position_title'] = isset($requestVar['position_title'])? $requestVar['position_title'] :'';
            $inputData['date_employment'] = isset($requestVar['date_employment']) ? Helpers::getDateByFormat($requestVar['date_employment'], 'd/m/Y', 'Y-m-d') : null;
            $inputData['last_monthly_salary'] = isset($requestVar['position_title'])? (double) @$requestVar['last_monthly_salary'] :0;
            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;


            $this->userRepo->storeUserKycProfessionalData($inputData, $id);

            Session::flash('message', trans('success_messages.update_professional_successfully'));


            if (@$request->save != '' && @$request->save != null) {
                return redirect(route('professional_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            } else if (@$request->save_next !== '' && @$request->save_next != null) {
                return redirect(route('commercial_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Commercial Information
     *
     * @return Response
     */
    public function editCommercialInformation(Request $request) {
//============================
        $corp_user_id = @$request->get('corp_user_id');
        $user_kyc_id = @$request->get('user_kyc_id');

        $userData = [];
        $benifinary = [];

        if ($corp_user_id > 0 && $user_kyc_id > 0) {

            $benifinary['user_kyc_id'] = (int) $user_kyc_id;
            $benifinary['corp_user_id'] = (int) $corp_user_id;
            $benifinary['is_by_company'] = 1;
            $userKycId = (int) $user_kyc_id;
            $userId = null;
        } else {
            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
            $benifinary['corp_user_id'] = 0;
            $benifinary['is_by_company'] = 0;
        }

        $prof_status = $this->userRepo->getProfessionalInfo($userKycId);

        if ($prof_status && in_array($prof_status->prof_status, ['3', '4'])) {

            $bussData = [];
            $resData = $this->userRepo->getCommercialInfo($userKycId);
            $resBussData = $this->userRepo->getBussAddrInfo($userKycId);
            if ($resData && $resData->count()) {
                $userData = $resData->toArray();
            }

            if ($resBussData && $resBussData->count()) {
                $bussData = $resBussData->toArray();
            }

            return view('frontend.commercial_information', compact('userData', 'benifinary', 'bussData'));
        } else {
            return redirect(route('financial_information', ['user_kyc_id' => $user_kyc_id, 'corp_user_id' => $corp_user_id, 'is_by_company' => $benifinary['is_by_company']]));
        }
    }

    /**
     * save Commercial Information
     *
     * @return Response
     */
    public function saveCommercialInformation(CommercialFormRequest $request) {
        try {

            $requestVar = $request->all();
            $inputData = [];

            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = 0;
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }

            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;

            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;
            $inputData['comm_name'] = @$requestVar['comm_name'];
            $inputData['date_of_establish'] = isset($requestVar['date_of_establish']) ? Helpers::getDateByFormat($requestVar['date_of_establish'], 'd/m/Y', 'Y-m-d') : null; //@$requestVar['date_of_establish'];

            $inputData['country_establish_id'] = @$requestVar['country_establish_id'];
            $inputData['comm_reg_no'] = @$requestVar['comm_reg_no'];
            $inputData['comm_reg_place'] = @$requestVar['comm_reg_place'];
            $inputData['comm_country_id'] = @$requestVar['comm_country_id'];
            $inputData['country_activity'] = @$requestVar['country_activity'];

            $inputData['syndicate_no'] = @$requestVar['syndicate_no'];
            $inputData['taxation_no'] = @$requestVar['taxation_no'];
            $inputData['taxation_id'] = @$requestVar['taxation_id'];
            $inputData['annual_turnover'] = @$requestVar['annual_turnover'];
            $inputData['main_suppliers'] = @$requestVar['main_suppliers'];
            $inputData['main_clients'] = @$requestVar['main_clients'];
            $inputData['authorized_signatory'] = @$requestVar['authorized_signatory'];
            $inputData['fax_no'] = @$requestVar['buss_fax_no'];
            $inputData['mailing_address'] = @$requestVar['mailing_address'];
            $inputData['relation_exchange_company'] = @$requestVar['relation_exchange_company'];
            $inputData['concerned_party'] = @$requestVar['concerned_party'];
            $inputData['details_of_company'] = @$requestVar['details_of_company'];
            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;
//dd($inputData);
            $this->userRepo->storeUserKycCommercialData($inputData, $id);
            $buss_addr_id = (@$requestVar['buss_addr_id'] != '' && @$requestVar['buss_addr_id'] != null) ? (int) $requestVar['buss_addr_id'] : null;

            $bussData['user_kyc_id'] = $userKycId;
            $bussData['buss_country_id'] = @$requestVar['buss_country_id'];
            $bussData['buss_city_id'] = @$requestVar['buss_city_id'];
            $bussData['buss_region'] = @$requestVar['buss_region'];
            $bussData['buss_building'] = @$requestVar['buss_building'];
            $bussData['buss_floor'] = @$requestVar['buss_floor'];
            $bussData['buss_street'] = @$requestVar['buss_street'];
            $bussData['buss_postal_code'] = @$requestVar['buss_postal_code'];
            $bussData['buss_po_box_no'] = @$requestVar['buss_po_box_no'];
            $bussData['buss_email'] = @$requestVar['buss_email'];
            $bussData['buss_telephone_no'] = @$requestVar['buss_telephone_no'];
            $bussData['buss_mobile_no'] = @$requestVar['buss_mobile_no'];
            $bussData['buss_fax_no'] = @$requestVar['buss_fax_no'];


            $bb = $this->userRepo->storeUserKycBussAddrData($bussData, $buss_addr_id);

            Session::flash('message', trans('success_messages.update_comercial_successfully'));
            if (@$request->save !== '' && @$request->save != null) {
                return redirect(route('commercial_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            } else if (@$request->save_next !== '' && @$request->save_next != null) {

                return redirect(route('financial_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Financial Information
     *
     * @return Response
     */
    public function editFinancialInformation(Request $request) {

        $corp_user_id = @$request->get('corp_user_id');
        $user_kyc_id = @$request->get('user_kyc_id');

        $userData = [];
        $benifinary = [];


        if ($corp_user_id > 0 && $user_kyc_id > 0) {

            $benifinary['user_kyc_id'] = (int) $user_kyc_id;
            $benifinary['corp_user_id'] = (int) $corp_user_id;
            $benifinary['is_by_company'] = 1;
            $userKycId = (int) $user_kyc_id;
            $userId = null;
        } else {
            $userId = (int) Auth::user()->user_id;
            $userKycId = (int) Auth::user()->user_kyc_id;
            $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
            $benifinary['corp_user_id'] = 0;
            $benifinary['is_by_company'] = 0;
        }
        $resData = $this->userRepo->getFinancialInfo($userKycId);
        if ($resData && $resData->count()) {
            $userData = $resData->toArray();
        }

        return view('frontend.financial_information', compact('userData', 'benifinary'));
    }

    /**
     * save Financial Information
     *
     * @return Response
     */
    public function saveFinancialInformation(FinancialFormRequest $request) {
        try {


            $requestVar = $request->all();
//dd($requestVar);
            $inputData = [];

            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = (int) @$requestVar['corp_user_id'];
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }
            $id = (@$requestVar['id'] != '' && @$requestVar['id'] != null) ? (int) $requestVar['id'] : null;
            $inputData['user_id'] = $userId;
            $inputData['user_kyc_id'] = $userKycId;
            $inputData['source_funds'] = @$requestVar['source_funds'];
            $inputData['jurisdiction_funds'] = @$requestVar['jurisdiction_funds'];
            $inputData['annual_income'] = @$requestVar['annual_income'];
            $inputData['estimated_wealth'] = @$requestVar['estimated_wealth'];
            $inputData['wealth_source'] = @$requestVar['wealth_source'];
            $inputData['tin_code'] = @$requestVar['tin_code'];
            $inputData['is_abandoned'] = @$requestVar['is_abandoned'];
            $inputData['date_of_abandonment'] = isset($requestVar['date_of_abandonment']) ? Helpers::getDateByFormat($requestVar['date_of_abandonment'], 'd/m/Y', 'Y-m-d') : null; ////@$requestVar['date_of_abandonment'];
            $inputData['abandonment_reason'] = @$requestVar['abandonment_reason'];
            $inputData['justification'] = @$requestVar['justification'];
            $inputData['tin_country_name'] = @$requestVar['tin_country_name'];
            $inputData['tin_number'] = @$requestVar['tin_number'];

            $inputData['created_by'] = $userId;
            $inputData['updated_by'] = $userId;
//dd($inputData);
            $this->userRepo->storeUserKycFinancialData($inputData, $id);
            Session::flash('message', trans('success_messages.update_financial_successfully'));

            if (@$request->save !== '' && @$request->save != null) {
                return redirect(route('financial_information', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            } else if (@$request->save_next !== '' && @$request->save_next != null) {

                return redirect(route('upload_document', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * edit Documents
     *
     * @return Response
     */
    public function editDocuments(Request $request) {
        //dd($request);
        try {

            $corp_user_id = @$request->get('corp_user_id');
            $user_kyc_id = @$request->get('user_kyc_id');

            $userData = [];
            $benifinary = [];
            $userData = [];
            $benifinary = [];


            if ($corp_user_id > 0 && $user_kyc_id > 0) {

                $benifinary['user_kyc_id'] = (int) $user_kyc_id;
                $benifinary['corp_user_id'] = (int) $corp_user_id;
                $benifinary['is_by_company'] = 1;
                $userKycId = (int) $user_kyc_id;
                $userId = $corp_user_id;
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
                $benifinary['corp_user_id'] = 0;
                $benifinary['is_by_company'] = 0;
            }
            
            
            $documentArray = $this->application->corporateDocument($userKycId); //corp doc required

            return view('frontend.upload_document', compact('userData', 'benifinary', 'documentArray'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * save Documents
     *
     * @return Response
     */
    public function saveDocuments(Request $request) {
        $userid = Auth()->user()->user_id;
        $res = '';


        try {
            $requestVar = $request->all();
            
            $resdata = [];
            if (@$requestVar['corp_user_id'] > 0 && @$requestVar['user_kyc_id'] > 0 && @$requestVar['is_by_company'] == 1) {
                $userId = (int) @$requestVar['corp_user_id'];
                $userKycId = (int) @$requestVar['user_kyc_id'];
                $corpUserId = (int) @$requestVar['corp_user_id'];
                $isBycompany = (int) @$requestVar['is_by_company'];
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $isBycompany = 0;
                $corpUserId = 0;
            }

            foreach ($request->file() as $keyArrayMain) {

                foreach ($keyArrayMain as $key => $doc) {
                    $keyArray = explode("#", $key);
                    $user_req_doc_id = $keyArray[0];
                    $user_id = $keyArray[1];
                    $doc_id = $keyArray[2];

                    foreach ($doc as $row) {

                        $docname = $row->getClientOriginalName();
                        $certificate = basename($row->getClientOriginalName());
                        $certificate = pathinfo($certificate, PATHINFO_FILENAME);
                        $ext = $row->getClientOriginalExtension();
                        $fileSize = $row->getClientSize();

                        if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                            $userBaseDir = 'appDocs/Document/indivisual/pdf/' . $user_id;
                            $userFileName = $docname;
                            $pathName = $row->getPathName();

                            $this->storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($pathName));
                            // Delete the temporary file
                            File::delete($pathName);
                        } else {
                            return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                        }


                        //store data in array
                        $array = [];
                        $array1 = [];
                        $array1['user_req_doc_id'] = $user_req_doc_id;
                        $array1['doc_type'] = 1;
                        $array1['user_kyc_id'] = $user_id;
                        $array1['user_id'] = Auth()->user()->user_id;
                        $array1['doc_id'] = $doc_id;

                        $array1['doc_name'] = $certificate;
                        $array1['doc_ext'] = $ext;
                        $array1['doc_status'] = 1;
                        $array1['enc_id'] = md5(rand(1, 9999));
                        $array1['created_by'] = Auth()->user()->user_id;
                        $array1['updated_by'] = Auth()->user()->user_id;

                        $array['updated_by'] = $user_id;
                        $array['is_upload'] = 1;
                        $result = Document::create($array1);
                    }
                    $res = UserReqDoc::where('user_req_doc_id', $user_req_doc_id)->update($array);
                }
            }
           
            if ($res) {

                if ($isBycompany == 1) {
                    Session::flash('message', trans('success_messages.update_documents_successfully'));

                    $resdata['status'] = 'success';
                    $resdata['message'] = 'success';
                    $resdata['redirect'] = route('shareholding_structure');
                } else {
                    Session::flash('message', trans('success_messages.update_documents_successfully'));

                    $resdata['status'] = 'success';
                    $resdata['message'] = 'success';
                    $resdata['redirect'] = route('upload_document', ['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]);
                }
            } else {
                $resdata['status'] = 'error';
                $resdata['message'] = 'success';
                $resdata['redirect'] = '';
            }
            return json_encode($resdata);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    //download documents

    public function IndivisualDocDownload(Request $request) {
        $documentHash = $request->get('enc_id');

        $docList = $this->application->getSingleDocument($documentHash);

        $userID = $docList->user_kyc_id;
        $fileName = $docList->doc_name . "." . $docList->doc_ext;
        $file = storage_path('app/appDocs/Document/indivisual/pdf/' . $userID . "/" . $fileName);
        return response()->download($file);
    }

    //===========================================================

    /**
     * Open popup of myaccount
     *
     * @return Response
     */
    public function myAccoutPopup() {
        return view('framePopup.myAccount');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function publicProfile(Request $request) {
        $userId = $request->get("user_id");

        $userData = $this->userRepo->find($userId);
        $educationData = $this->userRepo->getEductions($userId);
        $connectionDatas = $this->userRepo->getConnection($userId);
        $approveConnection = $this->userRepo->getApproveConnection($userId);
        $totalConnection = count($approveConnection);
        //dd($approveConnection);

        $skillData = $this->userRepo->getSkills($userId);
        $researchData = $this->userRepo->getResearches($userId);
        $awardsData = $this->userRepo->getAwards($userId);
        $ownedRights = [];
        $ownedRights = $this->application->getUserRights((int) $userId);

        return view('frontend.public_profile', compact('userData', 'educationData'
                        , 'skillData', 'researchData', 'awardsData', 'ownedRights', 'userId', 'connectionDatas', 'approveConnection', 'totalConnection'));
    }

    /**
     * update the notification to user
     * 
     * @return Response
     */
    public function updateNotifications(Request $request) {
        $id = $request->get('note_id');
        $deletedRes = $this->userRepo->updateNotifications($id);
        return redirect()->back();
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getAttachmentResearch($user_id = '', $file_id = '') {
        $rightDetailAttachments = $this->application->getAttachmentResearch($user_id, $file_id);
        return response()->download(storage_path('app/appDocs/publications/' . $rightDetailAttachments[0]->attachment), $rightDetailAttachments[0]->attachment_file_name);
    }

}
