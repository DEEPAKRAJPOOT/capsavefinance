<?php

namespace App\Helpers;

use Mail;
use Exception;
use Carbon\Carbon;
use App\Helpers\PaypalHelper;
use App\Inv\Repositories\Models\Patent;
use DB;
class Helper extends PaypalHelper
{

    /**
     * Send exception emails
     *
     * @param Exception $exception
     * @param string    $exMessage
     * @param boolean   $handler
     */
    public static function shootDebugEmail($exception, $handler = false)
    {
        $request                 = request();
        $data['page_url']        = $request->url();
        $data['loggedin_userid'] = (auth()->guest() ? 0 : auth()->user()->id);
        $data['ip_address']      = $request->getClientIp();
        $data['method']          = $request->method();
        $data['message']         = $exception->getMessage();
        $data['class']           = get_class($exception);

        if (app()->envrionment('live') === false) {
            $data['request'] = $request->except('password');
        }

        $data['file']  = $exception->getFile();
        $data['line']  = $exception->getLine();
        $data['trace'] = $exception->getTraceAsString();

        $subject = 'Inventrust ('.app()->environment().') '.($handler ?
            '' : 'EXCEPTION').' Error at '.date('Y-m-d D H:i:s T');

        config(['mail.driver' => 'mail']);
        Mail::raw(
            print_r($data, true),
            function ($message) use ($subject) {
            $message->to(config('errorgroup.error_notification_group'))
                ->from(
                    config('errorgroup.error_notification_email'),
                    config('errorgroup.error_notification_from')
                )
                ->subject($subject);
        });
    }

    /**
     * Get exception message w.r.t. application environment
     *
     * @param  Exception $exception
     * @return string
     */
    public static function getExceptionMessage($exception)
    {
        $exMessage = trans('error_messages.generic.failure');

        $actualException = 'Error: '.$exception->getMessage().
            ' . File: '.$exception->getFile().' . Line#: '.$exception->getLine();

        if (config('app.debug') === false) {
            self::shootDebugEmail($exception);
            return $exMessage;
        } else {
            return $actualException;
        }
    }

    /**
     * Get How you heard about us drop down
     * 
     * @return object|array
     */
    public static function getHeardFromDropDown()
    {
        return \App\Inv\Repositories\Models\Master\HeardFrom::getDropDown();
    }

    /**
     * Get Country drop down
     *
     * @return object|array
     */
    public static function getCountryDropDown()
    {
        return \App\Inv\Repositories\Models\Master\Country::getDropDown();
    }

    /**
     * Get How you heard about us drop down
     *
     * @return array
     */
    public static function getYearDropdown()
    {
        $year = [];
        for ($i = date("Y"); $i >= config('inv_common.START_YEAR'); $i--) {
            $year[$i] = $i;
        }
        return $year;
    }

    /**
     * Get all months
     *
     * @return array
     */
    public static function getMonthDropdown()
    {
        $months = [
            'January' => 'January',
            'February' => 'February',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'August' => 'August',
            'September' => 'September',
            'October' => 'October',
            'November' => 'November',
            'December' => 'December'
        ];

        return $months;
    }

    /**
     * getUserType
     * 
     * @return string
     */
    public static function getUserType()
    {
        $usertype = [
            '1' => 'To validate and earn',
            '2' => 'To innovate and earn',
            '3' => 'To explore and develop'
        ];

        return $usertype;
    }
    
    /**
     * Creating random password for user
     * 
     * @return String
     */
    public static function randomPassword()
    {
        $len    = config('inv_common.PWD_LENGTH');
        $sets   = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '0123456789';
        //$sets[] = '!@#$&*?';

        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }
        while (strlen($password) < $len) {

            $randomSet = $sets[array_rand($sets)];
            $password  .= $randomSet[array_rand(str_split($randomSet))];
        }

