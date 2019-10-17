<?php

namespace App\Inv\Repositories\Entities\Application;

use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Research;
use App\Inv\Repositories\Models\Userdetail;
use App\Inv\Repositories\Models\CompanyProfile;
use App\Inv\Repositories\Models\CompanyAddress;
use App\Inv\Repositories\Models\ShareHolding;
use App\Inv\Repositories\Models\FinancialInformation;
use App\Inv\Repositories\Models\Document;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\UserReqDoc;
use App\Inv\Repositories\Models\Userkyc;
use App\Inv\Repositories\Models\Corpdetail;


use DB;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use Session;




/**
 * Application repository class for right
 */
class ApplicationRepository extends BaseRepositories implements ApplicationInterface {

    use CommonRepositoryTraits;

    /**
     * Class constructor
     *
     * @return void
     */
    protected $CompanyProfile;
    protected $CompanyAddress;
    public function __construct(CompanyProfile $CompanyProfile,CompanyAddress $CompanyAddress,Document $document) {
        $this->Companyprofile=$CompanyProfile;
        $this->Companyaddress=$CompanyAddress;
        $this->document=$document;

    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes) {
        return Rights::saveRights($attributes);
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $rightId) {
        return Rights::updateRights((int) $rightId, $attributes);
    }

    /**
     * Get all records method
     *
     * @param array $columns
     */
    public function all($columns = array('*')) {
        return Rights::all($columns);
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns     
     */
    public function find($id, $columns = array('*')) {
        $varRightData = Rights::find((int) $id, $columns);

        return $varRightData;
    }


    /**
     * Update User Membership
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function updateMembership($user_d, $attributes) {
        return Rights::updateMembership($user_d, $attributes);
    }

    
    

    

    /**
     * Get all rights
     *     
     * @return object|boolean
     */

public function getAllRight($paginationArr, $keyword = null, $sortBy = [], $type = [], $group = [], $min_price, $max_price,$login_user_id='') {

        return Rights::getAllRight($paginationArr, $keyword, $sortBy, $type, $group, $min_price, $max_price,$login_user_id);
    }


    
    

   

   

    /**
     * get trending rights
     *
     * @return object|boolean
     */
    public function getTrendingRights($limit = null,$login_user_id='') {
        return Rights::getTrendingRights($limit,$login_user_id);
    }

    /**
     * get recommended rights
     *
     * @return object|boolean
     */
    public function getRecommendedRightsUser($limit = null,$login_user_id='') {
        return Rights::getRecommendedRightsUser($limit);
    }

    /**
     * get recommended rights
     *
     * @return object|boolean
     */
    public function getRecommendedRights($limit = null,$login_user_id='') {
        return Rights::getRecommendedRights($limit,$login_user_id);
    }

    
     /*-------------company profile function------------*/

    public function getCompanyProfileData($userId)
    {

     return $this->Companyprofile->personalCompanyProfile($userId) ;
    }
    public function saveCompanyProfile($request, $userKycid)
    {

       
        $res=CompanyProfile::where('user_kyc_id', $userKycid)->first();
      
        $attributes=[
                    'user_kyc_id'=>$userKycid,
                    'user_id'=>Auth()->user()->user_id,
                    'customer_name'=>$request->customername,
                    'registration_no'=>$request->regisno,
                    'registration_date'=>$request->regisdate,
                    'status'=>$request->status,

                    'business_nature'=>$request->naturebusiness,
               ];
        if($res){
           
            $result = CompanyProfile::where('user_kyc_id', $userKycid)->update($attributes);

            return $result ?: false;
        } else {
            return CompanyProfile::create($attributes);
        }

    }

    
    public function getCompanyAddress($userId)
    {
       return $this->Companyaddress->getCorpAddress($userId);
    }

    public function saveCompanyAddress($request, $userId)
    {
    
        return $this->Companyaddress->createCompanyAddress($request,$userId) ;

    }

    
    public function saveShareHoldingForm($attributes,$id)
    {

        return ShareHolding::storeData($attributes,$id);

    }
    
    /*
     * get Last level share with share type comapny
     *       
    */
    
    public function getHigestLevelShareData($user_id,$share_type){
        return ShareHolding::getHigestLevelShareData($user_id,$share_type);
    }
    
    /*
     * get Beneficiary Owners
     * 
    */
    
    public function getBeneficiaryOwnersData($user_id){
        return ShareHolding::getBeneficiaryOwnersData($user_id);
    }
    
    //getShareHolderData
    
    /*
     * get Beneficiary Owners
     * 
    */
    
    public function getShareHolderData($user_id){
        return ShareHolding::getShareHolderData($user_id);
    }
    
    public function getShareHolderInfo($id){
        return ShareHolding::getData($id);
    }

    public function getCompanyFinancialData($userId)
    {
        return FinancialInformation::getFinancialData($userId);
    }
    public function saveFinancialInfoForm($request, $userKycid)
    {
        $attributes=[
            'user_kyc_id'=>$userKycid,
            'user_id'=>Auth()->user()->user_id,
            'yearly_usd'=>$request->yearly_usd,
            'yearly_profit_usd'=>$request->yearly_profit_usd,
            'total_debts_usd'=>$request->total_debts_usd,
            'total_receivables_usd'=>$request->total_recei_usd,
            'total_cash'=>$request->total_cash_usd,
         ];

         $res=FinancialInformation::where('user_kyc_id', $userKycid)->first();

        

        if($res){
           
            $result = FinancialInformation::where('user_kyc_id', $userKycid)->update($attributes);

            return $result ?: false;
        } else {
            return FinancialInformation::create($attributes);
        }
    
       

    }

   

    public function corporateDocument($userKycId)
    {
         //$userkyc=Userkyc::where('user_id',Auth()->user()->user_id)->first();

         $corpdata=UserReqDoc::where('user_kyc_id', $userKycId)->get()->toArray();
        if(!empty($corpdata)){
             
             return   $corpdata;
        }

    }

    public function getcorpDocument($userId)
    {
            return $data=DB::table('user_req_doc')
                ->where('user_id',$userId)
                ->where('is_upload',1)
                ->get();
    }


    public function getDocumentList($userId,$user_req_doc_id)
    {

           
        return $this->document->getDocumentList($userId,$user_req_doc_id);

    }


    public function getSingleDocument($documentHash)
    {

           
        return $this->document->getSingleDocument($documentHash);

    }

    public function getUserKycid($userId){
        $userkyc=Userkyc::where('user_id',$userId)->first();
      // Session::set(['userkycid'=> $userkyc->kyc_id]);
       //$userkycid=Session::get('userkycid');
       return $userkyc->kyc_id? :false;

    }
    public function getRegisterDetails($userId)
    {

        $result=Corpdetail::where('user_id', (int)$userId)->first();

        return $result ? :false;
    }



    

}
