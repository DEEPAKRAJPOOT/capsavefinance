<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\Master\Equipment;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BizOwner;

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
            if($prgmDocsWhere['stage_code'] == 'upload_pre_sanction_doc'){
                $whereCondition['doc_type_id'] =  2;
                $preDocs = $this->appRepo->getSTLDocs($whereCondition, $appProductIds)->toArray();
            }
            else  {
                $whereCondition['doc_type_id'] =  3;
                $preDocs = $this->appRepo->getSTLDocs($whereCondition, $appProductIds)->toArray();
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


    protected function getSanctionLetterData($appId, $bizId, $offerId=null, $sanctionID=null){
        $offerWhereCond = [];
        
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
            $businessAddress = $businessData->address->where('address_type','2')->first();
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
            $data['biz_entity_name'] = $businessData->biz_entity_name;
            $data['security_deposit_of'] = $security_deposit_of;
            $data['offerId'] = $offerData->prgm_offer_id;
            $data['equipmentData'] = $equipmentData;
            $data['ptpqrData'] = $ptpqrData;
            $data['businessAddress'] = $businessAddress;    
        }
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
        $data['ConcernedPersonName'] = $CamData['operational_person'];
        $data['purpose'] = $CamData['t_o_f_purpose'];
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
        $data['bizOwnerData'] = $bizOwnerData;
        return $data;
    }
}
