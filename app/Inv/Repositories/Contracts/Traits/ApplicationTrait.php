<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use App\Inv\Repositories\Models\Cam;
use App\Inv\Repositories\Models\Master\Equipment;

trait ApplicationTrait
{
    /**
     * Get Application Required documents
     * 
     * @param array $prgmDocsWhere
     * @return mixed
     */
    protected function getProgramDocs($prgmDocsWhere, $appProductIds)
    {
        $array =[];
        $finalDocs =[];
        if ($prgmDocsWhere['stage_code'] == 'doc_upload') {
            $prgmDocs = $this->appRepo->getRequiredDocs(['doc_type_id' => 1], $appProductIds);
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
        $appProductIds = [];
        $appDocProduct = [];
        $appDocs = [];
        $prgmDocsWhere['app_id'] = $app_id;

        $appProducts = $this->appRepo->getAppProducts($app_id);
        foreach($appProducts->products as $product){
            array_push($appProductIds, $product->pivot->product_id);
        }

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

        $data = [
            'product_id' => $programLimitData->product_id,
            'biz_entity_name' => $businessData->biz_entity_name,
            'security_deposit_of' => $security_deposit_of,
            'appId' => $appId,
            'bizId' => $bizId,
            'offerId' => $offerData->prgm_offer_id,
            'offerData' => $offerData,
            'equipmentData' =>$equipmentData,
            'ptpqrData' => $ptpqrData,
            'businessAddress' => $businessAddress
        ];
        
        $data['contact_person'] = ($cam)?$cam->contact_person:'';
        $data['sanction_id'] = ($sanctionData)?$sanctionData->sanction_id:'';
        $data['validity_date'] = ($sanctionData)?$sanctionData->validity_date:'';
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

        return $data;
    }
}
