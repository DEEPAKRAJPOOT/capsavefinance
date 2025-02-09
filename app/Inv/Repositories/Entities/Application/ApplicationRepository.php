<?php

namespace App\Inv\Repositories\Entities\Application;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Agency;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Models\AppNote;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\UserNach;
use App\Inv\Repositories\Models\AppPdNote;
use App\Inv\Repositories\Models\BizGstLog;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\FiAddress;
use App\Inv\Repositories\Models\OfferPTPQ;
use App\Inv\Repositories\Models\AppProduct;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\BizPerfios;
use App\Inv\Repositories\Models\CamHygiene;
use App\Inv\Repositories\Models\ProgramDoc;
use App\Inv\Repositories\Models\UserAppDoc;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\WfAppStage;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Master\Bank;
use App\Inv\Repositories\Models\OfferCharge;
use App\Inv\Repositories\Models\RcuDocument;
use App\Inv\Repositories\Models\AppStatusLog;
use App\Inv\Repositories\Models\BizEntityCin;
use App\Inv\Repositories\Models\Master\Asset;
use App\Inv\Repositories\Models\RcuStatusLog;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Models\AppDocProduct;
use App\Inv\Repositories\Models\ColenderShare;
use App\Inv\Repositories\Models\LiftingDetail;
use App\Inv\Repositories\Models\Lms\NachBatch;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\NachStatusLog;
use App\Inv\Repositories\Models\AnchorRelation;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Inv\Repositories\Models\AppLimitReview;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\Master\Charges;
use App\Inv\Repositories\Models\Master\Company;
use App\Inv\Repositories\Models\Master\Segment;
use App\Inv\Repositories\Models\ProgramCharges;
use App\Inv\Repositories\Models\UserCkycReport;
use App\Inv\Repositories\Models\AppBizFinDetail;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\Master\DoaLevel;
use App\Inv\Repositories\Models\Master\Industry;
use App\Inv\Repositories\Models\UserBankAccount;
use App\Inv\Repositories\Models\UserCkycConsent;
use App\Inv\Repositories\Models\AppBizBankDetail;
use App\Inv\Repositories\Models\AppBorrowerLimit;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\Documents;
use App\Inv\Repositories\Models\Master\Equipment;
use App\Inv\Repositories\Models\AppSanctionLetter;
use App\Inv\Repositories\Models\AppOfferAdhocLimit;
use App\Inv\Repositories\Models\CamReviewerSummary;
use App\Inv\Repositories\Models\Master\SubIndustry;
use App\Inv\Repositories\Models\Master\Constitution;
use App\Inv\Repositories\Models\CamReviewSummPrePost;
use App\Inv\Repositories\Models\OfferEscrowMechanism;
use App\Inv\Repositories\Models\OfferPrimarySecurity;
use App\Inv\Repositories\Models\CamReviewSummRiskCmnt;
use App\Inv\Repositories\Models\OfferPersonalGuarantee;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Models\AppProgramOfferSanction;
use App\Inv\Repositories\Models\OfferCollateralSecurity;
use App\Inv\Repositories\Models\OfferCorporateGuarantee;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

/**
 * Application repository class
 */
class ApplicationRepository extends BaseRepositories implements ApplicationInterface {

	use CommonRepositoryTraits;

	/**
	 * Class constructor
	 *
	 * @return void
	 */    
	public function __construct() {
	}

	/**
	 * Create method
	 *
	 * @param array $attributes
	 */
	protected function create(array $attributes) {        
	}

	/**
	 * Update method
	 *
	 * @param array $attributes
	 */
	protected function update(array $attributes, $id) {        
	}

	/**
	 * Get all records method
	 *
	 * @param array $columns
	 */
	public function all($columns = array('*')) {        
	}

	/**
	 * Find method
	 *
	 * @param mixed $id
	 * @param array $columns     
	 */
	public function find($id, $columns = array('*')) {        
	}

	/**
	*create business information details
	* @param mixed $userId
	* @param array $attributes     
	*/

	public function saveBusinessInfo($attributes = [], $userId = null){
		/**
		 * Check Data is Array
		 */
		if (!is_array($attributes)) {
			throw new InvalidDataTypeExceptions('Please send an array');
		}

		/**
		 * Check Data is not blank
		 */
		if (empty($attributes)) {
			throw new BlankDataExceptions('No Data Found');
		}

		if(is_null($userId)){
			throw new BlankDataExceptions('No Data Found');
        }
        //dd($attributes);
		return Business::creates($attributes, $userId);
	}

	/**
	*update company details
	* @param mixed $bizId
	* @param array $attributes     
	*/

	public function updateCompanyDetail($attributes = [], $bizId = null, $userId){
		/**
		 * Check Data is Array
		 */
		if (!is_array($attributes)) {
			throw new InvalidDataTypeExceptions('Please send an array');
		}

		/**
		 * Check Data is not blank
		 */
		if (empty($attributes)) {
			throw new BlankDataExceptions('No Data Found');
		}

		if(is_null($bizId)){
			throw new BlankDataExceptions('No Data Found');
		}
		return Business::updateCompanyDetail($attributes, $bizId, $userId);
	}

	/**
	 * Get Applications for Application list data tables
	 */
	public function getApplications() 
	{
		return Application::getApplications();
	}

    /**
	 * Get Applications for Application list data tables
	 */
	public function getAssignedApplications($request) 
	{
		return Application::getAssignedApplications($request['role_id'],$request['user_id']);
	}

	/**
	 * Get business information according to app id
	 */
	public function getAppDataByAppId($appId = null){
		if(is_null($appId)){
			throw new BlankDataExceptions('No Data Found');
		}
		return Application::find($appId);
	}

	/**
	 * Get Entity by biz id
	 */
	public function getEntityByBizId($bizId) 
	{
		return Business::getEntityByBizId($bizId);
	}

    /**
     * Get Entity by biz id
     */
    public function getCamDataByBizAppId($bizId, $appId) 
    {
        return Cam::getCamDataByBizAppId($bizId, $appId);
    }

    /**
     * Get Application by app id
     */
    public function getApplicationById($bizId) 
    {
        return Business::getApplicationById($bizId);
    }
	
	/**
	 * Update Application Status
	 * 
	 * @param integer $appId
	 * @param array $attributes
	 * @return boolean
	 * @throws InvalidDataTypeExceptions
	 * @throws BlankDataExceptions
	 */
	public function updateAppStatus($appId, $attributes = []){
		/**
		 * Check Data is Array
		 */
		if (!is_array($attributes)) {
			throw new InvalidDataTypeExceptions('Please send an array');
		}

		/**
		 * Check Data is not blank
		 */
		if (empty($attributes)) {
			throw new BlankDataExceptions('No Data Found');
		}

		if(empty($appId)){
			throw new BlankDataExceptions('No Data Found');
		}
		return true;
	}

	/**
	 * Update Application Assignee
	 * 
	 * @param type $appId
	 * @param type $attributes
	 * @return boolean
	 * @throws InvalidDataTypeExceptions
	 * @throws BlankDataExceptions
	 */
	public function updateAssignee($appId, $attributes = []){
		/**
		 * Check Data is Array
		 */
		if (!is_array($attributes)) {
			throw new InvalidDataTypeExceptions('Please send an array');
		}

		/**
		 * Check Data is not blank
		 */
		if (empty($attributes)) {
			throw new BlankDataExceptions('No Data Found');
		}

		if(empty($appId)){
			throw new BlankDataExceptions('No Data Found');
		}
		return true;        
	}
	
	/**
	 * Get Applications for Application list data tables
	 */
	public function getApplicationsDetail($user_id) 
	{
		return Application::getApplicationsDetail($user_id);
	}
	
	/**
	 * Get Applications for Application list data tables
	 */
	public function getApplicationPoolData() 
	{
		return Application::getApplicationPoolData();
	}    
		
