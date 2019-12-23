<?php

namespace App\Inv\Repositories\Entities\User;
use Carbon\Carbon;
use App\Inv\Repositories\Models\Relationship;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\BizApi;
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
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use DB;

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
          $owner =  BizPanGst::where('biz_id',$biz_id)->first();
             if (empty($owner)) {
            return false;
        }

        return $owner;
           
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
     
        public function getAllAnchorUsers()
        {
          $result = AnchorUser::getAllAnchorUsers();
        
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
     
        public function getAllAnchor($orderBy='anchor_id')
        {
          $result = Anchor::getAllAnchor($orderBy);
        
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
        $status = Agency::create($attributes);
        return $status ?: false;
    }

    public function updateAgency($attributes, $agency_id){
        $status = Agency::whereAgencyId($agency_id)->update($attributes);
        return $status ?: false;
    }

    public function getAgencyUserLists(){
        $result = UserModel::orderBy('user_id', 'DESC')->where('agency_id','<>', null);
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
    public function getBackendUsersByRoleId($role_id)
    {
        return RoleUser::getBackendUsersByRoleId($role_id);
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
}