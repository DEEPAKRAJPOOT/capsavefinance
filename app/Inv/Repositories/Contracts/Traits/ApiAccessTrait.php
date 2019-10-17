<?php

namespace App\Inv\Repositories\Contracts\Traits;

use File;
use Auth;
use Helpers;
use Response;
use Exception;
use Illuminate\Http\Request;
trait ApiAccessTrait
{

    /**
     * Tranjection  files images
    *
     * @return Response
     * @auther Anand
     */
    public function onApprovedScout($email_id, $type)
    {
        try {
            
            $data_array =  array(
                    'uniqueId' => $email_id,
                    'type'     =>  $type,
                    'status'   => 1
                );
          
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('inv_common.API_URL_ON_SCOUT_APPROVED'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //save log
                 
                $apiLog['request'] = json_encode($data_array);
                $apiLog['responce'] = $result;
                $apiLog['source'] = 'onTime Enrolment User';
               \Helpers::saveApiLog($apiLog);
                
                
                
                return   $result; 
             
                
        } catch (Exception $ex) {
           dd($ex);
        }
    }
    
    
     /**
     * Tranjection  files images
     *
     * @return Response
     * @auther Anand
     */
    public function onAddRights($name,$path, $post)
    {
        
        try {
            $filesArr = [];
            $postArr =[];
                array_push($filesArr, $path.'/'.$name); 
                $files = $filesArr;
                // Create array of files to post
                foreach ($files as $index => $file) {
                    $docArr['doc[' . $index . ']'] = curl_file_create(
                            storage_path('app/' . $file)
                    );
                }
                
               $postArr =  array_merge($post,$docArr);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('inv_common.API_URL_ON_ADD_NEW_RIGHT'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postArr);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
                $result = curl_exec($ch);
                //print_r(curl_error($ch));
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //dd($name,$path, $post,$result,curl_error($ch), $httpcode );
                 return ($result);
                curl_close($ch);
            } catch (Exception $ex) {
                dd($ex);
            }
    }
    
    
    
    
    
     /**
     * On update user type on right
     *
     * @return Response
     * @auther Anand
     */
    public function onUpdateUser($data_array)
    {
        try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('inv_common.API_UPDATE_USER_TYPE'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //save log

                $apiLog['request'] = json_encode($data_array);
                $apiLog['responce'] = $result;
                $apiLog['source'] = 'onTime make scout User';
                \Helpers::saveApiLog($apiLog);
                
               return ($httpcode);
             
                
        } catch (Exception $ex) {
                dd($ex);
            }
    }
    
    
    /**
     * Get Similar Record from WCI
     *
     * @return Response
     * 
     */
    public function getSimilarRecord($data_array)
    {
        try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('inv_common.API_UPDATE_USER_TYPE'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //save log

                $apiLog['request'] = json_encode($data_array);
                $apiLog['responce'] = $result;
                $apiLog['source'] = 'onTime make scout User';
                \Helpers::saveApiLog($apiLog);

               return ($httpcode);


        } catch (Exception $ex) {
                dd($ex);
            }
    }
}