	/**
	 * Save application note
	 * 
	 * @param array $noteData
	 * @return mixed
	 */
	public function saveAppNote($noteData) 
	{
		return AppNote::create($noteData);
	}

	
	/**
	 * Get Applications for Application list data tables
	 */
	public function saveShaircase($attributes) 
	{
		return AppAssignment::saveData($attributes);
	}
	
	 /**
	 * update Applications for Application list data tables
	 */
	public function updateAppDetails($app_id, $arrUserData = []) 
	{
		return Application::updateAppDetails((int)$app_id, $arrUserData);
	}
 
	/**
	 * Get Applications for Application list data tables
	 */
	public function getCustomerApplications($user_id) 
	{
            
		return Application::with('business')
				  ->with('prgmLimit')
                                  ->where(['user_id' => $user_id, 'status' => 2])
                                  ->get();
	}    

	/**
	 * Get Applications for Application list data tables
	 */
	public function getCustomerAnchors($user_id) 
	{
		return Application::with('business')
				->with('appLimit')
				->where(['user_id' => $user_id, 'status' => 2])
				->get();
	} 
    
    /**
     * update Applications for Application list data tables
     */
    public function updateAppAssignById($app_id, $arrUserData = []) 
    {
        return AppAssignment::updateAppAssignById((int)$app_id, $arrUserData);
    }
    
     /**
     * update Applications for Application list data tables
     */
    public function updateAssignedAppById($appData, $arrUserData = []) 
    {
        return AppAssignment::updateAssignedAppById($appData, $arrUserData);
    }

    /**
     * Get Application Data By Biz Id
     * 
     * @param integer $biz_id
     * @return mixed
     */
    public function getAppDataByBizId($biz_id)
    {
       return Application::getAppDataByBizId((int)$biz_id); 
    }
    
    /**
     * Update Application Data By application Id
     * 
     * @param integer $app_id
     * @param array $arrData
     *
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function updateAppData($app_id, $arrData)
    {
       return Application::updateAppData((int)$app_id, $arrData);  
    }

    /**
     * Assign application
     * 
     * @param type $dataArr
     */
    public function assignApp($dataArr)
    {
        return AppAssignment::saveData($dataArr);
    }

    /**
     * Get User Applications for Application list data tables for frontend
     */
    public function getUserApplications() 
    {
        return Application::getUserApplications();
    }

    /**
     * Get Agency Applications for Application list data tables for frontend
     */
    public function getAgencyApplications() 
    {
        return Application::getAgencyApplications();
    }

    /**
     * function for get all FI lists
     * @return type
     */
     
    public function getFiLists($dataArr)
    {
      $result = BusinessAddress::getFiLists($dataArr);
      return $result ?: false;
    }

     /**
     * function for get all RCU documents list
     * @return type
     */
     
    public function getRcuLists($appId)
    {
      $result = AppDocumentFile::getRcuLists($appId);
      return $result ?: false;
    }

    public function getRcuActiveLists($appId)
    {
      $result = AppDocumentFile::getRcuActiveLists($appId);
      return $result ?: false;
    }
    
     /**
     * function for get all RCU documents filess list
     * @return type
     */
     
    public function getRcuDocuments($appId, $docId)
    {
      $result = AppDocumentFile::getRcuDocuments($appId, $docId);
      return $result ?: false;
    }
    
     /**
     * function for get all RCU documents filess list
     * @return type
     */
     
    public function getCurrentRcuDoc($appId, $docId)
    {
      return RcuDocument::where('app_id', $appId)
                ->with('cmStatus')
                ->where('doc_id', $docId)
                ->where('is_active', 1)
                ->first();
      
    }
    
     /**
     * function for get all RCU documents filess list
     * @return type
     */
     
    public function getRcuAgencies($appId, $docId)
    {
      $result = RcuDocument::getRcuAgencies($appId, $docId);
      return $result ?: false;
    }

    public function getRcuActiveAgencies($appId, $docId)
    {
      $result = RcuDocument::getRcuActiveAgencies($appId, $docId);
      return $result ?: false;
    }
    
    /**
     * Get Program Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getProgramData($whereCondition=[])
    {
        $prgmData = Program::getProgramData($whereCondition);
        return $prgmData ? $prgmData : [];
    }
         
    /**
     * Get Anchor Data By Application Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function getAnchorDataByAppId($app_id)
    {
        $prgmData = Application::getAnchorDataByAppId($app_id);
        return $prgmData ? $prgmData : [];
    }

    /**
     * get address for FI
     * 
     * @param integer $biz_id
     * @return all address result
     */
    public function getAddressforFI($biz_id){
        $result = BusinessAddress::getAddressforFI($biz_id);
        return $result ?: false;
    }

    /**
     * get address for Agency FI
     * 
     * @param integer $biz_id
     * @return all address result
     */
    public function getAddressforAgencyFI($biz_id){
        $result = BusinessAddress::getAddressforAgencyFI($biz_id);
        return $result ?: false;
    }

    /**
     * Create app borrower limit
     * 
     * @param integer $applimitData
     * @return all created result
     */

     public function createBorrowerLimit($applimitData){

        $result =  AppBorrowerLimit::creates($applimitData);
        return $result ?: false;
     }

     public function getAppBorrowerLimit($appId){

        $result =  AppBorrowerLimit::getAppBorrowerLimit($appId);
        return $result ?: false;
     }

    /**
     * get address for FI
     * 
     * @param integer $biz_id
     * @return all address result
     */
    

    public function creates($attributes){
        $result =  LiftingDetail::creates(($attributes));
        return $result ?: false;
    }

    public function getLiftingDetail($appId){
        $result =  LiftingDetail::where('app_id',$appId)->get();
        return !$result->isEmpty() ? $result : false;
    }

     public function updateLiftingDetail($attributes, $anchor_lift_detail_id){
        $anchor =  LiftingDetail::where('anchor_lift_detail_id',$anchor_lift_detail_id)->first();
        $updateAnchorData = $anchor->update($attributes);
        return $updateAnchorData ? true : false;
    }

    /**
     * insert into FI address
     * 
     * @param array $data
     * @return status
     */
    public function insertFIAddress($data){
        $result = FiAddress::insertFiAddress($data);
        return $result ?: false;
    }
    
    /**
     * insert into RCU documents
     * 
     * @param array $data
     * @return status
     */
    public function assignRcuDocument($data){
        /**
         * Check Data is Array
         */
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($data)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        $assignData = RcuDocument::where('agency_id', $data['agency_id'])
                ->where('to_id', $data['to_id'])
                ->where('app_id', $data['app_id'])
                ->where('doc_id', $data['doc_id'])
                ->first();
        if(!$assignData) {
            $resp = RcuDocument::where('app_id', $data['app_id'])
                    ->where('doc_id', $data['doc_id'])
                    ->update(['is_active' => 0]);
            if($resp == true || !$assignData) {
                $result = RcuDocument::create($data);
                return $result ?: false;
            }
        }
        else {
            return "Assigned";
        }
        
    }
    
    /**
     * insert into RCU documents
     * 
     * @param array $data
     * @return status
     */
    public function saveRcuStatusLog($data){
        /**
         * Check Data is Array
         */
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($data)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        $result = RcuStatusLog::insert($data);
        
        return $result ?: false;
    }

    /**
     * get all agency list
     * @return agency
     */
    public function getAllAgency($type=null){
        $agency = Agency::getAllAgency($type);
        return $agency ?: false;
    }

    /**
     * Get Application assign data
     * 
     * @param array $whereCondition
     * @return mixed
     */
    public function getAppAssignmentData ($whereCondition=[])
    {
        return AppAssignment::getAppAssignmentData ($whereCondition);
    }

    /**
     * Get Back stages users to assign the application
     * 
     * @param integer $app_id
     * @param array $roles
     * 
     * @return mixed
     */
    public function getBackStageUsers($app_id, $roles=[])
    {
        return AppAssignment::getBackStageUsers ($app_id, $roles);
    }    

