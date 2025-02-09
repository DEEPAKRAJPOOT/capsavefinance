<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Inv\Repositories\Models\AppSecurityDoc;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\Master\Equipment;

trait ApplicationTrait
{
    /**
     * Get Application Required documents
     * 
     * @param array $prgmDocsWhere
     * @return mixed
     */
    protected function getProgramDocs($prgmDocsWhere, $appProductIds=[])
    {
        if (count($appProductIds) == 0) {
            $appProductIds = $this->getAppProductIds($prgmDocsWhere['app_id']);
        }
        $array =[];
        $finalDocs =[];
        if ($prgmDocsWhere['stage_code'] == 'doc_upload') {
            $prgmDocs = $this->appRepo->getRequiredDocs(['doc_type_id' => 1], $appProductIds);
            foreach ($prgmDocs as $key => $value) {
                $finalDocs[$key]['doc_id'] = $value->doc_id;
                $finalDocs[$key]['product_document'] = $this->appRepo->getDocumentProduct($value->doc_id);
            }
        } else if ($prgmDocsWhere['stage_code'] == 'pre_offer') {  
            $prgmDocs = $this->appRepo->getRequiredDocs(['doc_type_id' => 4], $appProductIds);
            foreach ($prgmDocs as $key => $value) {
                $finalDocs[$key]['doc_id'] = $value->doc_id;
                $finalDocs[$key]['product_document'] = $this->appRepo->getDocumentProduct($value->doc_id);
            }                    
        } else {
            $prgmDocs = $this->appRepo->getProgramDocs($prgmDocsWhere)->toArray();
            $prodIds = [];
            foreach($appProductIds as $prodId) {
                if ($prodId != 1) {
                    $prodIds[] = $prodId;
                }
            }
            
            if($prgmDocsWhere['stage_code'] == 'upload_pre_sanction_doc'){
                $whereCondition['doc_type_id'] =  2;                
                $preDocs = $this->appRepo->getSTLDocs($whereCondition, $prodIds)->toArray();
            }
            else  {
                $whereCondition['doc_type_id'] =  3;                
                $preDocs = $this->appRepo->getSTLDocs($whereCondition, $prodIds)->toArray();
            }

            $merged = array_merge($prgmDocs, $preDocs);

            foreach ($merged as $key => $value) {
                $array[] = $value['doc_id'];
            }
            $uniqueArray = array_unique($array);

            foreach ($uniqueArray as $key => $value) {
                $finalDocs[$key]['doc_id'] = $value;
                $finalDocs[$key]['product_document'] = $this->appRepo->getDocumentProduct($value);
            }

        }
        return $finalDocs ;
    }
    
    protected function getAppProductIds($app_id, $prgmDocsWhere=[])
    {
        $ProductIds = [];
        if (isset($prgmDocsWhere['stage_code']) && $prgmDocsWhere['stage_code'] == 'doc_upload') {
            $appProducts = $this->appRepo->getApplicationProduct($app_id);
            foreach($appProducts->products as $product){
                array_push($ProductIds, $product->id);
            }
        } else if (isset($prgmDocsWhere['stage_code']) && $prgmDocsWhere['stage_code'] == 'pre_offer') {
            $appProducts = $this->appRepo->getApplicationProduct($app_id);
            foreach($appProducts->products as $product){
                array_push($ProductIds, $product->pivot->product_id);
            }            
        }        
        else {
            $appProducts = $this->appRepo->getAppProducts($app_id);
            foreach($appProducts as $product){
                array_push($ProductIds, $product->programLimit->product_id);
            }
        }
        // dd($ProductIds);
        
        $appProductIds = array_unique($ProductIds);
        return $appProductIds;       
    }
    
    /**
     * Create Application Required Docs
     * 
     * @param array $prgmDocsWhere
     * @param integer $user_id
     * @param integer $app_id
     * @return mixed
     */
    protected function createAppRequiredDocs($prgmDocsWhere, $user_id, $app_id)
    {   
        //$appProductIds = [];
        $appDocProduct = [];
        $appDocs = [];
        $prgmDocsWhere['app_id'] = $app_id;

        //$appProducts = $this->appRepo->getAppProducts($app_id);
        //foreach($appProducts->products as $product){
        //    array_push($appProductIds, $product->pivot->product_id);
        //}
        
        $appProductIds = $this->getAppProductIds($app_id, $prgmDocsWhere);
        // dd($appProductIds);
        $reqDocs = $this->getProgramDocs($prgmDocsWhere, $appProductIds);
        if($reqDocs && count($reqDocs) == 0) {
            return;
        }
        foreach($reqDocs as $doc) {
            //$appDocCheck = AppDocument::where('app_id', $app_id)->count();
            $appDocCheck = $this->docRepo->isAppDocFound($app_id, $doc['doc_id']);
            
            if(!$appDocCheck){
                $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                $appDocs = [
                    'rcu_status' => 0,
                    'user_id' => $user_id,
                    'app_id' => (int) $app_id,
                    'doc_id' => $doc['doc_id'],
                    'is_upload' => 0,
                    'is_required' => 1,
                    'created_by' => Auth::user()->user_id,
                    'created_at' => $curData,
                    'updated_by' => Auth::user()->user_id,                   
                    'updated_at' => $curData, 
                ];
                $appDocResponce = $this->docRepo->saveAppRequiredDocs($appDocs);
                foreach ($doc['product_document']->product_document as $productDoc) {
                    if(in_array($productDoc->product_id, $appProductIds)) {
                        $appDocProduct = [
                            'app_doc_id' => $appDocResponce->app_doc_id,
                            'product_id' => $productDoc->product_id,
                            'doc_id' => $doc['doc_id']

                        ];
                        $productDoc = $this->docRepo->saveAppDocProduct($appDocProduct);
                    } 
                }
            }
        }
        return $reqDocs;
    }


