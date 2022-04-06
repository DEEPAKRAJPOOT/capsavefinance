<?php
namespace App\Inv\Repositories\Entities\User;
use Carbon\Carbon;
use App\Inv\Repositories\Models\Relationship;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Otp;
use App\Inv\Repositories\Contracts\UserInterface;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Models\Master\Permission as PermissionModel;
use App\Inv\Repositories\Models\Master\PermissionRole as PermissionRole;
use App\Inv\Repositories\Models\Master\RoleUser;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\Agency;
use App\Inv\Repositories\Models\AnchorUser;
use App\Inv\Repositories\Models\LeadAssign;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\UserBankAccount;
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use DB;
use Auth;
use App\Inv\Repositories\Models\CoLenderUsers;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Program;

class UserRepository extends BaseRepositories implements UserInterface
{

    use CommonRepositoryTraits,
        AuthTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes)
    {
        
        return UserModel::create($attributes);
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns
     */
    public function find($id, $columns = array('*'))
    {
       
        return (UserModel::find($id)) ?: false;
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $id)
    {
        $result = UserModel::updateUser((int) $id, $attributes);

        return $result ?: false;
    }

    /**
     * Delete method
     *
     * @param mixed $ids
     */
    protected function destroy($ids)
    {
        //
    }

    /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function save($attributes = [], $userId = null)
    {
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

        return is_null($userId) ? $this->create($attributes) : $this->update($attributes,$userId);
    }
   /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function getOwnerAppRes($attributes = [])
    {
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
          return BizApi::getKarzaRes($attributes);
  
       }

     
    /**
     * Validating and parsing data passed though this method
     *
     * @param array $attributes
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function saveUserDetails($attributes)
    {  
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
        
        return UserDetail::saveUserDetails($attributes);
    }
    /**
     * Get a user model by email
     *
     * @param string $email
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserByEmail($email)
    {
        $result = UserModel::where('email', $email)->first();

        return $result ?: false;
    }

    /**
     * Get a user model by user_name
     *
     * @param string $email
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserByUserName($userName)
    {
        $result = UserModel::where('username', $userName)->first();

        return $result ?: false;
    }



     /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function saveOtp($attributes)
    {
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
        return Otp::saveOtp($attributes);
    }

    
    /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function updateOtp($attributes, $id)
    {
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
        return Otp::updateOtp($attributes, $id);
    }

        /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function updateOtpByExpiry($attributes, $id)
    {
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
        return Otp::updateOtpByExpiry($attributes, $id);
    }

    /**
     * Get a user model by otp
     *
     * @param string $opt
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getOtps($otp)
    {
       // $result = Otp::where('otp_no', $otp)->first();

        //return $result ?: false;

         return Otp::getOtps($otp);
    }
    
    public function selectOtpbyLastExpired($user_id)
    {
         return Otp::selectOtpbyLastExpired($user_id);
    }
    
    public function selectOtpbyLastExpiredByThirty($user_id)
    {

         return Otp::selectOtpbyLastExpiredByThirty($user_id);
    }
    
    public function getOtpsbyActive($otp)
    {
       // $result = Otp::where('otp_no', $otp)->first();

        //return $result ?: false;

         return Otp::getOtpsbyActive($otp);
    }
    

    /**
     * Get a user model by otp
     *
     * @param string $opt
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserByOPT($otp,$user_id)
    {
       // $result = Otp::where('otp_no', $otp)->first();

        //return $result ?: false;

         return Otp::getUserByOPT($otp,$user_id);
    }




    /**
     * Get a user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserID($userId)
    {
        $result = UserModel::getUserID((int) $userId);

        return $result ?: false;
    }


    /**
     * Get a user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserDetail($userId)
    {
        $result = UserModel::getUserDetail((int) $userId);

        return $result ?: false;
    }

    /**
     * Get a customer model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getCustomerDetail($userId)
    {
        $result = UserModel::getCustomerDetail((int) $userId);

        return $result ?: false;
    }

   
     /**
     * Get a user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getfullUserDetail($userId)
    {
        $result = UserModel::getfullUserDetail((int) $userId);

        return $result ?: false;
    }

    /**
     * Get a user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getAllUsers()
    {
        $result = UserModel::getAllUsers();

        return $result ?: false;
    }
    
    
    /**
     * Get a user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getAllUsersPaginate()
    {
        $result = UserModel::getAllUsersPaginate();

        return $result ?: false;
    }
    

    /**
     * Save backend user
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function updateUser(array $attributes, $userId)
    {
        $result = UserModel::updateBackendUser((int) $userId, $attributes);

        return $result ?: false;
    }

    /**
     * delete user
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function deleteUser(array $userId)
    {  
        $current_data_time = Carbon::now()->toDateTimeString();
        $result = UserModel::whereIn('id', $userId)->update(['deleted_at' => $current_data_time]);
        return $result ?: false;
    }
    
    
    /**
     * Get a user model by email and is_block
     *
     * @param string $email
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getUserByEmailforOtp($email)
    {
        $result = UserModel::where('email', $email)
            ->where('is_active', 0)
            ->where('is_otp_verified', 0)
            ->first();

        return $result ?: false;
    }




    /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function saveUserPersonal($attributes,$id)
    {
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
        return Userpersonal::saveUserPersonal($attributes,$id);
    }



/**
     * Get a user Corporation Data model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
   

     public function getUserPersonalData($userId)
    {
        $result = UserModel::getUserPersonalData((int) $userId);

        return $result ?: false;
    }
    
   /**
     * Get a user Corporation Data model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
   

     public function getUserCorpData($userId)
    {
        $result = UserModel::getUserCorpData((int) $userId);

        return $result ?: false;
    }



    //
    /**
     * Get user KYC Personal data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function storeUseKycPersonalData($inputData,$id){
        
        return Userpersonal::storeData($inputData,$id);
    }

    /**
     * Get user KYC Personal data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getPersonalInfo($user_id){
        
        return Userpersonal::getData($user_id);
    }
    
    
    /**
     * Get user KYC Personal data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getUseKycPersonalData($user_kyc_id){
        
        return Userpersonal::getKycPersonalData($user_kyc_id);
    }
     
    
    /**
     * Store user family data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUseKycFamilyData($inputData,$id)
    {
       
        //dd($inputData);
        return UserFamily::storeData($inputData,$id);
    }
    
    
    /**
     * Get user KYC Residential data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getFamilyInfo($user_kyc_id){
        
        return UserFamily::getData($user_kyc_id);
    }
    
    
    /**
     * Store user Residential data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycResidentialData($inputData,$id)
    {
        return UserResidential::storeData($inputData,$id);
    }
    
    /**
     * Get user KYC Residential data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getResidentialInfo($user_kyc_id){
        
        return UserResidential::getData($user_kyc_id);
    }
    
    /**
     * Store user KYC Professional data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycProfessionalData($inputData,$id)
    {
        return UserProfessional::storeData($inputData,$id);
    }
    
     
    /**
     * Get user KYC Financial data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getProfessionalInfo($user_kyc_id){
        
        return UserProfessional::getData($user_kyc_id);
    }
    
    /**
     * Store user KYC Commercial data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycCommercialData($inputData,$id)
    {
        return UserCommercial::storeData($inputData,$id);
    }
    
    /**
     * Get user KYC Financial data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getCommercialInfo($user_kyc_id){
        
        return UserCommercial::getData($user_kyc_id);
    }
    
    /**
     * Store user KYC Financial data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycFinancialData($inputData,$id)
    {
        return UserFinancial::storeData($inputData,$id);
    }
    
    /**
     * Get user KYC Financial data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getFinancialInfo($user_kyc_id){
        
        return UserFinancial::getData($user_kyc_id);
    }
    
     /**
     * Store User Kyc DocumentTypeData
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycDocumentTypeData($inputData)
    {
        return Userdocumenttype::storeData($inputData,null);
    }
    
    /**
     * Get user KYC Financial data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getDocumentTypeInfo($user_kyc_id){
        
        return Userdocumenttype::getData($user_kyc_id);
    }
    
    
    /**
     * Get user Document Type data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function deleteDocumentType($user_kyc_id){
        
        return Userdocumenttype::deleteData($user_kyc_id);
    }
    //
    /**
     * Store user KYC Financial data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycSocialmediaData($inputData)
    {
        return UserSocialmedia::storeData($inputData,null);
    }
    
    /**
     * Get user KYC Financial data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getSocialmediaInfo($user_kyc_id){
        
        return UserSocialmedia::getData($user_kyc_id);
    }
    
    /**
     * Get user Document Type data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function deleteSocialmediaInfo($user_kyc_id){
        
        return UserSocialmedia::deleteData($user_kyc_id);
    }
    
    /**
     * Save Promoter Details
     *
     * @param mixed $id
     * @param array $columns
     */

    public function updateOwnerInfo($attributes = []){
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

       
        return BizOwner::creates($attributes);
    }
    
     public function saveOwner($attributes = []){
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

       
        return BizOwner::createsOwner($attributes);
    }
    
     /**
     * Find CIN Number By user id
     *
     * @param mixed $id
     * @param array $columns
     */
    public function getCinByUserId($biz_id)
    {
          return   BizPanGst::where('biz_id',$biz_id)->first();
           
    }
   
     /**
     * Get a backend user by id
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getBackendUser($user_id)
    {
        $user = UserModel::getBackendUser((int) $user_id);

        if (empty($user)) {
            return false;
        }

        return $user[0];
    }
    

/**
 * function for save anchor company detail
 * @param type $attributes
 * @return type
 */
    public function saveAnchor($attributes){
         return Anchor::saveAnchor($attributes);
     }
    
   /**
    *  function for add anchor company information 
    * @param type $arrAnchorUser
    * @return type
    */  
     public function saveAnchorUser($arrAnchorUser){
         return AnchorUser::saveAnchorUser($arrAnchorUser);
     }
    
     /**
      * function for get all anchor register user detail
      * @return type
      */
     
        public function getAllAnchorUsers($datatable=false)
        {
          $result = AnchorUser::getAllAnchorUsers($datatable);
        
          return $result ?: false;
        }
    

     /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public function getOwnerApiDetail($attributes = [])
    {
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

        return BizOwner::getOwnerApiDetails($attributes); 
    }
    
    /* get owner details behalf of biz id    */
    /* Created by gajendra chauhan  */
    public function getOwnerDetail($attributes = [])
    {
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
       
        return BizOwner::getOwnerByBizId($attributes['biz_id']); 
    }
    
    /**
      * function for get all anchor register user detail
      * @return type
      */
     
        public function getAnchorUsersByToken($token)
        {
          $result = AnchorUser::getAnchorUsersByToken($token);
        
          return $result ?: false;
        }
    
            
    /**
    * function for get all anchor register user detail
    * @return type
    */
     
        public function getAllAnchor($orderBy='anchor_id', $datatable=false)
        {
          $result = Anchor::getAllAnchor($orderBy, $datatable);
        
          return $result ?: false;
        }
        
        
         /**
      * function for get all anchor register user detail
      * @return type
      */
        public function getAnchorById($anch_id)
        {
            
          $result = Anchor::getAnchorById($anch_id);
        
          return $result ?: false;
        }
        
     /**
      * 
      * @param type $appId
      * @param type $attributes
      * @return boolean
      * @throws InvalidDataTypeExceptions
      * @throws BlankDataExceptions
      */  
   
     public function updateAnchor($anchoId, $attributes = []){
        $result = Anchor::updateAnchor((int) $anchoId, $attributes);
        return $result ?: false;
    }    
    /**
     * function for get user details using anchor id
     * @param type $userName
     * @return type
     */
    public function getUserByAnchorId($anchId)
    {
        $result = UserModel::getUserByAnchorId((int)$anchId);
         return $result ?: false;
    } 
    /**
     * function for get user details using app id
     * @param type $userName
     * @return type
     */
    public function getUserByAppId($appId)
    {
        $result = UserModel::getUserByAppId($appId);
        return $result ?: false;
    } 
      /**
     * Get a backend user by id
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getRoleByArray($arr)
    {
        $user = Role::getRoleByArray($arr);
        return $user;
    }

   /**
     * 
     * @param type $anchoId
     * @param type $attributes
     * @return type
     */
    public function updateAnchorUser($anchUId, $attributes = []) {
        $result = AnchorUser::updateAnchorUser((int) $anchUId, $attributes);
        return $result ?: false;
    }

    /**
     * 
     * @param type $email
     * @return type
     */
     public function getAnchorUsersByEmail($email)
        {
          $result = AnchorUser::getAnchorUsersByEmail($email);
        
          return $result ?: false;
        } 
        
        /**
         * function for get state list
         * @return type
         */
        public function getStateList() {
        $all_state = State::getStateList();        
        return $all_state ?: false;
    }
      
      /**
     *
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getRoleList()
    {
        $role = Role::getRoleLists();
        return $role;
    }
      
      /**
     * add role
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function addRole($roleData, $role_id)
    {
        $role = Role::addRole($roleData, $role_id);
        return $role;
    }
    
      
      /**
     * Get a role  by id
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getRole($role_id)
    {
        $role = Role::getRole($role_id);
        return $role;
    }
    
     
      /**
     * Get a all permition list
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getRoute()
    {
        $role = PermissionModel::getRoute();
        return $role;
    }
    
    
      /**
     * Get a all parent Route list
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getParaentRoute()
    {
        $role = PermissionModel::getParentRoute();
        return $role;
    }
    
     /**
     * Delete a role by id
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function deleteRecById($role_id)
    {
        $role = PermissionRole::deleteRecById($role_id);
        return $role;
    }
    
     /**
     * Add role
     *
     * @param integer $user_id
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function addPermissionRole($arr)
    {
        $role = PermissionRole::addPermissionRole($arr);
        return $role;
    }
    
    /**
     * Give permission to role
     *
     * @param array $attributes
     *
     * @return object
     */
    public function givePermissionTo($roleid, $permission)
    {

        $role   = Role::where('id', $roleid)->first();
        $result = $role->assignRolePermission($permission);

        return $result ? : false;
    }
    
   
    /**
     * Get all children of permmision
     *
     * @param type $permission_idgetChildByPermissionId
     *
     * @return permissions object
     */
    public function getChildByPermissionId($permission_id)
    {
        return PermissionModel::getChildByPermissionId($permission_id);
    }   
        /**
         * function for assign lead
         * @param type $arrLeadAssign
         * @return type
         */
        public function createLeadAssign($arrLeadAssign){
          $result = LeadAssign::createLeadAssign($arrLeadAssign);        
          return $result ?: false;
        } 
        
    /**
     * Get Lead Sales Manager
     * 
     * @param integer $userId
     * @return mixed
     */    
    public function getLeadSalesManager($userId)
    {
        return UserModel::getLeadSalesManager($userId);
    }
    
     /**
     * Get user role 
     * 
     * @return mixed
     */    
    public function getAllData()
    {
        return RoleUser::getAllData();
    }
    
     /**
     * set user role 
     * 
     * @return mixed
     */    
    public function addNewRoleUser($roleData)
    {
        return RoleUser::addNewRoleUser($roleData);
    }
    
      /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getRoleDataById($user_id)
    {
       return RoleUser::getRoleDataById($user_id);
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function updateUserRole($userId, $role)
    {
       return RoleUser::updateUserRole($userId, $role);
    }

    /**
     * get all agency users list
     * @return users
     */
    public function getAllAgencyUsers(){
        $users = UserModel::where('agency_id','<>', null)->where('is_active',1)->get(['user_id','agency_id','f_name','m_name','l_name']);
        return $users ?: false;
    }
    

    
    /**
     * get all anchor list
     * 
     * @param type $id int
     * @return type mixed
     */
    public function getAnchorDataById(int $id)
    {
        return Anchor::getAnchorDataById($id);
    }

    /**
     * Get Backend Roles          
     * 
     * @return mixed
     */
    public function getAllRole()
    {
        $role = Role::getAllRole();
        return $role;
    }

    /**
    * function for get all anchor register user detail
    * @return type
    */
     
    public function getAllAgency()
    {
      $result = Agency::orderBy('agency_id', 'DESC');
      return $result ?: false;
    }

    /**
    * function for get all anchor register user detail
    * @return type
    */
    public function getAgencyById($agency_id)
    {
      $result = Agency::find($agency_id);
      return $result ?: false;
    }

    public function saveAgency($attributes){
        $status = Agency::creates($attributes);
        return $status ?: false;
    }

    public function updateAgency($attributes, $agency_id){
        $status = Agency::updateAgency($attributes, $agency_id);
        return $status ?: false;
    }

    public function getAgencyUserLists(){
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $result = UserModel::orderBy('user_id', 'DESC')->with('agency')->where('agency_id','<>', null)->where('users.agency_id', \Auth::user()->agency_id);
        $resultAdmin = UserModel::orderBy('user_id', 'DESC')->with('agency')->where('agency_id','<>', null);

        if ($roleData[0]->is_superadmin != 1) {
            return $result;
        } else {
            return $resultAdmin;
        }

        return $result ?: false;

    }
    
    /**
     * Get Backend Users
     * 
     * @return type
     */
    public function getBackendUsers(){
        return UserModel::getBackendUsers();
    }
    
    /**
     * Get Backend Users By Role Id
     * 
     * @param integer $role_id
     * @return array
     */
    public function getBackendUsersByRoleId($role_id, $usersNotIn=[], $orUsersIn=[])
    {
        return RoleUser::getBackendUsersByRoleId($role_id, $usersNotIn, $orUsersIn);
    }
    
    /**
     * Get Roles By role_type
     *      
     * @param integer $role_type
     */
    public function getRolesByType($role_type) 
    { 
        return Role::getRolesByType($role_type);
    }

    /**
     * Get Child users of parent User Id
     * 
     * @param integer $parentUserId
     * @return mixed
     */
    public function getChildUsers($parentUserId, &$usersIds=[])
    {
        $result = UserModel::getChildUsers($parentUserId);        
        $children = array();
        $i = 0;
        if (count($result) > 0) {
            foreach($result as $row) {
                $children[$i] = array();
                $children[$i]['name'] = $row->f_name . ' ' . $row->l_name;
                $children[$i]['user_id'] = $row->user_id;
                $usersIds[] = $row->user_id;                
                $children[$i]['children'] = $this->getChildUsers($row->user_id, $usersIds);
                $i++;            
            }
        }
        return $usersIds;
    }


     /**
    * function for get all charges register user detail
    * @return type
    */   
    public function getAllCharges(){
      $result = Charges::orderBy('id', 'DESC');
      return $result ?: false;
    }

    public function saveCharges($attributes){
        $status = Charges::create($attributes);
        return $status ?: false;
    }
    
    /**
     * Get Approval Authority Users
     *
     * @return mixed
     */
    public function getApprAuthorityUsers()
    {
        $result = UserModel::getApprAuthorityUsers();
        return $result ? $result : [];
    } 

    /**
     * Get all customer from lms_users
     *
     * @return query data
     */
    public function lmsGetCustomers()
    {

        $getAppId  = Application::getSanctionApp();
        $result = LmsUser::whereIn('app_id',$getAppId)->with('user')->orderBy('lms_user_id','DESC');
        return $result ?: false;
    }

    public function lmsGetCustomer($userId){
        return LmsUser::where('user_id','=', $userId)->first();
    }

    /**
     * Get all customer from lms_users
     *
     * @return query data
     */
    public function lmsGetDisbursalCustomer($userIds = [])
    {
        $result = LmsUser::lmsGetDisbursalCustomer($userIds);
        return $result ?: false;
    }
    
    /**
     * Get Assigned Sales Manager User Id
     * 
     * @param integer $userId
     * @return integer
     */
    public function getAssignedSalesManager($userId)
    {
        return LeadAssign::getAssignedSalesManager($userId);
    }
    
    /**
     * Save co lender users
     * 
     * @param array $attributes
     * @return mixed
     */
    public function saveColenderUsers($attributes ,  $id)
    {
        return CoLenderUsers::saveColenderUsers($attributes , $id);
    }
    
    
    /**
     * get colender list
     * 
     * @return mixed
     */
    
    public function getColenderList()
    {
        return CoLenderUsers::getColenderList();
    }
    
    
    /**
     * get co lender data
     * 
     * @param array $where
     * @return mixed
     */
    public function getCoLenderData($where)
    {
       return CoLenderUsers::getCoLenderData($where);  
    }
    
    
    
    
    /**
     * get disbursal list
     * 
     * @return mixed
     */
    public function getDisbursalList()
    {
        return Disbursal::getDisbursalList();
    }

    /**
     * user bank account list 
     * 
     * @return type mixed
     */
    public function getUserBankAccounts($userId)
    {
        return UserBankAccount::with('bank')
            ->where('user_id', $userId)
            ->get();
    }
    
    /**
     * Get  Users details
     * 
     * @return type
     */
    public function getSingleUserDetails($user_id){
        $user_id=(int)$user_id;
        $result= UserModel::getSingleUserDetails($user_id);
        return $result?$result:'';
    }

    /**
     * Get all customer from lms_users
     *
     * @return query data
     */
    public function lmsGetRefundList()
    {
        $result = Disbursal::lmsGetRefundList();
      
        return $result ?: false;
    } 

    /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public function updateUserRolePassword($arrData, $userId)
    {
        $status = User::updateUserRolePassword($arrData, $userId);
        return $status ?: false;
    }
    
    /**
     * Get one user from anchor_users by email
     *
     * @return query data
     */
    public function getExistEmailStatus($email){
       return AnchorUser::getAnchorUsersByEmail($email);
    }
       

    /**
     * Get all customer from lms_users
     *
     * @return query data
     */
    public function lmsGetSentToBankInvCustomer($userIds = [])
    {
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        if( $chkUser->id==11)
        {
            $res = User::where('user_id',$id)->first();
            $anchor_id = $res->anchor_id;
        }
        else
        {
            $anchor_id="";
        }
       $result = Disbursal::select('*', DB::raw('sum(disburse_amount) as total_disburse_amount'))
                ->with(['lms_user.bank_details.bank', 'invoice_disbursed',  'user.anchor_bank_details.bank', 'disbursal_batch'])
                ->whereHas('invoice_disbursed.invoice', function($query) use ($anchor_id){
                    $query->where('status_id', 10);
                    if($anchor_id!='')
                    {
                       $query->where('anchor_id',$anchor_id);
                    }
                })
                ->whereHas('invoice_disbursed.invoice', function($query) use ($anchor_id){
                    if($anchor_id!='')
                    {
                       $query->where('anchor_id',$anchor_id);
                    }
                })
                ->groupBy(['disbursal_batch_id', 'user_id'])
                ->orderBy('disbursal_id', 'DESC');
        return $result ?: false;
    } 

    public function lmsGetSentToBankInvToExcel($custId = null, $selectDate=null, $batchId = null)
    {
        $result = Disbursal::select('*', DB::raw('sum(disburse_amount) as total_disburse_amount'))
                ->with(['lms_user.bank_details.bank', 'invoice_disbursed.invoice.program_offer',  'user.anchor_bank_details.bank', 'disbursal_batch', 'lms_user.user','disbursal_batch.disbursal_api_log'])
                ->whereHas('invoice_disbursed.invoice', function($query) {
                    $query->where('status_id', 10);
                })
                ->groupBy(['disbursal_batch_id', 'user_id'])
                ->orderBy('disbursal_id', 'DESC')

                ->whereHas('lms_user', function($query) use ($custId) {
                    if ($custId != null) {
                            $query->where('customer_id', 'like',"%$custId%");
                        }
                    })
                ->whereHas('disbursal_batch', function($query) use ($selectDate) {
                    if ($selectDate != null) {
                        $query->where('created_at', 'like',"%$selectDate%");
                    }
                })
                ->whereHas('disbursal_batch', function($query) use ($batchId) {
                    if ($batchId != null) {
                        $query->where('disbursal_batch_id', 'like',"%$batchId%");
                    }
                })
                ->get();

        return $result ?: false;
    }  
    
    public function getExistEmailStatusAnchor($comp_email){
        return Anchor::getExistEmailStatusAnchor($comp_email);
    }

    public function getBusinessDetails($biz_id){
        return Business::find($biz_id);
    }

    /**
     * check leasing product type
     * 
     * @return type mixed
     */
    public function checkLeasingProduct($appId)
    {
        try
        {
           return   DB::table('app_product')->where(['app_id' => $appId,'product_id' =>3])->count();
        } catch (Exception $ex) {
           return $ex;
        }     
    }     

    public function getAnchorsByUserId($userId) {
        return AnchorUser::getAnchorsByUserId($userId);
    }
    
    
    public function getAnchorByPan($pan){
        return AnchorUser::getAnchorByPan($pan);
    }  
    
    
    public function updateAnchorUserData($arrUserData, $whereCond){
        return AnchorUser::updateAnchorUserData($arrUserData, $whereCond);
    }    
    
    public function getAnchorUserData($whereCond) {
        return AnchorUser::getAnchorUserData($whereCond);
    }    

    /**
     * Get a sales user model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getfullSalesUserDetail($userId)
    {
        $result = UserModel::getfullSalesUserDetail((int) $userId);

        return $result ?: false;
    }
    
    /**
     * 
     * @param type $emailId
     * @param type $attributes
     * @return type
     */
    public function updateAnchorUserByEmailId($emailId, $attributes = []) {
        $result = AnchorUser::updateAnchorUserByEmailId($emailId, $attributes);
        return $result ?: false;
    }

    /**
     * Update User status in agency user list
     * 
     * @param type $attributes
     * @param type $conditions 
     * @return mixed
     */
    public function updateUserStatus($attributes, $conditions)
    {
        return User::updateUserStatus($attributes, $conditions);
    }

    // Check role id behalf of user_id
    public function getAllRoleDataByUserId($uesrId)
    {
        return RoleUser::getAllRoleDataByUserId($uesrId);
    }
    
    public function updateAgencyStatus($attributes, $conditions)
    {
        return Agency::updateAgencyStatus($attributes, $conditions);
    }
    // Frontend Dashboard
    public function getSupplierDataById($supplierId)
    {
        return User::getSupplierDataById($supplierId);
    }    
    public function getLenderAnchorDetail($anchorId = null)
    {
        return Anchor::get();
    }

    public function getAnchorUserDataDetail($anchorId = null)
    {
        return AnchorUser::getAnchorUserDataDetail($anchorId);
    }

    public function getAnchorInactiveUserDataDetail($anchorId = null)
    {
        return AnchorUser::getAnchorInactiveUserDataDetail($anchorId);
    }

    public function getAnchorAppDataDetail($anchorId = null)
    {
        return Application::getAnchorAppDataDetail($anchorId);
    }

    public function getAnchorInvoiceDataDetail($anchorId = null)
    {
        return BizInvoice::getAnchorInvoiceDataDetail($anchorId);
    }

    public function getAnchorDetail($anchorId = null)
    {
        return Program::with('programList')
            ->where('anchor_id', $anchorId)
            ->where('parent_prgm_id', 0)
            ->first();
    }

    public function getPrgmDetail($anchorId = null, $prgmId = null)
    {
        return Program::getSubProgramListByParentId((int) $anchorId, $prgmId)->get();
    }
    
    public function getAnchorUserDataByDate($whereCond, $date)
    {
        return AnchorUser::getAnchorUserDataByDate($whereCond, $date);
    }

    public function getActiveChrgDeleteEmailAllowedRoles()
    {
        return  Role::getActiveChrgDeleteEmailAllowedRoles();
    }
    
    /**
     * Get Anchor 
     * 
     * @param array $where
     * @return mixed mixed
     */
    public function getAnchorData($where=[])
    {
        return Anchor::getAnchorData($where);
    }

}