    public function changeAgentFiStatus($request){
      $status = FiAddress::changeAgentFiStatus($request);
      if($status){
        return response()->json(['status'=>$status, 'message'=>'Status changed successfully']);
      }else{
        return response()->json(['status'=>0, 'message'=>'Something went wrong, Try again later.']);
      }
    }

    public function changeCmFiStatus($request){
      $status = FiAddress::changeCmFiStatus($request);
      if($status){
        return response()->json(['status'=>$status, 'message'=>'Status changed successfully']);
      }else{
        return response()->json(['status'=>0, 'message'=>'Something went wrong, Try again later.']);
      }
    }

    public function updateFiFile($data, $fiAddrId){
        return FiAddress::updateFiFile($data, $fiAddrId);
    }
    
    
    
    /**
     * Save pd notes 
     * 
     * @param type $attr Array
     * @param type $id Int
     * @return type mixed
     */
    public function savePdNotes($attr, $id = null)
    {
        return AppPdNote::savePdNotes($attr, $id);
    }


    public function showData($id = null)
    {
        return AppPdNote::showData($id);
    }

    public function changeAgentRcuStatus($request){
      $status = RcuDocument::changeAgentRcuStatus($request);
      if($status){
        return response()->json(['status'=>$status, 'message'=>'Status changed successfully']);
      }else{
        return response()->json(['status'=>0, 'message'=>'Something went wrong, Try again later.']);
      }
    }
    
    
    
    /**
     * Get industry 
     * 
     * @return type mixed
     */
    public function getIndustryDropDown()
    {
        return Industry::getIndustryDropDown();
    }
    
    
    /**
     * Get sub industry 
     * 
     * @param type $where Array
     * @return type mixed
     */
    public function getSubIndustryByWhere($where)
    {
        return SubIndustry::getSubIndustryByWhere($where);
    }
    
    
    
    /**
     * Save program
     * 
     * @param type $attr array
     * @return type mixed
     */
    public function saveProgram($attr)
    {
        return Program::saveProgram($attr);
    }
    
    
    /**
     * program list by id
     * 
     * @param type $id int
     * @return type mixed
     */
    public function getProgramListById($id)
    {
        return Program::getProgramListById($id);
    }
    
    
    /**
     * get selected program data
     * 
     * @param type $attr array
     * @param type $selected array
     * @return type mixed
     */
    public function getSelectedProgramData($attr, $selected = null, $relations = [])
    {
        return Program::getSelectedProgramData($attr, $selected, $relations);
    }

    /**
     * get document list 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getDocumentList($where)
    {
        return DocumentMaster::getDocumentList($where);
    }

    public function changeCmRcuStatus($request){
      $status = RcuDocument::changeCmRcuStatus($request);
      if($status){
        return response()->json(['status'=>$status, 'message'=>'Status changed successfully']);
      }else{
        return response()->json(['status'=>0, 'message'=>'Something went wrong, Try again later.']);
      }
    }

    public function updateRcuFile($data, $rcuDocId){
        return RcuDocument::updateRcuFile($data, $rcuDocId);
    }



    public function saveAnchorRelationDetails($attributes){
        $result =  AnchorRelation::creates(($attributes));
        return $result ?: false;
    }

    public function getAnchorRelationDetails($appId){
        $result =  AnchorRelation::where('app_id',$appId)->first();
        return $result;
    }

     public function updateAnchorRelationDetails($attributes, $anchor_relation_id){
        $anchor =  AnchorRelation::where('anchor_relation_id',$anchor_relation_id)->first();
        $updateAnchorData = $anchor->update($attributes);
        return $updateAnchorData ? true : false;
    }

    /**
     * Update Authority Users against application
     * 
     * @param integer $app_id $user_id
     * @return mixed
     */    
    public function updateAppApprInActiveFlag($attributes)
    {
        return AppApprover::updateAppApprInActiveFlag(($attributes));
    }


    /**
     * check Approval Authority Users against application
     * 
     * @param integer $app_id $user_id
     * @return mixed
     */    
    public function checkAppApprovers($attributes)
    {
        return AppApprover::checkAppApprovers(($attributes));
    }

    /**
     * Save Approval Authority Users against application
     * 
     * @param integer $app_id
     * @return mixed
     */    
    public function saveAppApprovers($attributes)
    {
        return AppApprover::saveAppApprovers(($attributes));
    }

