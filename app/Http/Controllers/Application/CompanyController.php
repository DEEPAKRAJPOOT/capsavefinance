<?php

namespace App\Http\Controllers\Application;

use Auth;
use File;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalProfileFormRequest;
use App\Http\Requests\Company\ShareholderFormRequest;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use validate;
use App\Inv\Repositories\Models\CompanyAddress;
use App\Inv\Repositories\Models\Document;
use App\Inv\Repositories\Models\UserReqDoc;
use App\Inv\Repositories\Models\Userkyc;
use DB;
use Helpers;
class CompanyController extends Controller
{
    
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $application, StorageManagerInterface $storage) {
        $this->middleware('auth');
        $this->userRepo = $user;
        $this->application = $application;
        $this->storage = $storage;
    }

    public function index()
    {

        try {
                $userKycid=$this->application->getUserKycid(Auth()->user()->user_id);
                $data['userSignupdata']=$this->application->getRegisterDetails(Auth()->user()->user_id);
                //dd($userSignupdata->toArray());
                 $data['companyprofile']=$this->application->getCompanyProfileData($userKycid);

               
                 return view('frontend.company.company_details',$data);
        }  catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }	
    }

    public function companyDetailsForm(Request $request)
    {
        
    
        try {

                $userKycid=$this->application->getUserKycid(Auth()->user()->user_id);
                $res=$this->application->saveCompanyProfile($request,$userKycid);

                if($res){
                   return redirect()->route('company-address');
                } else {
                    return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                }
        }   catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }
    }
    
    public function companyAddress()
    {     
        try {
             $userKycid=$this->application->getUserKycid(Auth()->user()->user_id);
             $data['address']=$this->application->getCompanyAddress($userKycid);

             return view('frontend.company.address_details',$data);


        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    public function companyAddressForm(Request $request)
    {

        try {
                $this->companyAddressValidate($request);
                $userKycid=$this->application->getUserKycid(Auth()->user()->user_id);
                $res=$this->application->saveCompanyAddress($request,$userKycid);

                if($res){
                   return redirect()->route('shareholding_structure');
                } else {
                    return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                }
        }   catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }
           
    }

    public function shareholdingStructure()
    {

        $nextShare=[]; 

        $userId = Auth()->user()->user_id;
        $result=$this->application->getHigestLevelShareData((int)$userId,'2');
        $beficiyerData  =   [];
        $beficiyerData  =   $this->application->getBeneficiaryOwnersData((int)Auth()->user()->user_id);

        if($result && $result->count()){
            $nextShare=$result->toArray();
            return view('frontend.company.shareholding_stracture',compact('nextShare'));
        }else if($beficiyerData && $beficiyerData->count()){
             return view('frontend.company.shareholding_beneficinary',compact('beficiyerData'));
        }else{
           return view('frontend.company.shareholding_stracture',compact('nextShare')); 
        }


    }

    public function shareHoldingStructureForm(ShareholderFormRequest $request) {
        try {

            $requestVar = $request->all();
            //dd($requestVar);
            $userId = Auth()->user()->user_id;
            $shareParentIds = @$requestVar['share_parent_id'];
            $shareLevels = @$requestVar['share_level'];
            
            $next = 0;
            foreach ($shareParentIds as $pkey => $parent_id) {//dd($parent_id);
                $shareLevel=$shareLevels[$pkey];
                $shareTypes = @$requestVar['share_type_'. $parent_id];
               //dd($shareLevel);
                $companyNames = @$requestVar['company_name_'. $parent_id];
                $passportNos = @$requestVar['passport_no_'. $parent_id];
                $sharePercentages = @$requestVar['share_percentage_'. $parent_id];
                $shareValues = @$requestVar['share_value_'. $parent_id];

                foreach ($shareTypes as $key => $type) {

                    $shareData['user_id'] = $userId;
                    $shareData['share_type'] = $type;
                    $shareData['company_name'] = $companyNames[$key];
                    $shareData['passport_no'] = $passportNos[$key];
                    $shareData['share_percentage'] = $sharePercentages[$key];
                    $shareData["share_value"] = $shareValues[$key];
                    $shareData["share_parent_id"] = $parent_id;
                    $shareData["share_level"] = (int)$shareLevel;
                   // echo '<pre>';
                   //print_r($shareData);
                   //echo '</pre>';
                   // $response = $this->application->saveShareHoldingForm($shareData, null);
                   //echo '<pre>';
                   // print_r($response);
                  //echo '</pre>';
                    if($type == '2') {
                       $next++;
                   }
                }
            }


           
        

            if ($next > 0) {
                //return view('frontend.company.shareholding_stracture',compact('nextShare',''));
                Session::flash('message', trans('success_messages.UpdateShareHolderSuccessfully'));
                return redirect()->route('shareholding_structure');
            } else {
                Session::flash('message', trans('success_messages.UpdateShareHolderSuccessfully'));
                return redirect()->route('financial-show');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

   

    public function financialInfo()
    {
        try{
                $userKycid=$this->application->getUserKycid(Auth()->user()->user_id);
               $data['financial']=$this->application->getCompanyFinancialData($userKycid);
               
                return view('frontend.company.financial',$data);

        } catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }
    	
    }



    public function financialInfoForm(Request $request) {

        try {
            $res = $this->application->saveFinancialInfoForm($request, Auth()->user()->user_id);

            if ($res) {
                return redirect()->route('documents-show');
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    public function documentDeclaration()
    {


        try {
          
         
          
        /*  echo "<pre>";
          print_r($data);die;*/
          $data['kycid']        =   $this->application->getUserKycid(Auth()->user()->user_id);
        
          $data['documentArray']=   $this->application->corporateDocument($data['kycid']); //corp doc required

    	    return view('frontend.company.documents',$data);
        } catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }
    }




    public function documentDeclarationForm(Request $request)
    {
        $userid=Auth()->user()->user_id;
            
            //user_id = a user_kyc_id h
        try {
             
foreach($request->file() as $keyArrayMain)
{
/*            echo "<pre>";
            print_r($keyArrayMain);
            exit; */

    foreach($keyArrayMain as $key=>$doc)
    {
         $keyArray = explode("#",$key);

         $user_req_doc_id = $keyArray[0];
         $user_id = $keyArray[1];
         $doc_id = $keyArray[2];

        foreach($doc as $row){

        $docname = $row->getClientOriginalName();
        $certificate = basename($row->getClientOriginalName());
                        
        $certificate = pathinfo($certificate, PATHINFO_FILENAME); 
        $ext = $row->getClientOriginalExtension();
                     
                    


   /////////////////////////
   $fileSize  = $row->getClientSize();
   
   if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                     $userBaseDir       = 'appDocs/Document/indivisual/pdf/'.$user_id;
                     $userFileName      = $docname;
                     $pathName = $row->getPathName();
                     //echo $pathName; exit;
                    
$this->storage->engine()->put($userBaseDir.DIRECTORY_SEPARATOR.$userFileName,File::get($pathName));
                     // Delete the temporary file
                     File::delete($pathName);
                 } else {
                     return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                 }


                 //store data in array
                     $array=[];
                     $array1=[];
                     $array1['user_req_doc_id'] = $user_req_doc_id; 
                     $array1['doc_type'] = 2; 
                     $array1['user_kyc_id'] = $user_id;  
                     $array1['user_id'] = Auth()->user()->user_id;  
                     $array1['doc_id'] = $doc_id; 

                     $array1['doc_name'] = $certificate;
                     $array1['doc_ext'] = $ext;
                     $array1['doc_status']= 1;
                     $array1['enc_id'] = md5(rand(1,9999));
                     $array1['created_by']=Auth()->user()->user_id;
                     $array1['updated_by']=Auth()->user()->user_id;
                    
                    $array['updated_by']=$user_id;
                    $array['is_upload']=1;
                  $result= Document::create($array1);
                    }
               $res=UserReqDoc::where('user_req_doc_id', $user_req_doc_id)->update($array);

                }

            
             if($res){
                      echo "success";
                   
                }  else {
                    echo "some error occur";
                }

}



            }   catch (Exception $ex) {
                 return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
                }
}

    public function docDownload(Request $request){
      $documentHash = $request->get('enc_id');
      $docList = $this->application->getSingleDocument($documentHash);
      $userID = $docList->user_kyc_id;
      $fileName = $docList->doc_name.".".$docList->doc_ext;
      $file=storage_path('app/appDocs/Document/corporate/pdf/'.$userID."/".$fileName);
        return response()->download($file);

    }




    /*--------Form validate----- */

    public function companyProfile($request)
    {
         $this->validate(
                $request, 
                [   
                    'customername'=> 'required|min:2|max:50|regex:/^[a-zA-Z]/u|',
                    'regisno'=>'required|min:5|max:20|regex:/^[a-zA-Z0-9]/u|',
                    'regisdate'=>'required',
                    'status'=>'required',
                    'naturebusiness'=>'required',
                ],
                [   
                    'customername.required'=> 'This field is required.',
                    'customername.min'    => 'Please enter at least 2 characters',
                    'customername.max'    => 'Please enter no more than 50 characters',
                    'customername.regex'  => 'Please enter only characters',

                    'regisno.required'  => 'This field is required.',  
                    'regisno.min'    => 'Please enter at least 5 characters',
                    'regisno.max'    => 'Please enter no more than 20 characters', 
                    'regisno.regex' =>'Please enter only alphabetical characters',
                    'regisdate.required' => 'This field is required.',   
                    'status.required'=> 'This field is required.',  
                    'naturebusiness.required'=> 'This field is required.',
                ]
             );
    }

    public function companyAddressValidate($request)
    {

        $this->validate(
                $request, 
                [   
                    'country'=> 'required',
                    'city'=>'required',
                    'region'=>'required|max:30|regex:/^[a-z A-Z]/u|',
                    'building'=>'required|max:50|regex:/^[a-zA-Z0-9]/u|',
                    'street'=>'required|max:20|regex:/^[a-zA-Z0-9]/u|',
                    'postalcode'=>'required|max:10|regex:/^[a-zA-Z0-9]/u|',
                    'pobox'=>'required',
                    'email'=>'required|email',
                    'telephone'=>'required|max:10|regex:/^[0-9]/u|',
                    'mobile'=>'required|max:10|regex:/^[0-9]/u|',
                    'faxno'=>'required',
                    //Address for Correspondence
                    'corr_country'=> 'required',
                    'corr_city'=>'required',
                    'corr_region'=>'required|max:30|regex:/^[a-z A-Z]/u|',
                    'corr_building'=>'required|max:50|regex:/^[a-zA-Z0-9]/u|',
                    'corr_street'=>'required|max:20|regex:/^[a-zA-Z0-9]/u|',
                    'corr_postal'=>'required|max:10|regex:/^[a-zA-Z0-9]/u|',
                    'corr_pobox'=>'required',
                    'corr_email'=>'required|email',
                    'corr_tele'=>'required|max:10|regex:/^[0-9]/u|',
                    'corr_mobile'=>'required|max:10|regex:/^[0-9]/u|',
                    'corr_fax'=>'required',

                ],
                [   
                    'country.required'=> 'This field is required',
                    'city.required'   => 'This field is required',

                    'region.required' => 'This field is required',
                    'region.regex' =>'Please enter only alphabetical characters',
                    'region.max' =>'Please enter no more than 30 characters',

                    'building.required'  => 'This field is required',
                    'building.regex'  => 'Please enter only alphabetical characters',
                    'building.max' =>'Please enter no more than 50 characters',

                    'street.required'  => 'This field is required', 
                    'street.regex'  => 'Please enter only alphabetical characters',
                    'street.max' =>'Please enter no more than 20 characters',

                    'postalcode.required' =>'This field is required',
                    'postalcode.max' =>'Please enter no more than 10 characters',
                    'postalcode.regex' =>'Please enter only alphabetical characters',

                   // 'pobox.required'    => 'This field is required', 
                    'email.required' =>'This field is required',
                    'email.email'    =>'Please enter a valid email address',
                    

                    'telephone.required' => 'This field is required',  
                    'telephone.max'=>'Please enter no more than 10 number',
                    'telephone.regex'=>'Please enter valid telephone no',

                    'mobile.required'=> 'This field is required',  
                    'mobile.max'=>'Please enter no more than 10 number',
                    'mobile.regex'=>'Please enter valid mobile no',
                    'faxno.required'=> 'This field is required',

                    //Address for Correspondence
                    'corr_country.required'=> 'This field is required',
                    'corr_city.required'   => 'This field is required',

                    'corr_region.required' => 'This field is required',
                    'corr_region.regex' =>'Please enter only alphabetical characters',
                    'corr_region.max' =>'Please enter no more than 30 characters',

                    'corr_building.required'  => 'This field is required',
                    'corr_building.regex'  => 'Please enter only alphabetical characters',
                    'corr_building.max' =>'Please enter no more than 50 characters',

                    'corr_street.required'  => 'This field is required', 
                    'corr_street.regex'  => 'Please enter only alphabetical characters',
                    'corr_street.max' =>'Please enter no more than 20 characters',

                    'corr_postal.required' =>'This field is required',
                    'corr_postal.max' =>'Please enter no more than 10 characters',
                    'corr_postal.regex' =>'Please enter only alphabetical characters',

                    'corr_pobox.required'    => 'This field is required', 
                    'corr_email.required' =>'This field is required',
                    'corr_email.email'    =>'Please enter a valid email address',
                    

                    'corr_tele.required' => 'This field is required',  
                    'corr_tele.max'=>'Please enter no more than 10 number',
                    'corr_tele.regex'=>'Please enter valid telephone no',

                    'corr_mobile.required'=> 'This field is required',  
                    'corr_mobile.max'=>'Please enter no more than 10 number',
                    'corr_mobile.regex'=>'Please enter valid mobile no',
                    'corr_fax.required'=> 'This field is required',
                ]
            );
    }
    public function financialFormValidate($request)
    {
        $this->validate(
                $request, 
                [   
                    'yearly_usd'=> 'required|max:30|regex:/^[0-9]/u|',
                    'yearly_profit_usd'=>'required|max:30|regex:/^[0-9]/u|',
                    'total_debts_usd'=>'required|max:30|regex:/^[0-9]/u|',
                    'total_recei_usd'=>'required|max:30|regex:/^[0-9]/u|',
                    'total_cash_usd'=>'required|max:30|regex:/^[0-9]/u|',
                ],
                [   
                    'yearly_usd.required'=> 'This field is required',
                    'yearly_usd.regex'=>'Please enter only number $ float',
                    'yearly_usd.max'=>'Please enter no more than 30 numbers',

                    'yearly_profit_usd.required' => 'This field is required',
                    'yearly_profit_usd.regex'=>'Please enter only number $ float',
                    'yearly_profit_usd.max'=>'Please enter no more than 30 numbers',

                    'total_debts_usd.required'   => 'This field is required',
                    'total_debts_usd.regex'=>'Please enter only number $ float',
                    'total_debts_usd.max'=>'Please enter no more than 30 numbers',

                    'total_recei_usd.required'  =>'This field is required',
                    'total_recei_usd.regex'=>'Please enter only number $ float',
                    'total_recei_usd.max'=>'Please enter no more than 30 numbers',

                    'total_cash_usd.required' =>'This field is required',
                    'total_cash_usd.regex'=>'Please enter only number $ float',
                    'total_cash_usd.max'=>'Please enter no more than 30 numbers',


                ]
             );
    }
    public function validateDocuments($request)
    {
                $this->validate(
                $request, 
                [   
                    'certificate_Incor'=> 'required|mimes:pdf|max:10000',
                    'article_assoc'=>'mimes:pdf|max:10000',
                    'licence'=>'mimes:pdf|max:10000',
                    'director_passport'=>'mimes:pdf|max:10000',
                    'police_certificate'=>'mimes:pdf|max:10000',
                    'bankreference'=>'mimes:pdf|max:10000',
                    'lawfirm'=>'mimes:pdf|max:10000',
                    'auditorreference'=>'mimes:pdf|max:10000',
                    'auditor_financial'=>'mimes:pdf|max:10000',

                ],
                [   
                    'certificate_Incor.required'=> 'This field is required',
                    'certificate_Incor.mimes' =>'Please upload only pdf file',
                    'article_assoc.mimes' =>'Please upload only pdf file',
                    'licence.mimes' =>'Please upload only pdf file',
                    'director_passport.mimes' =>'Please upload only pdf file',
                    'police_certificate.mimes' =>'Please upload only pdf file',
                    'bankreference.mimes' =>'Please upload only pdf file',
                    'lawfirm.mimes' =>'Please upload only pdf file',
                    'auditorreference.mimes' =>'Please upload only pdf file',
                    'auditor_financial.mimes' =>'Please upload only pdf file',

                ]
             );
    }
                    
}