     protected function getSanctionLetterData($appId, int $bizId, $offerId=null, $sanctionID=null){
        $offerWhereCond = [];
        $appId = (int)$appId;
        if ($offerId) {
            $offerWhereCond['prgm_offer_id'] = $offerId;
        } else {
            $offerWhereCond['app_id'] = $appId;   
            $offerWhereCond['is_active'] = 1; 
        }
       
        $offerData = $this->appRepo->getOfferData($offerWhereCond);

        if(!empty($offerData)){
            $sanctionData = $this->appRepo->getOfferSanction($offerData->prgm_offer_id);
            $businessData = $this->appRepo->getApplicationById($bizId); 
            $businessAddress = $businessData ? $businessData->address->where('address_type','2')->first() : null;
            $cam =  Cam::select('contact_person')->where('biz_id',$bizId)->where('app_id',$appId)->first();
            
            $programLimitData = $this->appRepo->getLimit($offerData->app_prgm_limit_id);
            $ptpqrData =  $this->appRepo->getOfferPTPQR($offerData->prgm_offer_id);
            $equipmentData = null;
            if($offerData->equipment_type_id){
                $equipmentData = Equipment::find($offerData->equipment_type_id);
            }
    
            $security_deposit_of = ''; 
            switch ($offerData->security_deposit_of) {
                case(4): $security_deposit_of = 'Sanction'; break;
                case(3): $security_deposit_of = 'Asset Base Value'; break;
                case(2): $security_deposit_of = 'Asset value'; break;
                case(1): $security_deposit_of = 'Loan Amount'; break;
            }
            $data['contact_person'] = ($cam)?$cam->contact_person:'';
            $data['sanction_id'] = ($sanctionData)?$sanctionData->sanction_id:'';
            $data['validity_date'] = ($sanctionData)?$sanctionData->validity_date:'';
            $data['expire_date'] = ($sanctionData)?$sanctionData->expire_date:'';
            $data['lessor'] = ($sanctionData)?$sanctionData->lessor:'';
            $data['validity_comment'] = ($sanctionData)?$sanctionData->validity_comment:'';
            $data['payment_type'] = ($sanctionData)?$sanctionData->payment_type:'';
            $data['payment_type_other'] = ($sanctionData)?$sanctionData->payment_type_other:'';
            $data['delay_pymt_chrg'] = ($sanctionData)?$sanctionData->delay_pymt_chrg:'';
            $data['insurance'] = ($sanctionData)?$sanctionData->insurance:'';
            $data['bank_chrg'] = ($sanctionData)?$sanctionData->bank_chrg:'';
            $data['legal_cost'] = ($sanctionData)?$sanctionData->legal_cost:'';
            $data['po'] = ($sanctionData)?$sanctionData->po:'';
            $data['pdp'] = ($sanctionData)?$sanctionData->pdp:'';
            $data['disburs_guide'] = ($sanctionData)?$sanctionData->disburs_guide:'';
            $data['other_cond'] = ($sanctionData)?$sanctionData->other_cond:'';
            $data['covenants'] = ($sanctionData)?$sanctionData->covenants:'';
            $data['sanctionData'] = ($sanctionData)?$sanctionData:'';
            $data['product_id'] = $programLimitData->product_id;
            $data['biz_entity_name'] = $businessData ? $businessData->biz_entity_name : null;
            $data['security_deposit_of'] = $security_deposit_of;
            $data['offerId'] = $offerData->prgm_offer_id;
            $data['equipmentData'] = $equipmentData;
            $data['ptpqrData'] = $ptpqrData;
            $data['businessAddress'] = $businessAddress;
            $data['sanction_expire_msg'] = '';
            $currentDate = date("Y-m-d");
            if(empty($data['expire_date'])){
                 $data['expire_date'] = date('Y/m/d', strtotime($currentDate. ' + 30 days'));
            } 
            if(isset($data['expire_date'])){
                if(strtotime($currentDate) > strtotime($data['expire_date'])){
                    $data['sanction_expire_msg'] = "Sanction Letter Expired.";
                }
            }
               
        }
        $data['leasingLimitData'] = $this->appRepo->getProgramLimitData($appId, '3')->toArray();
        $data['leaseOfferData'] = AppProgramOffer::getAllOffers($appId, '3');
        $data['facilityTypeList']= $this->masterRepo->getFacilityTypeList()->toarray();
        $data['arrStaticData']['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
        $data['arrStaticData']['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
        $data['arrStaticData']['securityDepositType'] = array('1'=>'INR','2'=>'%');
        $data['arrStaticData']['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
        $data['arrStaticData']['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');

        $data['offerData'] = $offerData;
        $data['appId'] = $appId;
        $data['bizId'] = $bizId;

        return $data;
    }

    
    protected function getSanctionLetterSupplyChainData($appId, $bizId, $offerId=null, $sanctionID=null){
        $bizData = $this->appRepo->getApplicationById($bizId);
        $EntityData  = $this->appRepo->getEntityByBizId($bizId);
        $CamData  = $this->appRepo->getCamDataByBizAppId($bizId, $appId);
        $AppLimitData  = $this->appRepo->getAppLimit($appId);
        $supplyChainOfferData = $this->appRepo->appOfferWithLimit($appId);
        $reviewerSummaryData = $this->appRepo->getReviewerSummaryData($appId, $bizId);

        $user = $this->appRepo->getAppData($appId)->user;
        //$anchors = $user->anchors;
        $anchors = $this->userRepo->getAnchorsByUserId($user->user_id);
        $anchorArr=[];
        foreach($anchors as $anchor){
          $anchorArr[$anchor->anchor_id]  = $anchor->toArray();
        }

        $ProgramData = $supplyChainOffer = [];
        if ($supplyChainOfferData->count()) {
            $supplyChainOfferData = $supplyChainOfferData[0];
            $supplyChainOffer = array_merge($supplyChainOfferData->programLimit->toArray(),$supplyChainOfferData->toArray());
            $ProgramData = $this->appRepo->getProgramData(['prgm_id' => $supplyChainOffer['prgm_id']]);
        }
        $offerData = $this->appRepo->getAllOffers($appId, 1);
        $tot_limit_amt = 0;
        if (!empty($AppLimitData) && $AppLimitData->count()) {
            $tot_limit_amt = $AppLimitData['tot_limit_amt'];
        }
        $CommunicationAddress = '';
        if (!empty($bizData->address[1])) {
            $AddressData = $bizData->address[1];
            $stateName = "";
            if (!empty($AddressData->state)) {
               $stateName = $AddressData->state->name ?? '';
            }
            $CommunicationAddress = $AddressData->addr_1 . ' '. $AddressData->city_name .' '.  $stateName   .' '. $AddressData->pin_code;
        }
        $bizOwners = BizOwner::getCompanyOwnerByBizId($bizId);
        $bizOwnerData = [];
        if ($bizOwners->count()) {
            foreach ($bizOwners as $key => $bizOwner) {
                $bizOwnerData[$bizOwner['biz_owner_id']]  = $bizOwner->toArray();
            }
        }
        $app_prgm_limit_id = $supplyChainOffer['app_prgm_limit_id'] ?? 0;
        $data['ConcernedPersonName'] = $CamData['operational_person'] ?? NULL ;
        $data['purpose'] = $CamData['t_o_f_purpose'] ?? NULL;
        $data['EntityName'] = $bizData['biz_entity_name'];
        $data['Address'] = $CommunicationAddress;
        $data['EmailId'] = $EntityData['email'];
        $data['MobileNumber'] = $EntityData['mobile_no'];
        $data['limit_amt'] = $supplyChainOffer['limit_amt'] ?? 0;
        $data['product_id'] = $supplyChainOffer['product_id'] ?? 0;
        $data['prgm_type'] = $ProgramData['prgm_type'] ?? 0;
        $data['product_name'] = $ProgramData['product_name'] ?? 0;
        $data['tot_limit_amt'] = $tot_limit_amt;
        $data['offerData'] = $offerData;
        $data['reviewerSummaryData'] = $reviewerSummaryData;
        $data['bizOwnerData'] = $bizOwnerData;
        $data['anchorData'] = $anchorArr;
        return $data;
    }
    
    protected function copyApplication($userId, $appId, $bizId, $appType=null)
    {
        \DB::beginTransaction();

        try {   
            
            $excludeKeys = ['created_at', 'created_by','updated_at', 'updated_by'];
            
            //Get and save Business Data
            $bizData = $this->appRepo->getApplicationById($bizId);                        
            $bizData = $bizData ? $this->arrayExcept($bizData->toArray(), array_merge($excludeKeys, ['biz_id'])) : [];           
            $newBizData = $this->appRepo->createBusiness($bizData);
            $newBizId = $newBizData->biz_id;
            
            //Get and save Biz Address
            /*
            $bizAddressesData  = $this->appRepo->getBizAddresses($newBizId);
            foreach($bizAddressesData as $bizAddressData) {
                $bizAddressArrData = $bizAddressData ? $this->arrayExcept($bizAddressData->toArray(), array_merge($excludeKeys, ['biz_addr_id'])) : [];
                $bizAddressArrData['biz_id'] = $newBizId;
                $this->appRepo->saveAddress($bizAddressArrData);            
            }
            */
            
            
            //Get and save Application data
            $appData  = $this->appRepo->getAppDataByAppId($appId);
            $appData = $appData ? $this->arrayExcept($appData->toArray(), array_merge($excludeKeys, ['app_id', 'curr_status_id', 'curr_status_updated_at'])) : [];                
            $appData['biz_id'] = $newBizId;
            $appData['parent_app_id'] = $appId;
            $appData['status'] = 0;
            $appData['renewal_status'] = 0;
            $appData['app_type'] = $appType;
            $appData['is_old_app'] = 0;
            $newAppData = $this->appRepo->createApplication($appData);
            $newAppId = $newAppData->app_id;
            
            $appCode = \Helpers::formatIdWithPrefix($newAppId, 'APP');
            $this->appRepo->updateAppDetails($newAppId, ['app_code' => $appCode]);  
            \Helpers::updateAppCurrentStatus($newAppId, config('common.mst_status_id.APP_INCOMPLETE'));

            $newBizOwnersArr=[];
            //Get and save Biz Owner with Address Data
            $ownersData  = $this->appRepo->getOwnerByBizId($bizId);
            foreach($ownersData as $ownerData) {
                $bizOwnerId = $ownerData->biz_owner_id;
                $ownerArrData = $ownerData ? $this->arrayExcept($ownerData->toArray(), array_merge($excludeKeys, ['biz_owner_id'])) : [];

                $ownerArrData['biz_id'] = $newBizId;  
                $newOwnerData = $this->appRepo->createBizOwner($ownerArrData);
                $newBizOwnerId = $newOwnerData->biz_owner_id;
                
                $newBizOwnersArr[$bizOwnerId] = $newBizOwnerId;
                                                              
            }
            
            //Save Biz Entity Cin Data
            $whereCond=[];
            $whereCond['biz_id'] = $bizId;  
            $bizEntityCinData  = $this->appRepo->getBizEntityCinData($whereCond);
            foreach($bizEntityCinData as $bizEntityCin) {
                $newBizEntityCinData = $bizEntityCin ? $this->arrayExcept($bizEntityCin->toArray(), array_merge($excludeKeys, ['biz_entity_cin_id'])) : [];
                $newBizEntityCinData['biz_id'] = $newBizId;                
                $newBizEntityCinData['created_at'] = \Carbon\Carbon::now();
                $newBizEntityCinData['created_by'] = \Auth::user()->user_id;  
                $this->appRepo->saveBizEntityCinData($newBizEntityCinData);
            }
            
            //Get Biz Owner Address
            $whereCond=[];
            $whereCond['biz_id'] = $bizId;  
            $ownAddressesData  = $this->appRepo->getBizAddresses($whereCond);
            foreach($ownAddressesData as $ownAddressData) {
                $ownAddressArrData = $ownAddressData ? $this->arrayExcept($ownAddressData->toArray(), array_merge($excludeKeys, ['biz_addr_id'])) : [];
                $ownAddressArrData['biz_id'] = $newBizId;
                $ownAddressArrData['rcu_status'] = 0;
                $ownAddressArrData['biz_owner_id'] = isset($newBizOwnersArr[$ownAddressArrData['biz_owner_id']]) ? $newBizOwnersArr[$ownAddressArrData['biz_owner_id']] : null;
                $this->appRepo->saveAddress($ownAddressArrData);
            } 

            //Get Biz API Data
             $whereCond=[];
             $whereCond['biz_id'] = $bizId;
             //$whereCond['biz_owner_id'] = $bizOwnerId;
             $bizApiData  = $this->appRepo->getBizApiData($whereCond);
             foreach($bizApiData as $apiData) {
                 $bizApiArrData = $apiData ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['biz_api_id'])) : [];
                 $bizApiArrData['biz_id'] = $newBizId;
                 $bizApiArrData['biz_owner_id'] = isset($newBizOwnersArr[$bizApiArrData['biz_owner_id']]) ? $newBizOwnersArr[$bizApiArrData['biz_owner_id']] : null;
                 $this->appRepo->saveBizApiData($bizApiArrData);
             } 

             //Get and save Pan GST Data
             $whereCond=[];
             $whereCond['biz_id'] = $bizId;
             //$whereCond['biz_owner_id'] = $bizOwnerId;
             $bizPanGstData  = $this->appRepo->getBizPanGstData($whereCond);
             foreach($bizPanGstData as $gstData) {
                 $bizPanGstArrData = $gstData ? $this->arrayExcept($gstData->toArray(), array_merge($excludeKeys, ['biz_pan_gst_id'])) : [];
                 $bizPanGstArrData['biz_id'] = $newBizId;
                 $bizPanGstArrData['biz_owner_id'] = isset($newBizOwnersArr[$bizPanGstArrData['biz_owner_id']]) ? $newBizOwnersArr[$bizPanGstArrData['biz_owner_id']] : null;;
                 $this->appRepo->saveBizPanGstData($bizPanGstArrData);
             }

            //Get and save GST Log Data
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $bizGstLogsData = $this->appRepo->getBizGstLogData($whereCond);
            foreach($bizGstLogsData as $gstLog) {
                $bizGstLogArrData = $gstLog ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['id'])) : [];
                $bizGstLogArrData['app_id'] = $newAppId;                
                $this->appRepo->saveBizGstLogData($bizGstLogArrData);
            }
            
            //Get and save Perfios Data
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $bizPerfiosData = $this->appRepo->getBizPerfiosData($whereCond);
            foreach($bizPerfiosData as $perfiosData) {
                $bizPerfiosArrData = $perfiosData ? $this->arrayExcept($perfiosData->toArray(), array_merge($excludeKeys, ['biz_perfios_id'])) : [];
                $bizPerfiosArrData['app_id'] = $newAppId;                
                $this->appRepo->saveBizPerfiosData($bizPerfiosArrData);
            }            
            
                     
            //Get and save application product data            
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appProductData = $this->appRepo->getAppProductData($whereCond);
            foreach($appProductData as $appProdData) {
                $appProductArrData = $appProdData ? $this->arrayExcept($appProdData->toArray(), array_merge($excludeKeys, ['id'])) : [];
                $appProductArrData['app_id'] = $newAppId;                
                $this->appRepo->saveAppProductData($appProductArrData);
            }            
                    
            //Get and save application documents           
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appDocsData = $this->appRepo->getAppDocuments($whereCond);
            foreach($appDocsData as $appDoc) {
                $appDocId = $appDoc->app_doc_id;
                $appDocsArrData = $appDoc ? $this->arrayExcept($appDoc->toArray(), array_merge($excludeKeys, ['app_doc_id'])) : [];
                
                $appDocsArrData['app_id'] = $newAppId;                
                $appDocResult = $this->appRepo->saveAppDocuments($appDocsArrData);
                $newAppDocId = $appDocResult ? $appDocResult->app_doc_id : null;
                
                //Get and save application product document
                $whereCond=[];
                $whereCond['app_doc_id'] = $appDocId;
                $appDocFilesData = $this->appRepo->getAppProductDocs($whereCond);
                foreach($appDocFilesData as $appDocFile) {
                    $appDocFilesArrData = $appDocFile ? $this->arrayExcept($appDocFile->toArray(), array_merge($excludeKeys, ['app_doc_product_id'])) : [];
                    $appDocFilesArrData['app_doc_id'] = $newAppDocId; 
                    $this->appRepo->saveAppProductDocs($appDocFilesArrData);
                }  
            }      
            
            //Get and save application document files         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appDocFilesData = $this->appRepo->getAppDocFiles($whereCond);
            foreach($appDocFilesData as $appDocFile) {
                $appDocFilesArrData = $appDocFile ? $this->arrayExcept($appDocFile->toArray(), array_merge($excludeKeys, ['app_doc_file_id'])) : [];
                $appDocFilesArrData['app_id'] = $newAppId; 
                $appDocFilesArrData['biz_owner_id'] = isset($newBizOwnersArr[$appDocFilesArrData['biz_owner_id']]) ? $newBizOwnersArr[$appDocFilesArrData['biz_owner_id']] : null;
                $this->appRepo->saveAppDocFiles($appDocFilesArrData);
            }  
            
            //rta_user_app_doc
            //Get and save application document files         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appUserDocData = $this->appRepo->getUserAppDocData($whereCond)->where('file_type','<>',2);
            foreach($appUserDocData as $appUserDoc) {
                $appUserDocArrData = $appUserDoc ? $this->arrayExcept($appUserDoc->toArray(), array_merge($excludeKeys, ['app_doc_file_id'])) : [];
                $appUserDocArrData['app_id'] = $newAppId;
                $this->appRepo->saveUserAppDocData($appUserDocArrData);
            }
            
            //rta_app_biz_bank_detail
            //Get and save application business bank detail       
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appBizBankData = $this->appRepo->getAppBizBankDetail($whereCond);
            foreach($appBizBankData as $appBizBank) {
                $appBizBankArrData = $appBizBank ? $this->arrayExcept($appBizBank->toArray(), array_merge($excludeKeys, ['bank_detail_id'])) : [];
                $appBizBankArrData['app_id'] = $newAppId; 
                $this->appRepo->saveAppBizBankDetail($appBizBankArrData);
            }                
            
            
            //app_biz_fin_detail
            //Get and save application business finance detail         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appBizFinData = $this->appRepo->getAppBizFinDetail($whereCond);
            foreach($appBizFinData as $appBizFin) {
                $appBizFinArrData = $appBizFin ? $this->arrayExcept($appBizFin->toArray(), array_merge($excludeKeys, ['fin_detail_id'])) : [];
                $appBizFinArrData['app_id'] = $newAppId; 
                $appBizFinArrData['biz_id'] = $newBizId;
                $this->appRepo->saveAppBizFinDetail($appBizFinArrData);
            }                   
            
            //Get and save cam report data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camReportData = $this->appRepo->getCamReportData($whereCond);
            foreach($camReportData as $camReport) {
                $camReportArrData = $camReport ? $this->arrayExcept($camReport->toArray(), array_merge($excludeKeys, ['cam_report_id','contact_person','operational_person','program','rating_no','rating_comment','existing_exposure','proposed_exposure','sanction_limit_cam','outstanding_exposure_cam','group_company','total_exposure','t_o_f_limit','t_o_f_purpose','t_o_f_takeout','t_o_f_recourse','t_o_f_security_check','t_o_f_security','t_o_f_adhoc_limit','t_o_f_covenants','risk_comments','cm_comment','promoter_cmnt'])) : [];
                $camReportArrData['app_id'] = $newAppId; 
                $camReportArrData['biz_id'] = $newBizId;
                //$this->appRepo->saveAppBizFinDetail($camReportArrData); //Previous call
                $this->appRepo->saveCamReportData($camReportArrData);
            }    
            
            //rta_cam_hygiene
            //Get and save cam hygiene data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camHygieneData = $this->appRepo->getCamHygieneData($whereCond);
            foreach($camHygieneData as $camHygiene) {
                $camHygieneArrData = $camHygiene ? $this->arrayExcept($camHygiene->toArray(), array_merge($excludeKeys, ['cam_report_id'])) : [];
                $camHygieneArrData['app_id'] = $newAppId; 
                $camHygieneArrData['biz_id'] = $newBizId;
                $this->appRepo->saveCamHygieneData($camHygieneArrData);
            }             
            
            //rta_cam_reviewer_summary
            //Get and save cam reviewer summary data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camReviewerData = $this->appRepo->getCamReviewerSummaryData($whereCond);
            foreach($camReviewerData as $camReviewer) {
                $camReviewerSummaryId = $camReviewer->cam_reviewer_summary_id;
                $camReviewerArrData = $camReviewer ? $this->arrayExcept($camReviewer->toArray(), array_merge($excludeKeys, ['cam_reviewer_summary_id'])) : [];
                $camReviewerArrData['app_id'] = $newAppId; 
                $camReviewerArrData['biz_id'] = $newBizId;
                $newCamReviewer = $this->appRepo->saveCamReviewerSummaryData($camReviewerArrData);
                $newCamReviewerSummaryId = $newCamReviewer->cam_reviewer_summary_id;
                        
                //rta_cam_reviewer_risk_cmnt
                //Get and save cam reviewer risk cmnt data         
                $whereCond=[];
                $whereCond['cam_reviewer_summary_id'] = $camReviewerSummaryId;
                $camReviewerRiskData = $this->appRepo->getCamReviewerRiskData($whereCond);
                foreach($camReviewerRiskData as $camReviewerRisk) {
                    $camReviewerRiskArrData = $camReviewerRisk ? $this->arrayExcept($camReviewerRisk->toArray(), array_merge($excludeKeys, ['risk_cmnt_id'])) : [];
                    $camReviewerRiskArrData['cam_reviewer_summary_id'] = $newCamReviewerSummaryId;                     
                    $this->appRepo->saveCamReviewerRiskData($camReviewerRiskArrData);
                }   
                
                //rta_cam_reviewer_prepost_cond //OLD table for Pre/Post Data not used below table is used
                //Get and save cam reviewer prepost cond data         
                // $whereCond=[];
                // $whereCond['cam_reviewer_summary_id'] = $camReviewerSummaryId;
                // $camReviewerPrePostData = $this->appRepo->getCamReviewerPrePostData($whereCond);
                // foreach($camReviewerPrePostData as $camReviewerPrePost) {
                //     $camReviewerPrePostArrData = $camReviewerPrePost ? $this->arrayExcept($camReviewerPrePost->toArray(), array_merge($excludeKeys, ['prepost_cond_id'])) : [];
                //     $camReviewerPrePostArrData['cam_reviewer_summary_id'] = $newCamReviewerSummaryId;                     
                //     $this->appRepo->saveCamReviewerPrePostData($camReviewerPrePostArrData);
                // }

                //rta_app_security_doc
                //Get and save cam reviewer prepost cond data new functionality     
                $appSecurtiyDocs = AppSecurityDoc::where(['cam_reviewer_summary_id' => $camReviewerSummaryId,'is_active' => 1])->whereIn('status', [2])->whereIn('is_non_editable', [1])->get();
                if (!empty($appSecurtiyDocs)) {
                    foreach ($appSecurtiyDocs as $clone) {
                        $cloneAppSecurityData = $clone->replicate();
                        $cloneAppSecurityData->cam_reviewer_summary_id = $newCamReviewerSummaryId;
                        $cloneAppSecurityData->app_id = $newAppId; 
                        $cloneAppSecurityData->biz_id = $newBizId;
                        $cloneAppSecurityData->prgm_offer_id = NULL;
                        $cloneAppSecurityData->is_non_editable = 0;
                        $cloneAppSecurityData->status = 1;
                        //$cloneAppSecurityData->created_at = NULL;
                        $cloneAppSecurityData->updated_at = NULL;
                        $cloneAppSecurityData->created_by = NULL;
                        $cloneAppSecurityData->updated_by = NULL;
                        $cloneAppSecurityData->save();
                    }
                }
            }  
            
            $wfStageArr = [1, 2, 5, 10];
            foreach($wfStageArr as $wfStageId) {
                $wfData=[];
                $wfData['biz_app_id'] = $newAppId;
                $wfData['user_id'] = $userId;
                $wfData['wf_stage_id'] = $wfStageId;
                $stats = $wfStageId == 10 ? 0 : 1;
                $wfData['app_wf_status'] = $stats;
                $wfData['is_complete'] = $stats;
                $this->appRepo->saveWfDetail($wfData);
            }
            
            
            $userData = $this->userRepo->getfullUserDetail($userId);
            if ($userData && !empty($userData->anchor_id)) {
                $toUserId = $this->userRepo->getLeadSalesManager($userId);
            } else {
                $toUserId = $this->userRepo->getAssignedSalesManager($userId);
            }
            
            
            //$roles = $this->appRepo->getBackStageUsers($appId, [4]);  //Assigned Sales Manager
            //$toUserId = isset($roles[0]) ? $roles[0]->user_id : null;
                                        
            if ($toUserId) {
               \Helpers::assignAppToUser($toUserId, $newAppId);
            }            
            
            
            //\DB::rollback(); dd($ownerData);

            \DB::commit();
            
            $result = [];
            $result['new_app_id'] = $newAppId;
            $result['new_biz_id'] = $newBizId;
            $result['new_user_id'] = $userId;
            
            return $result;
            // all good
        } catch (\Exception $e) {
            \DB::rollback();
            // something went wrong
            //dd($e->getFile(), $e->getLine(), $e->getMessage());       
            return [];
        }
    }
    
    protected function copyApplicationUcic($userId, $appId, $bizId, $appType=null, $ucicId, $isNotParent=true, $newuser_id)
    {
        \DB::beginTransaction();
        
        try {  
            $ucicDetails = UcicUser::find($ucicId);
            $managementInfo = json_decode($ucicDetails->management_info,true) ?? [];
            $businessInfo = json_decode($ucicDetails->business_info,true) ?? [];
    
            $excludeKeys = ['created_at', 'created_by','updated_at', 'updated_by'];
            
            //Get and save Business Data
            $bizData = $this->appRepo->getApplicationById($bizId);                      
            $bizData = $bizData ? $this->arrayExcept($bizData->toArray(), array_merge($excludeKeys, ['biz_id'])) : [];           
            $newBizData = $this->appRepo->createBusiness($bizData);


            if ($isNotParent) {
                $userId = $userId;
            } else {
                $userId = $newuser_id;
            }
            
            $newBizData->update([
                'user_id' => $userId,
                'biz_entity_name' => $businessInfo['business_info']['entity_name'],
                'date_of_in_corp'=>Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['incorporation_date'])->format('Y-m-d'),
                'entity_type_id'=>$businessInfo['business_info']['sub_industry'],
                'nature_of_biz'=>$businessInfo['business_info']['industry'],
                'turnover_amt'=>($businessInfo['business_info']['business_turnover'])? str_replace(',', '', $businessInfo['business_info']['business_turnover']): 0,
                'biz_constitution'=>$businessInfo['business_info']['biz_constitution'],
                'biz_segment'=>$businessInfo['business_info']['segment'],
                'share_holding_date'=> isset($businessInfo['business_info']['share_holding_per']) ? Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['share_holding_per'])->format('Y-m-d') : null,
                'busi_pan_comm_date'=> isset($businessInfo['business_info']['commencement_date']) ? Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['commencement_date'])->format('Y-m-d') : null,
                'msme_type' => $businessInfo['business_info']['msme_type'],
                'msme_no' => $businessInfo['business_info']['msme_no'],
                'label_1' => $businessInfo['business_info']['label']['1'] ?? '',
                'label_2' => $businessInfo['business_info']['label']['2'] ?? '',
                'label_3' => $businessInfo['business_info']['label']['3'] ?? '',
                'email' => $businessInfo['business_info']['email'] ?? '',
                'mobile' => $businessInfo['business_info']['mobile'] ?? '',
            ]);

            $newBizId = $newBizData->biz_id;
            
            //Get and save Application data
            $appData  = $this->appRepo->getAppDataByAppId($appId);
            $appData = $appData ? $this->arrayExcept($appData->toArray(), array_merge($excludeKeys, ['app_id', 'curr_status_id', 'curr_status_updated_at'])) : [];                
            $appData['biz_id'] = $newBizId;
            if ($isNotParent) {
                $appData['parent_app_id'] = $appId;
            } else {
                $appData['user_id'] = $newuser_id;
                $appData['parent_app_id'] = NULL;
            }

            $appData['status'] = 0;
            $appData['renewal_status'] = 0;
            $appData['app_type'] = $appType;
            $appData['is_old_app'] = 0;
            $newAppData = $this->appRepo->createApplication($appData);
            $newAppId = $newAppData->app_id;

            UcicUserUcic::create([
            'ucic_id' => $ucicId,
            'user_id' => $userId, 
            'app_id' => $newAppId,
            'group_id' => $ucicDetails->group_id,
            ]);

            $appCode = \Helpers::formatIdWithPrefix($newAppId, 'APP');
            $this->appRepo->updateAppDetails($newAppId, ['app_code' => $appCode]);  
            \Helpers::updateAppCurrentStatus($newAppId, config('common.mst_status_id.APP_INCOMPLETE'));

            //Save Biz Entity Cin Data
            $whereCond=[];
            $whereCond['biz_id'] = $bizId;  
            $bizEntityCinData  = $this->appRepo->getBizEntityCinData($whereCond);
            foreach($bizEntityCinData as $bizEntityCin) {
                $newBizEntityCinData = $bizEntityCin ? $this->arrayExcept($bizEntityCin->toArray(), array_merge($excludeKeys, ['biz_entity_cin_id'])) : [];
                $newBizEntityCinData['biz_id'] = $newBizId;                
                $newBizEntityCinData['created_at'] = \Carbon\Carbon::now();
                $newBizEntityCinData['created_by'] = \Auth::user()->user_id;                
                $this->appRepo->saveBizEntityCinData($newBizEntityCinData);
            }
            
            //Get Biz Owner Address
            $gst_address = array(
                'addr_1' => $businessInfo['gst_address']['address'] ?? '',
                'city_name' => $businessInfo['gst_address']['city'] ?? '',
                'state_id' => $businessInfo['gst_address']['state_id'] ?? '',
                'pin_code' => $businessInfo['gst_address']['pincode'] ?? '',
                'location_id' => $businessInfo['gst_address']['address_label'] ?? '',
                'address_type' => 0,
                'biz_id' => $newBizId,
                'biz_owner_id' => null
            );
            BusinessAddress::create($gst_address);

            $communication = array(
                'addr_1' => $businessInfo['other_address']['communication']['address'] ?? '',
                'city_name' => $businessInfo['other_address']['communication']['city'] ?? '',
                'state_id' => $businessInfo['other_address']['communication']['state_id'] ?? '',
                'pin_code' => $businessInfo['other_address']['communication']['pincode'] ?? '',
                'location_id' => $businessInfo['other_address']['communication']['address_label'] ?? '',
                'address_type' => 1,
                'biz_id' => $newBizId,
                'biz_owner_id' => null
            );
            BusinessAddress::create($communication);

            $gst = array(
                'addr_1' => $businessInfo['other_address']['gst']['address'] ?? '',
                'city_name' => $businessInfo['other_address']['gst']['city'] ?? '',
                'state_id' => $businessInfo['other_address']['gst']['state_id'] ?? '',
                'pin_code' => $businessInfo['other_address']['gst']['pincode'] ?? '',
                'location_id' => $businessInfo['other_address']['gst']['address_label'] ?? '',
                'address_type' => 2,
                'biz_id' => $newBizId,
                'biz_owner_id' => null
            );
            BusinessAddress::create($gst);

            $warehouse = array(
                'addr_1' => $businessInfo['other_address']['warehouse']['address'] ?? '',
                'city_name' => $businessInfo['other_address']['warehouse']['city'] ?? '',
                'state_id' => $businessInfo['other_address']['warehouse']['state_id'] ?? '',
                'pin_code' => $businessInfo['other_address']['warehouse']['pincode'] ?? '',
                'location_id' => $businessInfo['other_address']['warehouse']['address_label'] ?? '',
                'address_type' => 3,
                'biz_id' => $newBizId,
                'biz_owner_id' => null
            );
            BusinessAddress::create($warehouse);

            $factory = array(
                'addr_1' => $businessInfo['other_address']['factory']['address'] ?? '',
                'city_name' => $businessInfo['other_address']['factory']['city'] ?? '',
                'state_id' => $businessInfo['other_address']['factory']['state_id'] ?? '',
                'pin_code' => $businessInfo['other_address']['factory']['pincode'] ?? '',
                'location_id' => $businessInfo['other_address']['factory']['address_label'] ?? '',
                'address_type' => 4,
                'biz_id' => $newBizId,
                'biz_owner_id' => null
            );
            BusinessAddress::create($factory);

            //Get Biz API Data
            $bizApiData  = BizApi::whereNull('biz_owner_id')->where('biz_id',$bizId)->get();
            foreach($bizApiData as $apiData) {
                $bizApiArrData = $apiData ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['biz_api_id'])) : [];
                $bizApiArrData['biz_id'] = $newBizId;
                $this->appRepo->saveBizApiData($bizApiArrData);
            } 

            //Get and save Pan GST Data
            $bizPanGstData  = BizPanGst::whereNull('biz_owner_id')->where('biz_id',$bizId)->get();
            foreach($bizPanGstData as $gstData) {
                $bizPanGstArrData = $gstData ? $this->arrayExcept($gstData->toArray(), array_merge($excludeKeys, ['biz_pan_gst_id'])) : [];
                $bizPanGstArrData['biz_id'] = $newBizId;
                $this->appRepo->saveBizPanGstData($bizPanGstArrData);
            }

            BizPanGst::where(['biz_id'=>$newBizId, 'type'=>'2', 'parent_pan_gst_id'=>'0'])
            ->update(['pan_gst_hash'=>$businessInfo['business_info']['gst_no']['pan_gst_hash']]);                

            //update for CIN
            BizPanGst::where(['biz_id'=>$newBizId, 'type'=>1, 'parent_pan_gst_id'=>'0', 'biz_owner_id'=>null])
            ->update(['cin'=>(isset($businessInfo['business_info']['cin_no']))? $businessInfo['business_info']['cin_no']: NULL]);
       
            $newBizOwnersArr=[];
            $oldOwnersArr=[];
            if(!empty($managementInfo)){
                $ucicOwnerIds = $managementInfo['management_info']['ownerIds'];
                $ucicOwner = $managementInfo['management_info']['owners'];
    
                $i = 0;
                foreach ($ucicOwnerIds as $key => $ucicOwnerId) {
                    $newOwnerData = BizOwner::create([
                    'user_id' => $userId,
                    'biz_id' => $newBizId, 
                    'first_name' => $ucicOwner[$i]['name'],
                    'is_promoter' => $ucicOwner[$i]['owner_type']??0,
                    'applicant_type' => $ucicOwner[$i]['owner_type']??0,
                    'email' => $ucicOwner[$i]['email']??'',
                    'mobile_no' => $ucicOwner[$i]['mobile_no']??'',
                    'date_of_birth' => $ucicOwner[$i]['dob'],
                    'gender' => $ucicOwner[$i]['gender'],
                    'owner_addr' => $ucicOwner[$i]['address'] ?? '',
                    'comment' => $ucicOwner[$i]['comment'],
                    'other_ownership' => $ucicOwner[$i]['other_ownership'],
                    'networth' => $ucicOwner[$i]['networth'],
                    'share_per' => $ucicOwner[$i]['shareholding'],
                    'designation' => $ucicOwner[$i]['designation'],
                    'home_no' => $ucicOwner[$i]['home_no'] ?? null,
                    'biz_pan_gst_id' => $ucicOwner[$i]['biz_pan_gst_id'] ?? null,
                    'is_pan_verified' => $ucicOwner[$i]['is_pan_verified'] ?? null,
                    'edu_qualification' => $ucicOwner[$i]['edu_qualification'] ?? null,
                    'cibil_score' => $ucicOwner[$i]['cibil_score'] ?? null,
                    'is_cibil_pulled' => $ucicOwner[$i]['is_cibil_pulled'] ?? null,
                    'ckyc_ref_no' => $ucicOwner[$i]['ckyc_ref_no'] ?? null,
                    'pan_number' => $ucicOwner[$i]['pan_no'] ?? null,
                    'mobile' => $ucicOwner[$i]['mobile'] ?? null,
                    'pan_card' => $ucicOwner[$i]['verify_pan'] ?? null,
                    'driving_license' => $ucicOwner[$i]['verify_dl'] ?? null,
                    'voter_id' => $ucicOwner[$i]['verify_voter'] ?? null,
                    'passport' => $ucicOwner[$i]['verify_passport'] ?? null,
                    
                    'created_by' =>  Auth::user()->user_id]);
                    
                    $newBizOwnerId = $newOwnerData->biz_owner_id;

                    if($ucicOwner[$i]['owner_id']){
                        $oldOwnersArr[$ucicOwner[$i]['owner_id']] = $newBizOwnerId;
                    }else{
                        $newBizOwnersArr[] = $newBizOwnerId;
                    }
                    if($newBizOwnerId){
                        // Add Ownere Address
                        $ownerAddress = array(
                            'addr_1' => $ucicOwner[$i]['address'],
                            'biz_id' => $newBizId,
                            'address_type' => 5,
                            'rcu_status' => 0,
                            'created_by' => Auth::user()->user_id,
                            'biz_owner_id' => $newBizOwnerId
                        );
                        BusinessAddress::create($ownerAddress);

                        // Add Ownere Document Files
                        $ownerDocumnets = $managementInfo['document_upload'][$ucicOwner[$i]['owner_id']] ?? [];
                        if(!empty($ownerDocumnets)){
                            foreach ($ownerDocumnets as $ownerDoc) {
                                if(isset($ownerDoc['file']['id']) && is_numeric($ownerDoc['file']['id'])){
                                    $this->uploadApplicationDocumentUcic($ownerDoc['file']['id'], $newAppId, $newBizOwnerId, $ownerDoc['doc_id']??'');
                                }     
                                 // Add Owner Document Verification
                                if(isset($ownerDoc['biz_api_id']) && is_numeric($ownerDoc['biz_api_id'])){
                                    $whereCond=[];
                                    $whereCond['biz_api_id'] = $ownerDoc['biz_api_id'];
                                    $bizApiData  = $this->appRepo->getBizApiData($whereCond);
                                    foreach($bizApiData as $apiData) {
                                        $bizApiArrData = $apiData ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['biz_api_id'])) : [];
                                        $bizApiArrData['biz_id'] = $newBizId;
                                        $bizApiArrData['biz_owner_id'] = $newBizOwnerId;
                                        $this->appRepo->saveBizApiData($bizApiArrData);
                                    } 
                                }
                            }
                        }
                    }
                    $i++;
                }
            }
    
            //Get and save GST Log Data
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $bizGstLogsData = $this->appRepo->getBizGstLogData($whereCond);
            foreach($bizGstLogsData as $gstLog) {
                $bizGstLogArrData = $gstLog ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['id'])) : [];
                $bizGstLogArrData['app_id'] = $newAppId;                
                $this->appRepo->saveBizGstLogData($bizGstLogArrData);
            }
            
            //Get and save Perfios Data
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $bizPerfiosData = $this->appRepo->getBizPerfiosData($whereCond);
            foreach($bizPerfiosData as $perfiosData) {
                $bizPerfiosArrData = $perfiosData ? $this->arrayExcept($perfiosData->toArray(), array_merge($excludeKeys, ['biz_perfios_id'])) : [];
                $bizPerfiosArrData['app_id'] = $newAppId;                
                $this->appRepo->saveBizPerfiosData($bizPerfiosArrData);
            }            
                    
            //Get and save application product data  
            if(isset($businessInfo['product_type']['product_type'])){
                foreach ($businessInfo['product_type']['product_type'] as $productId) {
                    $appProductArrData['app_id'] = $newAppId;    
                    $appProductArrData['product_id'] = $productId;
                    $appProductArrData['loan_amount'] = $businessInfo['product_type'][$productId]['loan_amount'];
                    $appProductArrData['tenor_days'] = $businessInfo['product_type'][$productId]['tenor'];            
                    $this->appRepo->saveAppProductData($appProductArrData);
                }
            }        
                    
            //Get and save application documents           
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appDocsData = $this->appRepo->getAppDocuments($whereCond);
            foreach($appDocsData as $appDoc) {
                $appDocId = $appDoc->app_doc_id;
                $appDocsArrData = $appDoc ? $this->arrayExcept($appDoc->toArray(), array_merge($excludeKeys, ['app_doc_id'])) : [];
                
                $appDocsArrData['app_id'] = $newAppId;
                $appDocsArrData['user_id'] = $userId;                
                $appDocResult = $this->appRepo->saveAppDocuments($appDocsArrData);
                $newAppDocId = $appDocResult ? $appDocResult->app_doc_id : null;
                
                //Get and save application product document
                $whereCond=[];
                $whereCond['app_doc_id'] = $appDocId;
                $appDocFilesData = $this->appRepo->getAppProductDocs($whereCond);
                foreach($appDocFilesData as $appDocFile) {
                    $appDocFilesArrData = $appDocFile ? $this->arrayExcept($appDocFile->toArray(), array_merge($excludeKeys, ['app_doc_product_id'])) : [];
                    $appDocFilesArrData['app_doc_id'] = $newAppDocId; 
                    $this->appRepo->saveAppProductDocs($appDocFilesArrData);
                }  
            }      
            
            //Get and save application document files         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appDocFilesData = AppDocumentFile::whereNull('biz_owner_id')->where('app_id',$appId)->get();
            foreach($appDocFilesData as $appDocFile) {
                $appDocFilesArrData = $appDocFile ? $this->arrayExcept($appDocFile->toArray(), array_merge($excludeKeys, ['app_doc_file_id'])) : [];
                $appDocFilesArrData['app_id'] = $newAppId; 
                $appDocFilesArrData['user_id'] = $userId; 
                $this->appRepo->saveAppDocFiles($appDocFilesArrData);
            }  
            
            //rta_user_app_doc
            //Get and save application document files         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appUserDocData = $this->appRepo->getUserAppDocData($whereCond)->where('file_type','<>',2);
            foreach($appUserDocData as $appUserDoc) {
                $appUserDocArrData = $appUserDoc ? $this->arrayExcept($appUserDoc->toArray(), array_merge($excludeKeys, ['app_doc_file_id'])) : [];
                $appUserDocArrData['app_id'] = $newAppId;
                $appUserDocArrData['user_id'] = $userId;
                $this->appRepo->saveUserAppDocData($appUserDocArrData);
            }
            
            //rta_app_biz_bank_detail
            //Get and save application business bank detail       
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appBizBankData = $this->appRepo->getAppBizBankDetail($whereCond);
            foreach($appBizBankData as $appBizBank) {
                $appBizBankArrData = $appBizBank ? $this->arrayExcept($appBizBank->toArray(), array_merge($excludeKeys, ['bank_detail_id'])) : [];
                $appBizBankArrData['app_id'] = $newAppId; 
                $this->appRepo->saveAppBizBankDetail($appBizBankArrData);
            }                
            
            //app_biz_fin_detail
            //Get and save application business finance detail         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $appBizFinData = $this->appRepo->getAppBizFinDetail($whereCond);
            foreach($appBizFinData as $appBizFin) {
                $appBizFinArrData = $appBizFin ? $this->arrayExcept($appBizFin->toArray(), array_merge($excludeKeys, ['fin_detail_id'])) : [];
                $appBizFinArrData['app_id'] = $newAppId; 
                $appBizFinArrData['biz_id'] = $newBizId;
                $this->appRepo->saveAppBizFinDetail($appBizFinArrData);
            }                   
            
            //Get and save cam report data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camReportData = $this->appRepo->getCamReportData($whereCond);
            foreach($camReportData as $camReport) {
                $camReportArrData = $camReport ? $this->arrayExcept($camReport->toArray(), array_merge($excludeKeys, ['cam_report_id','contact_person','operational_person','program','rating_no','rating_comment','existing_exposure','proposed_exposure','sanction_limit_cam','outstanding_exposure_cam','group_company','total_exposure','t_o_f_limit','t_o_f_purpose','t_o_f_takeout','t_o_f_recourse','t_o_f_security_check','t_o_f_security','t_o_f_adhoc_limit','t_o_f_covenants','risk_comments','cm_comment','promoter_cmnt'])) : [];
                $camReportArrData['app_id'] = $newAppId; 
                $camReportArrData['biz_id'] = $newBizId;
                //$this->appRepo->saveAppBizFinDetail($camReportArrData); //Previous call
                $this->appRepo->saveCamReportData($camReportArrData);
            }    
            
            //rta_cam_hygiene
            //Get and save cam hygiene data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camHygieneData = $this->appRepo->getCamHygieneData($whereCond);
            foreach($camHygieneData as $camHygiene) {
                $camHygieneArrData = $camHygiene ? $this->arrayExcept($camHygiene->toArray(), array_merge($excludeKeys, ['cam_report_id'])) : [];
                $camHygieneArrData['app_id'] = $newAppId; 
                $camHygieneArrData['biz_id'] = $newBizId;
                $this->appRepo->saveCamHygieneData($camHygieneArrData);
            }             
            
            //rta_cam_reviewer_summary
            //Get and save cam reviewer summary data         
            $whereCond=[];
            $whereCond['app_id'] = $appId;
            $camReviewerData = $this->appRepo->getCamReviewerSummaryData($whereCond);
            foreach($camReviewerData as $camReviewer) {
                $camReviewerSummaryId = $camReviewer->cam_reviewer_summary_id;
                $camReviewerArrData = $camReviewer ? $this->arrayExcept($camReviewer->toArray(), array_merge($excludeKeys, ['cam_reviewer_summary_id'])) : [];
                $camReviewerArrData['app_id'] = $newAppId; 
                $camReviewerArrData['biz_id'] = $newBizId;
                $newCamReviewer = $this->appRepo->saveCamReviewerSummaryData($camReviewerArrData);
                $newCamReviewerSummaryId = $newCamReviewer->cam_reviewer_summary_id;
                        
                //rta_cam_reviewer_risk_cmnt
                //Get and save cam reviewer risk cmnt data         
                $whereCond=[];
                $whereCond['cam_reviewer_summary_id'] = $camReviewerSummaryId;
                $camReviewerRiskData = $this->appRepo->getCamReviewerRiskData($whereCond);
                foreach($camReviewerRiskData as $camReviewerRisk) {
                    $camReviewerRiskArrData = $camReviewerRisk ? $this->arrayExcept($camReviewerRisk->toArray(), array_merge($excludeKeys, ['risk_cmnt_id'])) : [];
                    $camReviewerRiskArrData['cam_reviewer_summary_id'] = $newCamReviewerSummaryId;                     
                    $this->appRepo->saveCamReviewerRiskData($camReviewerRiskArrData);
                }   
                
                //rta_cam_reviewer_prepost_cond
                //Get and save cam reviewer prepost cond data         
                $whereCond=[];
                $whereCond['cam_reviewer_summary_id'] = $camReviewerSummaryId;
                $camReviewerPrePostData = $this->appRepo->getCamReviewerPrePostData($whereCond);
                foreach($camReviewerPrePostData as $camReviewerPrePost) {
                    $camReviewerPrePostArrData = $camReviewerPrePost ? $this->arrayExcept($camReviewerPrePost->toArray(), array_merge($excludeKeys, ['prepost_cond_id'])) : [];
                    $camReviewerPrePostArrData['cam_reviewer_summary_id'] = $newCamReviewerSummaryId;                     
                    $this->appRepo->saveCamReviewerPrePostData($camReviewerPrePostArrData);
                }                  
            }  
            
            $wfStageArr = [1, 2, 5, 10];
            foreach($wfStageArr as $wfStageId) {
                $wfData=[];
                $wfData['biz_app_id'] = $newAppId;
                $wfData['user_id'] = $userId;
                $wfData['wf_stage_id'] = $wfStageId;
                $stats = $wfStageId == 10 ? 0 : 1;
                $wfData['app_wf_status'] = $stats;
                $wfData['is_complete'] = $stats;
                $this->appRepo->saveWfDetail($wfData);
            }
            
            $userData = $this->userRepo->getfullUserDetail($userId);
            if ($userData && !empty($userData->anchor_id)) {
                $toUserId = $this->userRepo->getLeadSalesManager($userId);
            } else {
                $toUserId = $this->userRepo->getAssignedSalesManager($userId);
            }
            
            //$roles = $this->appRepo->getBackStageUsers($appId, [4]);  //Assigned Sales Manager
            //$toUserId = isset($roles[0]) ? $roles[0]->user_id : null;
                                        
            if ($toUserId) {
               \Helpers::assignAppToUser($toUserId, $newAppId);
            }            
            
            \DB::commit();
            
            $result = [];
            $result['new_app_id'] = $newAppId;
            $result['new_biz_id'] = $newBizId;
            $result['new_user_id'] = $userId;
            
            return $result;
        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            return [];
        }
    }
    
    public function uploadApplicationDocumentUcic($fileId, $appId, $ownerId = null, $docId, $docIdNo = null){
        if(!empty($fileId)){
            $ownerDocCheck = $this->docRepo->appOwnerDocCheck($appId, $docId, $ownerId);
            if(!empty($ownerDocCheck)) {
                $appDocResponse = $this->docRepo->updateAppDocFile($ownerDocCheck, $fileId);
                // $appDocResponse = $this->docRepo->updateAppDocNumberFile($ownerDocCheck, $docIdNo);
            } else {
                $appDocData['app_id'] = $appId;
                $appDocData['doc_id'] = $docId;
                $appDocData['biz_owner_id'] = $ownerId;
                $appDocData['file_id']  = $fileId;
                $appDocData['is_upload'] = 1;
                $appDocData['is_ovd_enabled'] = 1;
                // $appDocData['doc_id_no'] = $docIdNo;
                $appDocResponse = $this->docRepo->saveAppDoc($appDocData);
            }
        }
    }
    protected function arrayExcept($array, $keys)
    {

        foreach($keys as $key){
            if (isset($array[$key])) {
              unset($array[$key]);
            }
        }

        return $array;

    }

    public function getAnchorProgramLimit(int $appId, int $program_id, int $offer_id = null){
        $utilizedLimit = 0;

        $prgm_limit =  $this->application->getProgramBalanceLimit($program_id);
        $prgm_data =  $this->application->getProgram(['prgm_id' => $program_id]);
        $anchor_id = $prgm_data->anchor_id;
        $anchorData = Anchor::getAnchorById($anchor_id);
        # product Type 1=> Supply Chain
        if ($prgm_data->product_id == 1) {
            $totalConsumtionAmt = 0;
            $appData = $this->application->getAppData($appId);
            $anchorUsers = $this->application->getAnchorPrgmUserIdsInArray($prgm_data->anchor_id,$program_id);
            foreach($anchorUsers as $user_id)
            {
                $totalConsumtionAmt += \Helpers::getPrgmBalLimitAmt($user_id, $program_id);
            }

            if($anchorData->is_fungible == 0) {
                $totalBalanceAmt = $prgm_data->anchor_sub_limit - $totalConsumtionAmt;
            } else {
                $totalBalanceAmt = $prgm_data->anchor_sub_limit;
            }

            $appUserConsumtionLimit = \Helpers::getPrgmBalLimitAmt($appData->user_id, $program_id, $appData->app_id);
            $appPrgmLimit = $this->application->getProgramLimitData($appId,1);
            $appUserBalLimit = $appPrgmLimit[0]->limit_amt - $appUserConsumtionLimit;
            
            /** Enhancement || Reduction */
            if ($appData->app_type == 2 || $appData->app_type == 3) {

                /**  Current Offer Consumed Limit */
                if($offer_id){
                    if($appData->app_id){
                        $parentAppConsumAmt = \Helpers::getPrgmBalLimitAmt($appData->user_id, $program_id, $appData->app_id);
                        $totalBalanceAmt += $parentAppConsumAmt;
                    }

                    $currOfferConsumAmt = \Helpers::getPrgmBalLimitAmt($appData->user_id, $program_id, $appData->app_id, $offer_id);
                    $appUserBalLimit += $currOfferConsumAmt;
                } else{
                    if(!$appData->prgmOffer()->count()){
                        $parentAppConsumAmt = \Helpers::getPrgmBalLimitAmt($appData->user_id, $program_id, $appData->parent_app_id, null);
                        $totalBalanceAmt += $parentAppConsumAmt;
                    }
                }
            }else {
                if (in_array($appData->app_type, [0]) && $offer_id) {
                    $currOfferConsumAmt = \Helpers::getPrgmBalLimitAmt($appData->user_id, $program_id, $appData->app_id, $offer_id);
                    $appUserBalLimit += $currOfferConsumAmt;
                }
            }
        }

        if ($prgm_data && $prgm_data->copied_prgm_id) {
            $utilizedLimit = \Helpers::getPrgmBalLimit($prgm_data->copied_prgm_id);
        }
        return ['prgm_limit' => $prgm_limit + $utilizedLimit, 'prgm_data' => $prgm_data, 'prgmBalLimitAmt' => $appUserBalLimit ?? 0, 'anchorBalLimitAmt' => $totalBalanceAmt ?? 0];
    }
    
    protected function getNewSanctionLetterData($appId, int $bizId, $offerId=null, $sanctionID=null){
        $offerWhereCond = [];
        $appId = (int)$appId;
        if ($offerId) {
            $offerWhereCond['prgm_offer_id'] = $offerId;
        } else {
            $offerWhereCond['app_id'] = $appId;   
            $offerWhereCond['is_active'] = 1; 
        }
       
        $offerData = $this->appRepo->getOfferData($offerWhereCond);

        if(!empty($offerData)){
            $sanctionData = $this->appRepo->getOfferNewSanctionLetter(null, $sanctionID);
            $businessData = $this->appRepo->getApplicationById($bizId); 
            $businessAddress = $businessData ? $businessData->address->where('address_type','2')->first() : null;
            $cam =  Cam::select('contact_person')->where('biz_id',$bizId)->where('app_id',$appId)->first();
            
            $programLimitData = $this->appRepo->getLimit($offerData->app_prgm_limit_id);
            $ptpqrData =  $this->appRepo->getOfferPTPQR($offerData->prgm_offer_id);
            $equipmentData = null;
            if($offerData->equipment_type_id){
                $equipmentData = Equipment::find($offerData->equipment_type_id);
            }
    
            $security_deposit_of = ''; 
            switch ($offerData->security_deposit_of) {
                case(4): $security_deposit_of = 'Sanction'; break;
                case(3): $security_deposit_of = 'Asset Base Value'; break;
                case(2): $security_deposit_of = 'Asset value'; break;
                case(1): $security_deposit_of = 'Loan Amount'; break;
            }
            $data['contact_person'] = ($cam)?$cam->contact_person:'';
            $data['sanction_id'] = ($sanctionData)?$sanctionData->sanction_letter_id:'';
            $data['app_id'] = ($sanctionData)?$sanctionData->app_id:'';
            $data['prgm_offer_id'] = ($sanctionData)?$sanctionData->prgm_offer_id:'';
            $data['ref_no'] = ($sanctionData)?$sanctionData->ref_no:'';
            $data['date_of_final_submission'] = ($sanctionData)?$sanctionData->date_of_final_submission:'';
            $data['sanction_content'] = ($sanctionData)?$sanctionData->sanction_content:'';
            $data['status'] = ($sanctionData)?$sanctionData->status:'';
            $data['sanctionData'] = ($sanctionData)?$sanctionData:'';
            $data['product_id'] = $programLimitData->product_id;
            $data['biz_entity_name'] = $businessData ? $businessData->biz_entity_name : null;
            $data['security_deposit_of'] = $security_deposit_of;
            $data['offerId'] = $offerData->prgm_offer_id;
            $data['equipmentData'] = $equipmentData;
            $data['ptpqrData'] = $ptpqrData;
            $data['businessAddress'] = $businessAddress;
            $data['sanction_expire_msg'] = '';
            $currentDate = date("Y-m-d");
            if(empty($data['expire_date'])){
                 $data['expire_date'] = date('Y/m/d', strtotime($currentDate. ' + 30 days'));
            } 
            if(isset($data['expire_date'])){
                if(strtotime($currentDate) > strtotime($data['expire_date'])){
                    $data['sanction_expire_msg'] = "Sanction Letter Expired.";
                }
            }
               
        }
        $data['offerData'] = $offerData;
        $data['appId'] = $appId;
        $data['bizId'] = $bizId;

        return $data;
    }

    
    protected function getNewSanctionLetterSupplyChainData($appId, $bizId, $offerId=null, $sanctionID=null){
        $bizData = $this->appRepo->getApplicationById($bizId);
        $EntityData  = $this->appRepo->getEntityByBizId($bizId);
        $CamData  = $this->appRepo->getCamDataByBizAppId($bizId, $appId);
        $AppLimitData  = $this->appRepo->getAppLimit($appId);
        $supplyChainOfferData = $this->appRepo->appOfferWithLimit($appId);
        $reviewerSummaryData = $this->appRepo->getReviewerSummaryData($appId, $bizId);
        $user = $this->appRepo->getAppData($appId)->user;
        //$anchors = $user->anchors;
        $anchors = $this->userRepo->getAnchorsByUserId($user->user_id);
        $anchorArr=[];
        foreach($anchors as $anchor){
          //$anchorArr[$anchor->anchor_id]  = $anchor->toArray();
          $anchorArr[]  = $anchor->toArray();
        }

        $ProgramData = $supplyChainOffer = [];
        if ($supplyChainOfferData->count()) {
            $supplyChainOfferData = $supplyChainOfferData[0];
            $supplyChainOffer = array_merge($supplyChainOfferData->programLimit->toArray(),$supplyChainOfferData->toArray());
            $ProgramData = $this->appRepo->getProgramData(['prgm_id' => $supplyChainOffer['prgm_id']]);
        }
        $offerData = $this->appRepo->getAllOffers($appId, 1);
        $tot_limit_amt = 0;
        if (!empty($AppLimitData) && $AppLimitData->count()) {
            $tot_limit_amt = $AppLimitData['tot_limit_amt'];
        }
        $CommunicationAddress = '';
        if (!empty($bizData->address[1])) {
            $AddressData = $bizData->address[1];
            $stateName = $city_name = $pin_code = $addr_1 = "";
            if (!empty($AddressData->state)) {
               $stateName = $AddressData->state->name.' - ' ?? '';
            }
            if (!empty($AddressData->city_name)) {
                $city_name = $AddressData->city_name. ', ' ?? '';
             }
             if (!empty($AddressData->pin_code)) {
                $pin_code = $AddressData->pin_code ?? '';
             }
             if (!empty($AddressData->addr_1)) {
                $addr_1 = $AddressData->addr_1. ', ' ?? '';
             }
            $CommunicationAddress = $addr_1 . $city_name . $stateName  . $pin_code;
        }
        $bizOwners = BizOwner::getCompanyOwnerByBizId($bizId);
        $bizOwnerData = [];
        if ($bizOwners->count()) {
            foreach ($bizOwners as $key => $bizOwner) {
                $bizOwnerData[$bizOwner['biz_owner_id']]  = $bizOwner->toArray();
            }
        }
        $offerData = $this->appRepo->getAllOffers($appId, 1);
        $tot_offer_amt = 0;
        if (!empty($offerData) &&  $offerData->count()) {
            foreach ($offerData as $key => $offerDataV) {
                $tot_offer_amt += $offerDataV->prgm_limit_amt;
            }
        }
        $nachPDCData = AppSecurityDoc::with(['mstSecurityDocs'])->where(['app_id'=>$appId,'biz_id'=>$bizId,'is_active'=>1])->whereIn('status',[3,4])->get();
        $isNachPdc = false;
        foreach($nachPDCData as $nachPdcV){
           if(isset($nachPdcV->mstSecurityDocs) && (strtolower($nachPdcV->mstSecurityDocs->name) == 'nach' || strtolower($nachPdcV->mstSecurityDocs->name) == 'pdc')){
               $isNachPdc = true;
           } 
        }
        $supplyChainOffer['limit_amt'] = $tot_offer_amt;
        $app_prgm_limit_id = $supplyChainOffer['app_prgm_limit_id'] ?? 0;
        $data['ConcernedPersonName'] = $CamData['operational_person'] ?? NULL ;
        $data['purpose'] = $CamData['t_o_f_purpose'] ?? NULL;
        $data['EntityName'] = $bizData['biz_entity_name'];
        $data['Address'] = $CommunicationAddress;
        $data['BizConstitution'] = $EntityData['name'];
        $data['EmailId'] = $EntityData['email'];
        $data['MobileNumber'] = $EntityData['mobile_no'];
        $data['limit_amt'] = $supplyChainOffer['limit_amt']?? 0;
        $data['product_id'] = $supplyChainOffer['product_id'] ?? 0;
        $data['prgm_type'] = $ProgramData['prgm_type'] ?? 0;
        $data['product_name'] = $ProgramData['product_name'] ?? 0;
        $data['tot_limit_amt'] = $tot_limit_amt;
        $data['offerData'] = $offerData;
        $data['reviewerSummaryData'] = $reviewerSummaryData;
        $data['bizOwnerData'] = $bizOwnerData;
        $data['anchorData'] = $anchorArr;
        $data['amountInwords'] = numberTowords($data['limit_amt']);
        $data['isNachPdc'] = $isNachPdc;
        return $data;
    }

}