    public function getApproverStatus($where){
        if(!is_array($where)){
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        return AppApprover::where($where)->first();
    }
    
    /**
     * get charges list
     * 
     * @param type $where Array 
     * @return type mixed
     */
    public function getChargesList()
    {
        return Charges::getCharagesList();
    }    
    
    /**
     * get charge 
     * 
     * @return type mixed
     */
    
    public function getChargeData($where)
    {
        return Charges::getChargeData($where);
    }
        
    /**
     * Save program doc
     * 
     * @param type $attr Array
     * @return type mixed 
     */
    public function saveProgramDoc($attr)
    {
        return ProgramDoc::saveDoc($attr);
    }
    
    
    /**
     * save program charge
     * 
     * @param type $attr Array
     * @return type mixed
     */
    public function saveProgramChrgData($attr)
    {
        return ProgramCharges::saveProgramChrgData($attr);
    }
    
    
    /**
     * delete program Data
     * 
     * @param type $where
     * @return type mixed
     */
    public function deleteProgramData($where)
    {
        return ProgramCharges::deleteProgramData($where);
    }
    

    /**
     * get sub program data 
     * 
     * @param type $id
     * @return type mixed
     */
    public function getSubProgramListByParentId($anchor_id , $program_id)
    {
        return Program::getSubProgramListByParentId($anchor_id , $program_id);
    }
    
    /**
     * Check any one post sanction document is uploaded or not
     * 
     * @param integer $appId
     * @return boolean
     */
    public function isDocsUploaded($appId, $docIds=[])
    {
        return AppDocument::isDocsUploaded($appId, $docIds);
    }


    public function getAnchorsByProduct($product_id)
    {
        return Program::getAnchorsByProduct($product_id);
    }

    public function getProgramsByAnchor($anchor_id)
    {
        return Program::getProgramsByAnchor($anchor_id);
    }

    public function getProgramBalanceLimit($program_id)
    {
        return AppProgramLimit::getProgramBalanceLimit($program_id);
    }

    public function getTotalOfferedLimit($app_id){
        return AppProgramOffer::getTotalOfferedLimit($app_id);
    }

    /**
     * Get Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getOfferData($whereCondition=[])
    {
        $offerData = AppProgramOffer::getOfferData($whereCondition);
        return $offerData ? $offerData : [];
    }

    
    /**
     * Update Offer Data By Application Id
     * 
     * @param integer $app_id
     * @param array $arr
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function updateOfferByAppId($app_id, $arr = [])
    {        
        return AppProgramOffer::updateOfferByAppId((int) $app_id, $arr);
    }

    public function updateActiveOfferByAppId($app_id, $arr = [])
    {        
        return AppProgramOffer::updateActiveOfferByAppId((int) $app_id, $arr);
    }

    /**
     * Save Offer Data
     * 
     * @param array $offerData
     * @param integer $offerId optional
     * 
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function saveOfferData($offerData=[], $offerId=null)
    {
        $offerData = AppProgramOffer::saveOfferData($offerData, $offerId);
        return $offerData ? $offerData : false;
    }

    public function saveSanctionData($sanctionData=[], $sactionId=null)
    {
        $sanctionData = AppProgramOfferSanction::saveSanctionData($sanctionData, $sactionId);
        return $sanctionData ? $sanctionData : false;
    }

    public function getOfferSanction($offerId){
        return AppProgramOfferSanction::getOfferSanction($offerId);
    }
    
    public function saveAppLimit($arr, $limit_id=null){
        return AppLimit::saveAppLimit($arr, $limit_id);
    }

    public function getAppLimit($appId){
        return AppLimit::where(['app_id'=> $appId, 'is_deleted' => 0])->first();
    }

    public function saveProgramLimit($arr, $prgm_limit_id=null){
        return AppProgramLimit::saveProgramLimit($arr, $prgm_limit_id);
    }

    public function getProgramLimitData($appId, $type=null)
    {
        $prgmLimitData = AppProgramLimit::getProgramLimitData($appId, $type);
        return $prgmLimitData ? $prgmLimitData : [];
    }

    public function getProgramOffer($app_prgm_limit_id){
        $prgmLimitOfferData = AppProgramOffer::getProgramOffer($app_prgm_limit_id);
        return $prgmLimitOfferData ? $prgmLimitOfferData : [];
    }

    public function addProgramOffer($data, $app_prgm_limit_id, $prgm_offer_id=null){
        $prgmLimitOfferData = AppProgramOffer::addProgramOffer($data, $app_prgm_limit_id, $prgm_offer_id);
        return $prgmLimitOfferData ? $prgmLimitOfferData : [];
    }

    public function getLimit(int $app_prgm_limit_id){
        $prgmLimitData = AppProgramLimit::getLimit($app_prgm_limit_id);
        return $prgmLimitData ? $prgmLimitData : [];
    }

    public function checkduplicateProgram($data){
        return AppProgramLimit::checkduplicateProgram($data);
    }
    
    public function checkduplicateOffer($data){
        return AppProgramOffer::checkduplicateOffer($data);
    }

  
    /**
     * update program data
     * 
     * @param type $attributes
     * @param type $conditions 
     * @return mixed
     */
    public function updateProgramData($attributes, $conditions)
    {
        return Program::updateProgramData($attributes, $conditions);
    }
    
    /**
     * update Doa data
     * 
     * @param type $attributes
     * @param type $conditions 
     * @return mixed
     */
    public function updateDoaData($attributes, $conditions)
    {
        return DoaLevel::updateDoaLevelData($attributes, $conditions);
    }
    
    /**
     * delete program doc
     * 
     * @param type $conditions
     * @return type mixed
     */
    public function deleteDoc($conditions)
    {
        return ProgramDoc::deleteDoc($conditions);
    }

    public function getAllOffers($appId, $product_id=null){
        return AppProgramOffer::getAllOffers((int)$appId, $product_id);
    }    
    
    /**
     * Get required documents
     * 
     * @param array $where
     * @return mixed
     */
    public function getRequiredDocs($where, $appProductIds)
    {
        return DocumentMaster::getRequiredDocs($where,$appProductIds);
    }

    /**
     * Get required documents
     * 
     * @param array $where
     * @return mixed
     */
    public function getDocumentProduct($docId)
    {
        return DocumentMaster::with('product_document')
                ->where(['id' => $docId, 'is_active' => 1])
                ->first();
    }

    /**
     * Get DoA Users By $appId
     * 
     * @param type $appId
     */
    public function getDoAUsersByAppId($appId)
    {
        return Application::getDoAUsersByAppId((int) $appId);
    }

    /**
     * Get Program Documents
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getProgramDocs($whereCondition=[])
    {
        return Application::getProgramDocs($whereCondition);
    }

    /**
     * Get Program Documents
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function createCustomerId($lmsCustomerArray = [])
    {
    	// $customerCheck = LmsUser::where('user_id', $lmsCustomerArray['user_id'])
    	// 		->first();
    	// if(!isset($customerCheck)) {
		$customer = LmsUser::create($lmsCustomerArray);
    	// } 

        return (isset($customer)) ? $customer : false;
    } 

    /**
     * Get Program Documents
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function createVirtualId($lmsCustomerArray = [], $virtualId = false)
    {
    	
		$response = LmsUser::updateVirtualId($lmsCustomerArray->lms_user_id, $virtualId);

        return (isset($response)) ? $response : false;
    }

	/**
	 * Get Applications for Application list data tables
	 */
	public function getCustomerPrgmAnchors($user_id) 
	{
        $curDate = \Carbon\Carbon::now()->format('Y-m-d');   
        
        return AppProgramOffer::whereHas('programLimit.appLimit.app.user', function ($query) use ($user_id) {
                    $query->where(function ($q) use ($user_id) {
                        $q->where('user_id', $user_id);
                    });
                })
                ->with('anchor')
                ->with('anchorUser')
                ->with('program')
                ->whereHas('programLimit.appLimit.app.acceptedOffer')
                ->whereHas('programLimit', function ($query) use($curDate) {
                        $query->where('product_id', 1);
                        $query->where('status', 1);
                        $query->where('start_date', '<=', $curDate);
                        $query->where('end_date', '>=', $curDate);
                })
                ->where('is_active', 1)
                ->where(function($q) {
                    $q->where('status', NULL)
                        ->orWhere('status', 1);
                })                        
                ->get();
	}   

    
    
    
    /**
     * save bank account
     * 
     * @param type $attributes array
     * @param type $id int
     * @return type mixed
     */
    public function saveBankAccount($attributes, $id = null)
    {
        return UserBankAccount::saveBankAccount($attributes, $id);
    }
    
    
    
    /**
     * bank account list 
     * 
     * @return type mixed
     */
    public function getBankAccountList()
    {
        return UserBankAccount:: getBankAccountList();
    }
    
    
    /**
     * update bank account
     * 
     * @param type $attributes array
     * @param type $where array
     * @return type mixed
     */
    public function updateBankAccount($attributes, $where = [])
    {
        return UserBankAccount::updateBankAccount($attributes, $where);
    }
    
    
    /**
     * get Bank account 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getBankAccountData($where)
    {
        return UserBankAccount::getBankAccountData($where);
    } 

    /**
     * get Bank account 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getAppProducts($app_id)
    {
        return AppProgramOffer::with('programLimit')
                ->where(['app_id' => $app_id,
                    'is_active' => 1]
                )
                ->get();
    }



    public function appOfferWithLimit($app_id)
    {
        return AppProgramOffer::with('programLimit')
                ->where(['app_id' => $app_id,
                    'is_active' => 1]
                )
                ->get();
    }



    public function getApplicationProduct($app_id)
    {
        return Application::with('products')
                ->where('app_id', $app_id)
                ->first();
    }

    /**
     * get Bank account 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getSTLDocs($whereCondition, $appProductIds)
    {
        return DocumentMaster::select('id as doc_id')
                ->where($whereCondition)
                ->whereHas('product_document', function ($query) use ($appProductIds) {
                    $query->whereIn('product_id', $appProductIds);
                })
                ->where('is_active', 1)
                ->get();
    }

    public function getOfferStatus($where_condition){
        return AppProgramOffer::getOfferStatus($where_condition);
    }

    public function changeOfferApprove($appId){
        return AppProgramOffer::changeOfferApprove($appId);
    }

    public function getOfferPTPQR($offerId){
        return OfferPTPQ::getOfferPTPQR($offerId);
    }

    public function addOfferPTPQ($data){
        return OfferPTPQ::addOfferPTPQ($data);
    }

    public function getEquipmentList(){
        return Equipment::getEquipmentList();
    }

    
    /**
     * get Bank account by Company ID 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getBankAccountDataByCompanyId($bank_acc_id,$comp_id)
    {
        return UserBankAccount::getBankAccountDataByCompanyId($bank_acc_id,$comp_id);
    }
    
    /**
     * get Bank account by Anchor ID 
     * 
     * @param type $where array
     * @return type mixed
     */
    public function getBankAccountDataByAnchorId($bank_acc_id,$anchorId)
    {
        return UserBankAccount::getBankAccountDataByAnchorId($bank_acc_id,$anchorId);
    }


    /**
     * Get Approval 
     * 
     * @param integer $app_id
     * @return mixed
     */    
    public function getAppApproversDetails($app_id)
    {
        return AppApprover::getAppApproversDetails((int) $app_id);
    }
    /**
     * Get Constitution 
     * 
     * @return type mixed
     */
    public function getConstitutionDropDown()
    {
        return Constitution::getConstitutionDropDown();
    }
    
    /**
     * get Bank list
     * 
     * @return type mixed
     */
    public function getBankList()
    {
        return Bank::getBankList();
    }


    /**
     * Get Constitution 
     * 
     * @return type mixed
     */
    public function getSegmentDropDown()
    {
        return Segment::getSegmentDropDown();
    }



    /**
     * Get Updated application
     * 
     * @param integer $user_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function findUserAddressyById($userAddress_id)
    {
        if (empty($userAddress_id) || !ctype_digit($userAddress_id)) {
            throw new BlankDataExceptions('No Data Found');
        }
        $result = BusinessAddress::find($userAddress_id);
        return $result ?: false;
    }

    public static function getUpdatedApp($user_id)
    {
        return Application::getUpdatedApp($user_id);
    }  


    public function addressGetCustomers($user_id, $biz_id, $address_type=null)
    {
        return BusinessAddress::addressGetCustomer($user_id, $biz_id, $address_type);

    }

    public function getAppDataByOrder($where , $orderBy = 'DESC')
    {
        return Application::getAppDataByOrder($where , $orderBy);
    }
    
    public function saveAddress($arr, $limit_id=null){
        return BusinessAddress::saveBusinessAddress($arr, $limit_id);
    }

    public function updateUserAddress($attributes, $userAddressId)
    {
      $status = BusinessAddress::where('biz_addr_id', $userAddressId)->first()->update($attributes);
      return $status ?: false;
    }

    public function setDefaultAddress($attributes, $where = [])
    {
        return BusinessAddress::setDefaultAddress($attributes, $where);
    }

    /** 
     * @Author: Rent Alpha 
     * @Date: 2020-01-31 10:21:30 
     * @Desc: function for save app status log 
     */
    public function saveAppStatusLog($attributes)
    {
        $result=AppStatusLog::saveAppStatusLog($attributes);
        return  ($result)?$result:false;
    }
    /**
     * Get Applications for Application list data tables
     */
    public function getAppData($app_id) 
    {
        $app_id=(int)$app_id;
        $result= Application::getAppData($app_id);
        return ($result)?$result:false;
    }
    /**
    * bank account list 
    * 
    * @return type mixed
    */

    public function getTotalByPrgmLimitId($appPrgmLimitId){
        return AppProgramOffer::getTotalByPrgmLimitId($appPrgmLimitId);
    }


    public function getPrgmLimitByAppId($appId){
        return AppProgramLimit::where([
                'app_id' => $appId, 
                'product_id' => 1
                ])
            ->whereIn('status', [0,1,2])
            ->with('offer')
            ->first();
    }

    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveTransaction($transactions)
    {
        return Transactions::saveTransaction($transactions);
    }
    
    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getVirtualAccIdByUserId($userId)
    {
        return LmsUser::where('user_id', $userId)
                ->pluck('virtual_acc_id')->first();
    }

    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserTypeByUserId($userId)
    {
        return User::where('user_id', $userId)
                ->pluck('is_buyer')->first();
    }

    /**
     * Get trans type
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTransTypeData($transTypeId)
    {
        return TransType::where('id', $transTypeId)
                ->first();
    }
    /**
     * Get trans type
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTransTypeDataByChargeId($chrg_master_id)
    {
        return TransType::where('chrg_master_id', $chrg_master_id)
                ->first();
    }
    
    /**
     * Get prgm charge data
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getPrgmChrgeData($prgmId, $chargeId)
    {
        return ProgramCharges::where(['prgm_id' => $prgmId, 'charge_id' => $chargeId])
                ->first();
    }

    /**
     * Get user state id by appId
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserAddress($appId)
    {
        return Application::getUserAddress($appId);
    }

    /**
     * Get company state id by appId
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function companyAdress()
    {
        return Company::companyAdress();
    }

    /**
     * Get company state id by appId
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserIdByBankAccId($bankAccId)
    {
        return UserBankAccount::where('bank_account_id', $bankAccId)
            ->pluck('user_id')
            ->first();
    }


    

    public function saveShareToColender($data, $co_lenders_share_id=null){
        return ColenderShare::saveShareToColender($data, $co_lenders_share_id);
    }

    public function getSharedColender($where, $notColenderId = null){
        return ColenderShare::getSharedColender($where, $notColenderId);
    }
    
    public function getSharedColenderData($where){
        return ColenderShare::getSharedColenderData($where);
    }

    public function updateColenderData($attributes, $conditions){
        return ColenderShare::updateColenderData($attributes, $conditions);
    }

    public function getTotalPrgmLimitByAppId($appId){
        return AppProgramLimit::getTotalPrgmLimitByAppId($appId);
    }

    public function getPrgmsByAnchor($anchor_ids, $uesr_type){
        return Program::getPrgmsByAnchor($anchor_ids, $uesr_type);
    }

    public function getOfferPrimarySecurity($offerId){
        return OfferPrimarySecurity::getOfferPrimarySecurity($offerId);
    }

    public function addOfferPrimarySecurity($data){
        return OfferPrimarySecurity::addOfferPrimarySecurity($data);
    }

    public function getOfferCollateralSecurity($offerId){
        return OfferCollateralSecurity::getOfferCollateralSecurity($offerId);
    }

    public function addOfferCollateralSecurity($data){
        return OfferCollateralSecurity::addOfferCollateralSecurity($data);
    }

    public function getOfferPersonalGuarantee($offerId){
        return OfferPersonalGuarantee::getOfferPersonalGuarantee($offerId);
    }

    public function addOfferPersonalGuarantee($data){
        return OfferPersonalGuarantee::addOfferPersonalGuarantee($data);
    }

    public function getOfferCorporateGuarantee($offerId){
        return OfferCorporateGuarantee::getOfferCorporateGuarantee($offerId);
    }

    public function addOfferCorporateGuarantee($data){
        return OfferCorporateGuarantee::addOfferCorporateGuarantee($data);
    }

    public function getOfferEscrowMechanism($offerId){
        return OfferEscrowMechanism::getOfferEscrowMechanism($offerId);
    }

    public function addOfferEscrowMechanism($data){
        return OfferEscrowMechanism::addOfferEscrowMechanism($data);
    }

    public function getColenderApplications() 
    {
        return Application::getColenderApplications();
    }

    /**
     * Get Approval status against appId
     * 
     * @param integer $app_id
     * @return mixed
     */    
    public function getSingleAnchorDataByAppId($app_id)
    {
        return Application::getSingleAnchorDataByAppId($app_id);  
    }
   
     public function getAppApprovers($app_id)
    {
        return AppApprover::getAppApprovers($app_id);
    }
    
    public function getReviewerSummaryData($appId, $bizId){
        $returnData = []; 
        $reviewerSummaryData = CamReviewerSummary::where('biz_id',$bizId)->where('app_id', $appId)->first(); 
        if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
            $reviewerSummaryData = $reviewerSummaryData->toArray();
            $returnData['reviewerSummaryData'] = $reviewerSummaryData;
            $dataPrePostCond = CamReviewSummPrePost::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])->where('is_active', 1)->get();
            if ($dataPrePostCond->count()) {
                foreach ($dataPrePostCond as $key => $value) {
                    if($value->cond_type == '1'){
                        $returnData['preCond'][] = $value->cond;
                        $returnData['preCondTimeline'][] = $value->timeline;
                    }else if($value->cond_type == '2'){
                        $returnData['postCond'][] = $value->cond;
                        $returnData['postCondTimeline'][] = $value->timeline;
                    }
                }
            } 
        }
        return  $returnData;
    }
    
    
    public function getProgramOfferData($program_id)
    {
       return AppProgramLimit::getProgramOfferData($program_id);
    }

    public function addOfferCharges($data){
        return OfferCharge::addOfferCharges($data);
    }    
    
    public function getProgram($prgm_id)
    {
        try{
            return Program::getProgram($prgm_id);
        } catch (Exception $ex) {
            return $ex;
        }
    }
    public function chkUser()
    {
        try{
            return Application::chkUser();
        } catch (Exception $ex) {
            return $ex;
        }
    }
    
    public function getProgramByProgramName($name)
    {
        return Program::getProgramByProgramName($name);
    }
    
    public function getTotalLimit($biz_id,$program_id){
        return AppProgramLimit::where('biz_id','=',$biz_id)->where('product_id','=',$program_id)->sum('limit_amt');
    }


    public static function getAppLimitIdByUserIdAppId($userId, $appId)
    {
        return AppLimit::where('user_id',$userId)->where(['app_id'=> $appId,'is_deleted' => 0])
                ->pluck('app_limit_id')->first();
    }

    public function updatePrgmLimitByLimitId($arr, $limit_id=null){
        return AppProgramLimit::updatePrgmLimitByLimitId($arr, $limit_id);
    }

    /**
     * This method is used for see upload file in Bank Account  
     */
    public function seeUploadFilePopup($acc_id, $user_id) {
        return UserBankAccount::seeUploadFilePopup($acc_id, $user_id);

    }
    
    /**
     * check the company bank account is by default set
     * 
     * @param type $attributes array
     * @param type $id int
     * @return type mixed
     */
    public function isDefalutCmpBankAcc($attributes, $is_default)
    {
        return UserBankAccount::isDefalutCmpBankAcc($attributes, $is_default);
    }
    
    /*
     *check bank account is unique for a company
     */
    public function getBankAccByCompany($attributes){
        return UserBankAccount::getBankAccStatusByCompany($attributes);
    }

    /**
    * Get all GSTs by user id  
    */
    public function getGSTsByUserId($user_id)
    {   
        return BizPanGst::getGSTsByUserId($user_id);
    }

    /**
    * update is_default to 0 in biz_addr where address_type is 6 by user id
    */
    public function unsetDefaultAddress($user_id)
    {   
        return BusinessAddress::unsetDefaultAddress($user_id);
    }

    /**
    * update is_default to 0 in biz_addr where address_type is 6 by user id
    */
    public function updateGstHideAddress($data, $biz_addr_id)
    {   
        return BizPanGst::updateGstHideAddress($data, $biz_addr_id);
    }
    

   /** get the user limit  **/

   public function getUserLimit($user_id)
   {
       try
       {
           return AppLimit::getUserLimit($user_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   }

/** get the user Total limit  **/

   public function getUserTotalLimit($user_id)
   {
       try
       {
           return AppLimit::getUserTotalLimit($user_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   }
   
   /** get the user program  limit  **/

   public function getUserProgramLimit($user_id)
   {
       try
       {
           return AppLimit::getUserApproveLimit($user_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   } 


   public function getUserActiveProgramLimit($user_id)
   {
       try
       {
           return AppLimit::getUserActiveApproveLimit($user_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   }

   public function getUserProgramLimitByBizId($biz_id)
   {
       try
       {
           return AppProgramLimit::getUserProgramLimitByBizId($biz_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   } 

   
     /** get the get Avaliable User Limit   **/
   public function getAvaliableUserLimit($attr)
   {
       try
       {
           return AppProgramLimit::getAvaliableUserLimit($attr);
       } catch (Exception $ex) {
             return $ex;
       }
       
   } 
     /** get the user offer program  limit  **/
   public function getUserProgramOfferLimit($app_prgm_limit_id)
   {
       try
       {
           return AppLimit::getUserProgramOfferLimit($app_prgm_limit_id);
       } catch (Exception $ex) {
             return $ex;
       }
       
   }  

    /**
     * Get Renewal applications
     * 
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function getRenewalApp()
    {
        return Application::getRenewalApp();
    }
    
    public function createBusiness($bizData)
    {
        return Business::create($bizData);
    }
    
    public function createApplication($appData)
    {
        return Application::create($appData);
    }
        
    public function getOwnerByBizId($bizId)
    {
        return BizOwner::getOwnerByBizId($bizId);
    }
    
    public function createBizOwner($bizOwnerData)
    {
        return BizOwner::create($bizOwnerData);
    }
    
    /**
     * Get All Addresses
     * 
     * @param array $whereCond
     * @return type
     */
    public function getBizAddresses($whereCond=[])
    {
        return BusinessAddress::getBizAddresses($whereCond);
    }
    
    /**
     * Get All Addresses By Biz Id
     * 
     * @param int $bizId
     * @return type
     */
    public function getBizApiData($whereCond=[])
    {
        return BizApi::getBizApiData($whereCond);
    }

    /**
     * Save Biz Api Data
     * 
     * @param array $bizApiData
     * @return mixed
     */
    public function saveBizApiData($bizApiData)
    {
        return BizApi::saveBizApiData($bizApiData);
    }    
    
    /**
     * Get Biz Gst Log Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getBizGstLogData($whereCond=[])
    {        
        return BizGstLog::getBizGstLogData($whereCond);
    }   
    
    /**
     * Save Biz Gst Log Data
     * 
     * @param array $bizGstLogData
     * @return mixed
     */
    public function saveBizGstLogData($bizGstLogData)
    {
        return BizGstLog::create($bizGstLogData);
    }    
  
    /**
     * Get Biz Pan Gst Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getBizPanGstData($whereCond=[])
    {
        return BizPanGst::getBizPanGstData($whereCond);
    }   
    
    /**
     * Save Biz Pan Gst Data
     * 
     * @param array $bizPanGstData
     * @return mixed
     */
    public function saveBizPanGstData($bizPanGstData)
    {
        return BizPanGst::saveBizPanGstData($bizPanGstData);
    } 
    
    /**
     * Get Biz Perfios Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getBizPerfiosData($whereCond=[])
    {
        return BizPerfios::getBizPerfiosData($whereCond);
    }   
    
    /**
     * Save Biz Perfios Data
     * 
     * @param array $bizPerfiosData
     * @return mixed
     */
    public function saveBizPerfiosData($bizPerfiosData)
    {
        return BizPerfios::saveBizPerfiosData($bizPerfiosData);
    }
    
    /**
     * Get Application Product Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppProductData($whereCond=[])
    {
        return AppProduct::getAppProductData($whereCond);
    }   
    
    /**
     * Save Application Product Data
     * 
     * @param array $appProductData
     * @return mixed
     */
    public function saveAppProductData($appProductData)
    {
        return AppProduct::saveAppProductData($appProductData);
    }

    /**
     * Get Application Documents
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppDocuments($whereCond=[])
    {
        return AppDocument::getAppDocuments($whereCond);
    } 
    
    /**
     * Save Application Documents
     * 
     * @param array $appDocsData
     * @return mixed
     */
    public function saveAppDocuments($appDocsData)
    {
        return AppDocument::create($appDocsData);
    }     

    /**
     * Get Application Document Files
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppDocFiles($whereCond=[])
    {
        return AppDocumentFile::getAppDocFiles($whereCond);
    } 
    
    /**
     * Save Application Document Files
     * 
     * @param array $appDocFiles
     * @return mixed
     */
    public function saveAppDocFiles($appDocFiles)
    {
        return AppDocumentFile::create($appDocFiles);
    }
    
    /**
     * Get Application Product Documents
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppProductDocs($whereCond=[])
    { 
        return AppDocProduct::getAppProductDocs($whereCond);
    }  
    
    /**
     * Save Application Product Documents
     * 
     * @param array $appProductDocs
     * @return mixed
     */
    public function saveAppProductDocs($appProductDocs)
    { 
        return AppDocProduct::create($appProductDocs);
    }
    
    /**
     * Get Application Business Finance Detail
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppBizFinDetail($whereCond=[])
    {
        return AppBizFinDetail::getAppBizFinDetail($whereCond);
    }    
    
    /**
     * Save Application Business Finance Detail
     * 
     * @param array $appBizFinData
     * @return mixed
     */
    public function saveAppBizFinDetail($appBizFinData)
    {
        return AppBizFinDetail::create($appBizFinData);
    } 
                
    /**
     * Get Application Business Bank Detail
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getAppBizBankDetail($whereCond=[])
    {
        return AppBizBankDetail::getAppBizBankDetail($whereCond);
    }    
    
    /**
     * Save Application Business Bank Detail
     * 
     * @param array $appBizBankData
     * @return mixed
     */
    public function saveAppBizBankDetail($appBizBankData)
    {
        return AppBizBankDetail::create($appBizBankData);
    } 

    /**
     * Get Cam Report Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getCamReportData($whereCond=[])
    {
        return Cam::getCamReportData($whereCond);
    }   
    
    /**
     * Save Cam Report Data
     * 
     * @param array $camReportData
     * @return mixed
     */
    public function saveCamReportData($camReportData)
    {
        return Cam::create($camReportData);
    } 
    
    /**
     * Get Cam Hygiene Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getCamHygieneData($whereCond=[])
    {
        return CamHygiene::getCamHygieneData($whereCond);
    }   
    
    /**
     * Save Cam Hygiene Data
     * 
     * @param array $camHygieneData
     * @return mixed
     */
    public function saveCamHygieneData($camHygieneData)
    {
        return CamHygiene::create($camHygieneData);
    }    
    
    /**
     * Get Cam Reviewer Summary Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getCamReviewerSummaryData($whereCond=[])
    {
        return CamReviewerSummary::getCamReviewerSummaryData($whereCond);
    }  
    
    /**
     * Save Cam Reviewer Summary Data
     * 
     * @param array $camReviewerSummary
     * @return mixed
     */
    public function saveCamReviewerSummaryData($camReviewerSummary)
    {
        return CamReviewerSummary::create($camReviewerSummary);
    }   
    
    /**
     * Get Cam Reviewer Risk Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getCamReviewerRiskData($whereCond=[])
    {
        return CamReviewSummRiskCmnt::getCamReviewerRiskData($whereCond);
    } 
    
    /**
     * Save Cam Reviewer Risk Data
     * 
     * @param array $camReviewerRiskData
     * @return mixed
     */
    public function saveCamReviewerRiskData($camReviewerRiskData)
    {
        return CamReviewSummRiskCmnt::create($camReviewerRiskData);
    } 
    
    /**
     * Get Cam Reviewer Pre Post Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getCamReviewerPrePostData($whereCond=[])
    {
        return CamReviewSummPrePost::getCamReviewerPrePostData($whereCond);
    }  
    
    /**
     * Save Cam Reviewer Pre Post Data
     * 
     * @param array $camReviewerPrePostData
     * @return mixed
     */
    public function saveCamReviewerPrePostData($camReviewerPrePostData)
    {
        return CamReviewSummPrePost::create($camReviewerPrePostData);
    }
    
    /**
     * Get User Application Doc Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getUserAppDocData($whereCond=[])
    {
        return UserAppDoc::getUserAppDocData($whereCond);
    }  
    
    /**
     * Save User Application Doc Data
     * 
     * @param array $userAppDocData
     * @return mixed
     */
    public function saveUserAppDocData($userAppDocData)
    {
        return UserAppDoc::create($userAppDocData);
    }  
    
    /**
     * Save application workflow stage
     * 
     * @param array $arrData
     * @return mixed
     * @throws BlankDataExceptions
     */
    public function saveWfDetail($arrData)
    {
        return WfAppStage::saveWfDetail($arrData);
    }


    /**
     * Get Applications Data
     * 
     * @param array $where
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getApplicationsData($where=[])
    {
        return Application::getApplicationsData($where);
    }   
    
    /**
     * Get all renewal applications for data table
     * 
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public function getAllRenewalApps()
    {
        return Application::getAllRenewalApps();
    }   
    
    public function updateAppLimit($data, $whereCond=[])
    {
        return AppLimit::updateAppLimit($data, $whereCond);
    }

    public function updatePrgmLimit($data, $whereCond=[])
    {
        return AppProgramLimit::updatePrgmLimit($data, $whereCond);
    }      


    /**
    * Get GSTs by user id which are associated to application 
    */
    public function getAppGSTsByUserId($user_id)
    {   
        return BizPanGst::getAppGSTsByUserId($user_id);
    }

    public function getLmsUsers($whereCond=[])
    {
        return LmsUser::getLmsUsers($whereCond);
    }


    public function getBizDataByUserId($userId)
    {
        return Business::getBizDataByUserId($userId);
    } 

     public function saveAppOfferAdhocLimit($arr, $limit_id=null){
        return AppOfferAdhocLimit::saveAppOfferAdhocLimit($arr, $limit_id);
    }

    public function getProductLimit($appId, $productId, $checkApprLimit=true) 
    {
        return AppProgramLimit::getProductLimit($appId, $productId, $checkApprLimit);
    }
    
    public function getUtilizeLimit($appId, $productId, $checkApprLimit=true) 
    {
        return AppProgramLimit::getUtilizeLimit($appId, $productId, $checkApprLimit);

    }

    public function getAppLimitData($whereCond)
    {
        return AppLimit::getAppLimitData($whereCond);
    }    
  
    
    public function getAccountActiveClosure($uid) 
    {
        try
        {
          return UserDetail::getAccountActiveClosure($uid);  
        } catch (Exception $ex) {
             return $ex;
        }
    }
    
    /**
     * Save Biz Entity Cin Data
     * 
     * @param array $bizEntityCinData
     * @return mixed
     */
    public function saveBizEntityCinData($bizEntityCinData)
    {
        return BizEntityCin::create($bizEntityCinData);
    }

    /**
     * Get Biz Entity Cin Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public function getBizEntityCinData($whereCond=[])
    {
        return BizEntityCin::getBizEntityCinData($whereCond);
    }    

    public function getNoteDataById($noteId, $AppId){
        return AppNote::getNoteDataById($noteId, $AppId);
    } 

    public function findNoteDatabyNoteId($noteId){
        return AppNote::findNoteDatabyNoteId($noteId);
    }

    public function getAllCommentsByAppId($appId){
        //return AppNote::getAllCommentsByAppId($appId);
        return AppStatusLog::getAllCommentsByAppId($appId);
    }   
    
    public function getBizDataByPan($pan, $userId=null) {
        return Business::getBizDataByPan($pan, $userId);
    }    

    public function checkAppByPan($userId)
    {
         return Application::checkAppByPan($userId);
    }
     
    public function getApplicationsByPan($userId)
    {
        return Application::getApplicationsByPan($userId);
    }

    public function getPrgmChargeData($where)
    {
        return ProgramCharges::getPrgmChargeData($where);
    }    
    
    public function getPrgmDocs($where)
    {
        return ProgramDoc::getPrgmDocs($where);
    }    

    public function getPrgmBalLimit($program_id)
    {
        return AppProgramOffer::getPrgmBalLimit($program_id);
    }    
    
    public function getProgramAnchors() 
    {
        return Program::getProgramAnchors();
    }    
    
    public function checkProgramOffers($program_id)
    {
        return AppProgramOffer::checkProgramOffers($program_id);
    }

    public function getInvoiceUtilizedAmount($attr)
    {
        return BizInvoice::getInvoiceUtilizedAmount($attr);
    }
    
    public function getSettledInvoiceAmount($attr)
    {
        return BizInvoice::getSettledInvoiceAmount($attr);
    }

    public function getParentsPrograms($program_id, &$prgmIds=[])
    {
        $where=[];
        $where['prgm_id'] = $program_id;
        $result =  Program::getProgramByWhereCond($where); 
        $children = array();
        $i = 0;
        if (count($result) > 0) {
            foreach($result as $row) {
                $children[$i] = array();
                $children[$i]['prgm_id'] = $row->prgm_id;
                $prgmIds[] = $row->prgm_id;                
                $children[$i]['children'] = $this->getParentsPrograms($row->copied_prgm_id, $prgmIds);
                $i++;            
            }
        }
        return $prgmIds;        
    }    
    
    public function deleteProgram($prgmId)
    {
        return Program::deleteProgram($prgmId);
    }    

    public function getFiAddressData($where)
    {
        return FiAddress::getFiAddressData($where);
    }

    public function getRcuDocumentData($where)
    {
        return RcuDocument::getRcuDocumentData($where);
    }

    /**
     * Save Nach
     * 
     * @param type $arr
     * @return type
     */
    public function saveNach($arr){
        return UserNach::saveNach($arr);
    }
    
    /**
     * Get Nach Data
     * 
     * @param type $whereCond
     * @return type
     */
    public function getNachData($whereCond){
        return UserNach::getNachData($whereCond);
    }
    
    /**
     * Get Nach Data
     * 
     * @param type $whereCond
     * @return type
     */
    public function getNachDataInNachId($nachIds){
        return UserNach::getNachDataInNachId($nachIds);
    }
    
    /**
     * Update Nach Data By Nach Id
     * 
     * @param type $attr
     * @param type $users_nach_id
     * @return type
     */
    public function updateNach($attr, $users_nach_id){
        return UserNach::updateNach($attr, $users_nach_id);
    }
    
    /**
     * Get Company Detail
     * 
     * @param type $whereCond
     * @return type
     */
    public function getCompAddByCompanyName($whereCond) {
        return Company::getCompAddByCompanyName($whereCond);
    }
    
    /**
     * Save Nach batch details
     * 
     * @param type $arr
     * @param type $nach_batch_id
     * @return type
     */
    public function saveNachBatch($arr, $nach_batch_id = null){
        return NachBatch::saveNachBatch($arr, $nach_batch_id);
    }

    public function getUserBankNACH($where)
    {
        return UserBankAccount::with('user_nach','bank')
                ->where($where)
                ->whereDoesntHave('user_nach')
                ->get();
    }

    public function getUserNACH($whereCondition){
        return UserNach::where($whereCondition)
            // ->where('period_to', '>',date("Y-m-d"))
            ->orderBy('created_at', 'DESC');
    }

    public static function  createNachStatusLog($nachId, $status_id)
    {
        $created_at  = \Carbon\Carbon::now()->toDateTimeString();
        $created_by = Auth::user()->user_id;

        $arr  =  [
            'users_nach_id' => $nachId,
            'status' => $status_id,
            'created_at' => $created_at,
            'created_by' => $created_by
            ]; 
        return  NachStatusLog::create($arr);  
    }

    public function getNachUserList($roleType = false)
    {
        $query =  User::with('lms_user', 'roles');
        
        if($roleType == 1) {
            $data = $query->whereHas('lms_user')->get();
        } else {
            $data = $query->whereHas('roles', function($query) use ($roleType) {
                        $query->where('role_type', $roleType);
                    })->get();

        }
        return $data ?? null;
    }
    
    /**
     * Update Nach Data By Condition
     * 
     * @param arr $attr
     * @param arr $whereCond
     * @return type
     */
    public function updateNachByUserId($attr, $whereCond){
        return UserNach::updateNachByUserId($attr, $whereCond);
    }

    public function getUserRepaymentNACH($whereCondition){
        return UserNach::where($whereCondition)
            ->where('period_to', '>',date("Y-m-d"))
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    // get all agency
    public function getAgenciByAgenciId($id){
        $agency = Agency::getAgenciByAgenciId($id);
        return $agency ?: false;
    }
    
    public function getInvoiceProcessingFeeCharge(){
        return Charges::find(12);
    }
    
    public function getAnchorPrgmUserIdsInArray($anchorId, $prgmId){
        return AppProgramOffer::getAnchorPrgmUserIdsInArray($anchorId, $prgmId);
    }

    public function saveNewSanctionLetterData($sanctionData=[], $sactionId=null)
    {
        $sanctionData = AppSanctionLetter::saveNewSanctionLetterData($sanctionData, $sactionId);
        return $sanctionData ? $sanctionData : false;
    }

    public function getOfferNewSanctionLetter($offerId,$sanctionID){
        return AppSanctionLetter::getOfferNewSanctionLetter($offerId,$sanctionID);
    }

    public function getOfferNewSanctionLetterData($whereCondition=[], $orderBy='sanction_letter_id', $onlyFirst='no'){
        return AppSanctionLetter::getOfferNewSanctionLetterData($whereCondition, $orderBy, $onlyFirst);
    }
    
    public function getAppOfferLimitApproved($userId, $appId){
        return AppStatusLog::getAppOfferLimitApproved($userId, $appId);
    }

    public function getAssetList(){
        return Asset::getAssetList();
    }

    public function getBizOwnerDataByOwnerId($bizOwnerId)
    {
        return BizOwner::getBizOwnerDataByOwnerId($bizOwnerId);
    }

    public function saveAppLimitReview($arr){
        return AppLimitReview::saveAppLimitReview($arr);
    }

    public function getAppReviewLimit(int $user_id){
        $appLimitReviewData = AppLimitReview::getAppReviewLimit($user_id);
        return $appLimitReviewData ? $appLimitReviewData : [];
    }

    public function updateAppLimitReview($arr, $whereCond){
        return AppLimitReview::updateAppLimitReview($arr, $whereCond);
    }

    public function getAppReviewLimitLatestData($whereCond){
        return AppLimitReview::getAppReviewLimitLatestData($whereCond);
    }

    public function getAddressforCustomerApp($bizId) 
    {
        return Business::getAddressforCustomerApp($bizId);
    }
    
    public function ownaddress($biz_owner_id, $biz_id, $address_type){

        return BusinessAddress::ownaddress($biz_owner_id, $biz_id, $address_type);
    }

    public function getlatestBizDataByPan($pan, $userId=null){
        return Business::getlatestBizDataByPan($pan, $userId);
    }

    public function getPrgmOfferByAppId(int $appId)
    {
        $prgmOfferData = AppProgramOffer::getPrgmOfferByAppId($appId);
        return $prgmOfferData ? $prgmOfferData : [];
    }
    public function getActiveProgram($prgm_id)
    {
        try{
            return Program::getActiveProgram($prgm_id);
        } catch (Exception $ex) {
            return $ex;
        }
    }

    public function updateConsentByConsentId($ckyc_consent_id,$consentData){

        return UserCkycConsent::updateConsentByConsentId($ckyc_consent_id,$consentData);
    }

    public function updateConsentByuserId($where,$consentData){

        return UserCkycConsent::updateConsentByuserId($where,$consentData);
    }

    public function getUserConsent($where){

        return UserCkycConsent::getUserConsent($where);
    }

    public function getckycDocs($value, $appProductIds=null)
    {
        return DocumentMaster::select('id as doc_id')
                ->where('doc_name','like', '%'.$value.'%')
                ->where('is_active', 1)
                ->first();
    }


    public function updateAppGroupDetails($whereCondition = null, $whereInCondition = null, $data)
    {
        return AppGroupDetail::updateAppGroupDetails($whereCondition, $whereInCondition, $data);
    }
    
    public function updateUcicUserData($whereCondition = null, $whereInCondition = null, $data)
    {
        return UcicUser::updateUcicUserData($whereCondition, $whereInCondition, $data);
    }
    
    public function getGroupIdByAppId($appId)
    {
        return UcicUserUcic::getGroupIdByAppId($appId);
    }

    public function getAppByParentAppId($appId) {
        return Application::getAppByParentAppId((int) $appId);
    }

    public function checkAppByPanForNonAnchorLeads($userId)
    {
        return Application::checkAppByPanForNonAnchorLeads($userId);
    }

    public function getUcicUserAppCurrentStatus($app_id) 
    {
        $app_id=(int)$app_id;
        $result= Application::getUcicUserAppCurrentStatus($app_id);
        return ($result)?$result:false;
    }

    public function getappByUcicId($where){

        return UcicUserUcic::getappByUcicId($where);
    }

    public function getCompanyReport($where){

        return UserCkycReport::getCompanyReport($where);
    }
}