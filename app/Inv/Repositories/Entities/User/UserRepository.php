<?php

namespace App\Inv\Repositories\Entities\User;

use Carbon\Carbon;

use App\Inv\Repositories\Models\Relationship;
use App\Inv\Repositories\Models\Userdetail;
use App\Inv\Repositories\Models\Otp;
use App\Inv\Repositories\Contracts\UserInterface;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
        return Userdetail::saveUserDetails($attributes);
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
     * Store user KYC Bissiness Address data
     *
     * @param array $inputData
     * @param integer $Id
     * @return array
     */
    public function storeUserKycBussAddrData($inputData,$id)
    {
        return UserBussinessAddress::storeData($inputData,$id);
    }
    
    /**
     * Get user KYC Bissiness Address data
     *
     * @param 
     * @param integer $user_id
     * @return array
     */
    public function getBussAddrInfo($user_kyc_id){
        
        return UserBussinessAddress::getData($user_kyc_id);
    }
   
}