        return str_shuffle($password);
    }



     /**
     * Creating random password for user
     *
     * @return String
     */
    public static function randomOTP()
    {
        $len    = config('inv_common.OTP_LENGTH');
        $sets   = array();
        $sets[] = '123456789';
        //$sets[] = '!@#$&*?';

        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }
        while (strlen($password) < $len) {

            $randomSet = $sets[array_rand($sets)];
            $password  .= $randomSet[array_rand(str_split($randomSet))];
        }

        return str_shuffle($password);
    }

    /**
     * Get Users Detail by User ID
     *
     * @return string
     */
    public static function getUserDetail($user_id)
    {
        return \App\Inv\Repositories\Models\User::getUserDetail((int) $user_id);
    }

    /**
     * Get skills drop down
     *
     * @return type
     */
    public static function getDocumentsDropDown()
    {
        return \App\Inv\Repositories\Models\Master\Documents::getDropDown();
    }

    public static function getUserDocuments()
    {
        return \App\Inv\Repositories\Models\UserReqDoc::getUserDocuments();   
    }

    /**
     * Get country name by id
     *
     * @return type
     */
    public static function getCountryById($countryId)
    {
        return \App\Inv\Repositories\Models\Master\Country::getCountryById($countryId);
    }
    
    /**
     * Get country name by id
     *
     * @return type
     */
    public static function getOtherSkillsbyId($skillid,$userid)
    {
        return \App\Inv\Repositories\Models\OtherSkills::getOtherSkillsbyId($skillid,$userid);
    }

   

    /**
     * Get Social media drop down
     *
     * @return type
     */
    public static function getSocialmediaDropDown()
    {
        return \App\Inv\Repositories\Models\Master\Socialmedia::getDropDown();
    }

   

    /**
     * Get Date Format
     *
     * @param date  $date
     *
     * @param string $format
     *
     * @return string
     */
    public static function getDateByFormat($date, $fromFormat = 'Y-m-d H:i:s',
                                           $format = 'd F Y')
    {
        try {
            return Carbon::createFromFormat($fromFormat, $date)->format($format);
        } catch (InvalidArgumentException $x) {
            echo $x->getMessage();
        }
    }

    /**
     * Get User patent
     * @param type $userId
     * @return type
     */
    public static function getRecommendedUser($userId)
    {
        return \App\Inv\Repositories\Models\User::getRecommendedUser($userId);
    }

    /**
     * Get No of User patent
     * @param type $userId
     * @return type
     */
    public static function getRecommendedNoofUser()
    {
        return \App\Inv\Repositories\Models\User::getRecommendedNoofUser();
    }

    /**
     * Get User patent
     * @param type $userId
     * @return type
     */
    public static function getRightsUser()
    {
        return \App\Inv\Repositories\Models\Rights::getRightsUser();
    }

    /**
     * Get type drop down
     *
     * @return type
     */
    public static function getRightTypeList()
    {
        return \App\Inv\Repositories\Models\Master\RightType::getRightTypeList();
    }

    /**
     * Get skills list
     *
     * @return type
     */
    public static function getSkillsList()
    {
        return \App\Inv\Repositories\Models\Master\Skills::getSkillsList();
    }
    
    /**
     * Get skills name
     *
     * @return type
     */
    public static function getSkillName($id)
    {
        return \App\Inv\Repositories\Models\Master\Skills::getSkillName($id);
    }
    
    /**
     * Get skills name
     *
     * @return type
     */
    public static function getSkillOtherName($id)
    {
        return \App\Inv\Repositories\Models\OtherSkills::getSkillOtherName($id);
    }
    
    /**
     * Get Right Type name
     *
     * @return type
     */
    public static function getRightTypeById($id)
    {
        return \App\Inv\Repositories\Models\Master\RightType::getRightTypeById($id);
    }

     /**
     * Get valid novlty list
     *
     * @return object|array
     */
    public static function getValidNovltyCheckList($column_name , $type)
    {
        return \App\Inv\Repositories\Models\Master\ValidChecklist::getValidNovltyCheckList($column_name , $type);
    }
    
    
    
     /**
     * Get type wise list
     *
     * @return object|array
     */
    public static function getTypeWiseList($type)
    {
        return \App\Inv\Repositories\Models\Master\ValidChecklist::getTypeWiseList($type);
    }


    /**
     * Get connection by id
     *
     * @return type
     */
    public static function getConnectionData($toUserId, $fromUserId)
    {
        return \App\Inv\Repositories\Models\Relationship::getConnectionData($toUserId, $fromUserId);
    }

    /**
     * Get Connection Detail by User ID
     *
     * @return string
     */
    public static function getConnectionByID($user_id)
    {
        return \App\Inv\Repositories\Models\Relationship::getConnectionByID((int) $user_id);
    }
    
    /**
     * Get Connection Detail by User ID
     *
     * @return string
     */
    public static function getConnectionByIDNotSeen($user_id)
    {
        return \App\Inv\Repositories\Models\Relationship::getConnectionByIDNotSeen((int) $user_id);
    }
    
    
    /**
     * Create log for api
     *
     * @return string
     */
    public static function saveApiLog($arrData)
    {
        return \App\Inv\Repositories\Models\ApiLog::saveApiLog($arrData);
    }
    /**
     * Get Connection Detail by User ID
     *
     * @return string
     */
    public static function getRightPricebyId($rightId)
    {
        return \App\Inv\Repositories\Models\Rights::getRightPricebyId((int) $rightId);
    }
    

    /**
     * Get Put stack statement
     *
     * @param Array
     *
     */
    public static function getStackByID($userId, $rightId)
    {
        return \App\Inv\Repositories\Models\PutStack::getStackByID($userId, $rightId);

    }

     public static function getBounty($rightId)
    {
        return \App\Inv\Repositories\Models\RightCommission::getBounty($rightId);

    }

    public static function getRightTransactionHistory($rightId)
    {
        return \App\Inv\Repositories\Models\RightTransactionHistory::getRightTransactionHistory($rightId);

    }

    public static function ifscountPositive($valid_scout, $invalid_scout)
    {
        $result = ($valid_scout-$invalid_scout);
        
        if($invalid_scout == 0 && $valid_scout > 0) {
            $returnresult = true;
        } else {
            $returnresult = false;
        }
        return $returnresult;

    }


    public static function getPurchaseByUserID($userId)
    {
        return \App\Inv\Repositories\Models\RightPurchase::getPurchaseByUserID($userId);

    }
    
    public static function getsoldRightByUserID($userId)
    {
        return \App\Inv\Repositories\Models\RightPurchase::getsoldRightByUserID($userId);

    }


    
    public static function getRightByRightID($rightId)
    {
        return \App\Inv\Repositories\Models\Rights::getRightByRightID($rightId);

    }

    public static function getIncentiveByUserID($userId)
    {
        return \App\Inv\Repositories\Models\RightCommissionScout::getIncentiveByUserID($userId);

    }
    
     public static function getIncentiveRightByUserID($userId)
    {
        return \App\Inv\Repositories\Models\RightCommissionScout::getIncentiveRightByUserID($userId);

    }

    public static function getsoldRightAmountByUserID($userId)
    {
        return \App\Inv\Repositories\Models\RightCommission::getsoldRightAmountByUserID($userId);

    }
    public static function getKycDetails($userId)
    {
        return \App\Inv\Repositories\Models\Userkyc::getKycDetails($userId);

    }


    public static function checkRightsoldOrNot($rightId)
    {
        return \App\Inv\Repositories\Models\RightPurchase::checkRightsoldOrNot($rightId);

    }
    
    public static function getRightScoutHistory($rightId, $rightAddress)
    {
        return \App\Inv\Repositories\Models\Rights::getRightScoutHistory($rightId, $rightAddress);

    }


    public static function getRightBuyHistory($rightId, $rightAddress)
    {
        return \App\Inv\Repositories\Models\Rights::getRightBuyHistory($rightId, $rightAddress);

    }
    
     


    public static function getRightScoutSudoname($userpublickey)
    {
        return \App\Inv\Repositories\Models\User::getRightScoutSudoname($userpublickey);

    }
    
    public static function getPaypalDetails(){
        
        $paypal_conf = \Config::get('paypal');
        $data['client_id']=$paypal_conf['client_id'];
        $data['secret']= $paypal_conf['secret'];
        $data['auth_token_url']= $paypal_conf['auth_token_url'];
       
        return $data;
    }


    public  static function getCorpDocument($userId,$docid)
    {
      return \App\Inv\Repositories\Models\Document::getTotalDoc($userId,$docid);   
    }


    public  static function getDocumentList($user_id,$user_req_doc_id)
    {
        $userkyc=\App\Inv\Repositories\Models\Userkyc::where('user_id',Auth()->user()->user_id)->first();

        $kycid=$userkyc->kyc_id;

     /* $userKycid=\App\Inv\Repositories\Models\Document::getDocumentList($kycid,$user_req_doc_id); */  

      return \App\Inv\Repositories\Models\Document::getDocumentList($kycid,$user_req_doc_id);   
    }

    public static function getCorpStatus(){

     return \App\Inv\Repositories\Models\CorpStatus::all(); 
    }

}