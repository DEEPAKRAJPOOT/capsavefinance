<?php

namespace App\Inv\Repositories\Entities\User;

use Carbon\Carbon;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Contracts\UcicUserInterface;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\UcicUserDetail;


class UcicUserRepository extends BaseRepositories implements UcicUserInterface
{
    use CommonRepositoryTraits;

    public function __construct()
    {
        parent::__construct();
    }

	public function create(array $attributes) {   
        return UcicUser::create($attributes);
	}

	public function update(array $attributes, $id) {        
        $result = UcicUser::updateUcic($attributes, (int) $id);
        return $result ? true : false;
    }

    public function getUcicData($where){
       return UcicUser::getUcicData($where);  
    }

    public function getUcicUserApp(){
        return UcicUser::getUcicUserApp(); 
    }

    public function getUcicUserAppCurrentStatus($appId){
        return UcicUser::getUcicUserAppCurrentStatus($appId); 
    }

    public function createUpdateUcic($data){
        $ucicNewData = [];
        $ucicData = [];
        $ucicNewDataucic = [];
        if($data['pan_no']){

            $ucicData = UcicUser::firstOrCreate([
                    'pan_no' => $data['pan_no']
                ],[
                    'pan_no' => $data['pan_no'],
                    'user_id' => $data['user_id'] ?? NULL,
                    'app_id' => $data['app_id'] ?? NULL,
                    'updated_info_src' => 0
                ]);

            if(isset($data['user_id'])){
                $ucicData->user_id = $data['user_id']; 
            }
            if(isset($data['app_id'])){
                $ucicData->app_id = $data['app_id']; 
            }
            if(!$ucicData->ucic_code){
                $ucicData->ucic_code = \Helpers::formatIdWithPrefix($ucicData->user_ucic_id, 'UCIC'); 
            }
            $ucicData->save();

            if($ucicData && isset($data['app_id'])){
                UcicUserUcic::firstOrCreate(
                    [
                    'app_id' => $data['app_id'], 
                    'ucic_id' => $ucicData->user_ucic_id
                ],[
                    'ucic_id' => $ucicData->user_ucic_id,
                    'user_id' => $data['user_id'] ?? NULL,
                    'app_id' => $data['app_id']  ?? NULL,
                    'group_id' => $ucicData->group_id
                    ]       
                );
            }
        }
        return $ucicData;        
    }
    
    public function formatBusinessInfoDb($business_info, $product_ids){
        
        return [
            "business_info" => [
                "company_pan" => [
                    "pan_no" => $business_info->pan->pan_gst_hash,
                    "is_verified" => true
                ],
                "gst_no" => 
                    [
                        "pan_gst_hash" => $business_info->gst->pan_gst_hash, 
                        "is_selected" => false, //useless 
                        "is_gst_manual" => $business_info->is_gst_manual
                    ],
                "entity_name" => $business_info->biz_entity_name,
                "cin_no" => $business_info->cin->cin,
                "segment" => $business_info->biz_segment,
                "industry" => $business_info->nature_of_biz,
                "sub_industry" => $business_info->entity_type_id,
                "biz_constitution" => $business_info->biz_constitution,
                "msme_type" => $business_info->msme_type,
                "msme_no" => $business_info->msme_no,
                "incorporation_date" => \Carbon\Carbon::parse($business_info->date_of_in_corp)->format('d/m/Y'),
                "business_turnover" => ($business_info->turnover_amt),
                "share_holding_per" => \Carbon\Carbon::parse($business_info->share_holding_date)->format('d/m/Y'),
                "commencement_date" => \Carbon\Carbon::parse($business_info->busi_pan_comm_date)->format('d/m/Y'),
                "label" => [
                    '1' => $business_info->label_1,
                    '2' => $business_info->label_2,
                    '3' => $business_info->label_3,
                ],
                "email" => $business_info->email,
                "mobile" => $business_info->mobile,
            ],
            "product_type" => [
                "product_type" =>  array_keys($product_ids),
                "1" => [
                    "loan_amount" => ($product_ids[1]['loan_amount'] ?? 0),
                    "tenor" => ($product_ids[1]['tenor_days'] ?? 0)
                ],
                "2" => [
                    "loan_amount" => ($product_ids[2]['loan_amount'] ?? 0),
                    "tenor" => ($product_ids[2]['tenor_days'] ?? 0)
                ],
                "3" => [
                    "loan_amount" => ($product_ids[3]['loan_amount'] ?? 0),
                    "tenor" => ($product_ids[3]['tenor_days'] ?? 0)
                ]
            ],
            "gst_address" => [
                "address" => $business_info->address[0]->addr_1 ?? NULL,
                "state_id" => $business_info->address[0]->state_id ?? NULL,
                "city" => $business_info->address[0]->city_name ?? NULL,
                "pincode" => $business_info->address[0]->pin_code ?? NULL,
                "address_label" => $business_info->address[0]->location_id ?? NULL
            ],
            "other_address" => [
                "communication" => [
                    "address" => $business_info->address[1]->addr_1 ?? NULL,
                    "state_id" => $business_info->address[1]->state_id ?? NULL,
                    "city" => $business_info->address[1]->city_name ?? NULL,
                    "pincode" => $business_info->address[1]->pin_code ?? NULL,
                    "address_label" => $business_info->address[1]->location_id ?? NULL
                ],
                "gst" => [
                    "address" => $business_info->address[2]->addr_1 ?? NULL,
                    "state_id" => $business_info->address[2]->state_id ?? NULL,
                    "city" => $business_info->address[2]->city_name ?? NULL,
                    "pincode" => $business_info->address[2]->pin_code ?? NULL,
                    "address_label" => $business_info->address[2]->location_id ?? NULL
                ],
                "warehouse" => [
                    "address" => $business_info->address[3]->addr_1 ?? NULL,
                    "state_id" => $business_info->address[3]->state_id ?? NULL,
                    "city" => $business_info->address[3]->city_name ?? NULL,
                    "pincode" => $business_info->address[3]->pin_code ?? NULL,
                    "address_label" => $business_info->address[3]->location_id ?? NULL
                ],
                "factory" => [
                    "address" => $business_info->address[4]->addr_1 ?? NULL,
                    "state_id" => $business_info->address[4]->state_id ?? NULL,
                    "city" => $business_info->address[4]->city_name ?? NULL,
                    "pincode" => $business_info->address[4]->pin_code ?? NULL,
                    "address_label" => $business_info->address[4]->location_id ?? NULL
                ]
            ]
        ];
    }

    public function formatBusinessInfoForm($request){
        $productIds = [];
        foreach ($request['product_id'] as $prodId => $prodVal) {
            if(isset($prodVal['checkbox'])){
                $productIds[] = $prodId;
            }
        }

        return [
            "business_info" => [
                "company_pan" => [
                    "pan_no" => $request['biz_pan_number'],
                    "is_verified" => true
                ],
                "gst_no" => 
                    [
                        "pan_gst_hash" => $request['is_gst_manual'] ? $request['biz_gst_number_text'] : $request['biz_gst_number'], 
                        "is_gst_manual" => $request['is_gst_manual']
                    ],
                "entity_name" => $request['biz_entity_name'],
                "cin_no" => $request['biz_cin'],
                "segment" => $request['segment'],
                "industry" => $request['biz_type_id'],
                "sub_industry" => $request['entity_type_id'],
                "biz_constitution" => $request['biz_constitution'],
                "msme_type" => $request['msme_type'],
                "msme_no" => $request['msme_no'],
                "incorporation_date" => $request['incorporation_date'],
                "business_turnover" => $request['biz_turnover'],
                "share_holding_per" => $request['share_holding_date'],
                "commencement_date" => $request['busi_pan_comm_date'],
                "label" => [
                    '1' => $request['label']['1'],
                    '2' => $request['label']['2'],
                    '3' => $request['label']['3'],
                ],
                "email" => $request['email'],
                "mobile" => $request['mobile'],
            ],
            "product_type" => [
                "product_type" =>  $productIds,
                "1" => [
                    "loan_amount" => (str_replace(',', '', $request['product_id'][1]['loan_amount'] ?? 0)),
                    "tenor" => ($request['product_id'][1]['tenor_days'] ?? 0)
                ],
                "2" => [
                    "loan_amount" => (str_replace(',', '', $request['product_id'][2]['loan_amount'] ?? 0)),
                    "tenor" => ($request['product_id'][2]['tenor_days'] ?? 0)
                ],
                "3" => [
                    
                    "loan_amount" => (str_replace(',', '', $request['product_id'][3]['loan_amount'] ?? 0)),
                    "tenor" => ($request['product_id'][3]['tenor_days'] ?? 0)
                ]
            ],
            "gst_address" => [
                "address" => $request['biz_address'],
                "state_id" => $request['biz_state'],
                "city" => $request['biz_city'],
                "pincode" => $request['biz_pin'],
                "address_label" => $request['location_id']
            ],
            "other_address" => [
                "communication" => [
                    "address" => $request['biz_other_address'][0],
                    "state_id" => $request['biz_other_state'][0],
                    "city" => $request['biz_other_city'][0],
                    "pincode" => $request['biz_other_pin'][0],
                    "address_label" => $request['location_other_id'][0]
                ],
                "gst" => [
                    "address" => $request['biz_other_address'][1],
                    "state_id" => $request['biz_other_state'][1],
                    "city" => $request['biz_other_city'][1],
                    "pincode" => $request['biz_other_pin'][1],
                    "address_label" => $request['location_other_id'][1]
                ],
                "warehouse" => [
                    "address" => $request['biz_other_address'][2],
                    "state_id" => $request['biz_other_state'][2],
                    "city" => $request['biz_other_city'][2],
                    "pincode" => $request['biz_other_pin'][2],
                    "address_label" => $request['location_other_id'][2]
                ],
                "factory" => [
                    "address" => $request['biz_other_address'][3],
                    "state_id" => $request['biz_other_state'][3],
                    "city" => $request['biz_other_city'][3],
                    "pincode" => $request['biz_other_pin'][3],
                    "address_label" => $request['location_other_id'][3]
                ]
            ]
        ];
    }

    public function formatManagementInfoDb($management_info){
        $ownersArrayData = [];    
        $ownersArrayData["management_info"]["is_lease"] = 0;

        foreach($management_info as $key => $ownerData){
            $isPanVerified = $ownerData->businessApi()->where('type', 9)->first() ? true : false;
            $ownersArrayData["management_info"]["ownerIds"][$key] = $ownerData->biz_owner_id;
            $ownersArrayData["management_info"]["owners"][$key] = [
                'owner_id' => $ownerData->biz_owner_id ?? null,
                'name' => $ownerData->first_name ?? null,
                'owner_type' => $ownerData->applicant_type ?? null,
                'is_shareholding' => ($ownerData->applicant_type == 1) ? 1:0  ?? null,
                'shareholding' => $ownerData->share_per ?? null,
                'dob' => $ownerData->date_of_birth ?? null,
                'gender' => $ownerData->gender ?? null,
                'response' => (isset($ownerData->first_name) ? $key+1: 1),
                'designation' => $ownerData->designation ?? null,
                'other_ownership' => $ownerData->other_ownership ?? null,
                'address' => $ownerData->address->addr_1 ?? null,
                'networth' => $ownerData->networth ?? 0,
                'mobile' => $ownerData->mobile ?? null,
                'mobile_no' => $ownerData->mobile_no ?? null,
                'comment' => $ownerData->comment ?? null,
                'verify_dl' => $ownerData->driving_license ?? null,
                'verify_voter' => $ownerData->voter_id ?? null,
                'verify_passport' => $ownerData->passport ?? null,
                'biz_pan_gst_id' => $ownerData->biz_pan_gst_id ?? null,
                'designation' => $ownerData->designation ?? null,
                'pan_no' => $ownerData->pan_number ?? null,
                'verify_pan' => $ownerData->pan_card ?? null,
                'is_pan_verified' => $isPanVerified,
                'edu_qualification' => $ownerData->edu_qualification ?? null,
                'home_no' => $ownerData->home_no ?? null,
                'cibil_score' => $ownerData->cibil_score ?? null,
                'is_cibil_pulled' => $ownerData->is_cibil_pulled ?? null,
                'ckyc_ref_no' => $ownerData->ckyc_ref_no ?? null,
                'email' => $ownerData->email ?? null,
            ];
        }
        return $ownersArrayData;
    }

    public function formatManagementInfoFrom($request){
        $result = [];
        $ownerData = [];
        $is_lease = 0;

        foreach ($request['first_name'] as $key => $value) {
            $networth = str_replace(',', '', $request['networth'][$key]);
            $ownerId = $request['ownerid'][$key] ?? NULL;
            if(!isset($request['ownerid'][$key])){
                $ownerId = 'temp_'.rand(10000,99999);
                $request['ownerid'][] = $ownerId;
            }
            $ownerData[$key] =[
                'owner_id' => $ownerId ?? null,
                'name' => $request['first_name'][$key] ?? null,
                'owner_type' => $request['applicant_type'][$key] ?? null,
                'is_shareholding' => $request['isShareCheck'][$key] ?? null,
                'shareholding' => $request['share_per'][$key] ?? null,
                'dob' => isset($request['date_of_birth'][$key])? Carbon::createFromFormat('d/m/Y', $request['date_of_birth'][$key])->format('Y-m-d'): null,
                'gender' => $request['gender'][$key] ?? null,
                'pan_no' => $request['pan_no'][$key] ?? null,
                'response' => $request['response'][$key] ?? null,
                'designation' => $request['designation'][$key] ?? null,
                'other_ownership' => $request['other_ownership'][$key] ?? null,
                'address' => $request['owner_addr'][$key] ?? null,
                'networth' => is_numeric($networth) ? $networth : 0,
                'mobile' => $request['mobile_no'][$key] ?? null,
                'comment' => $request['comment'][$key] ?? null,
                'verify_pan' => $request['veripan'][$key] ?? null,
                'verify_dl' => $request['verifydl'][$key] ?? null,
                'verify_voter' => $request['verifyvoter'][$key] ?? null,
                'verify_passport' => $request['verifypassport'][$key] ?? null,
                'email' => $request['email'][$key] ?? null,
            ];
        }
        $result['management_info'] = [];
        $result['management_info']['owners'] = $ownerData;
        $result['management_info']['ownerIds'] = $request['ownerid'] ?? [];
        $result['management_info']['is_lease'] = $is_lease;
        return $result;
    }

    public function saveApplicationInfo(int $userUcicId, $businessInfo = [], $managementInfo = [], int $appId, $userId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                $ucicDetails->management_info = json_encode($managementInfo);
                $ucicDetails->business_info = json_encode($businessInfo);
                $ucicDetails->app_id = $appId;
                if($userId){
                    $ucicDetails->user_id = $userId;
                }
                //$ucicDetails->updated_info_src = 1;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }


    public function saveApplicationInfofinal(int $userUcicId, $businessInfo = [], $managementInfo = [], int $appId, $userId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                $ucicDetails->management_info = json_encode($managementInfo);
                $ucicDetails->business_info = json_encode($businessInfo);
                $ucicDetails->app_id = $appId;
                if($userId){
                    $ucicDetails->user_id = $userId;
                }
                $ucicDetails->updated_info_src = 1;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }
    
    public function saveBusinessInfoApp($request = [], $userUcicId = NULL, $appId = NULL, $userId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                if($appId){
                    $businessInfoJson = $request;
                }else{
                    $businessInfoJson = $this->formatBusinessInfoForm($request);
                }
                $ucicDetails->business_info = json_encode($businessInfoJson);
                $ucicDetails->app_id = $appId;
                if($userId){
                    $ucicDetails->user_id = $userId;
                }
                $ucicDetails->updated_info_src = 0;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }

    public function saveBusinessInfoUcic($request = [], $userUcicId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                $businessInfoJson = $this->formatBusinessInfoForm($request);
                $ucicDetails->business_info = json_encode($businessInfoJson);
                $ucicDetails->updated_info_src = 2;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }

    public function saveManagementInfoApp($request, $userUcicId = NULL, $appId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                if($appId){
                    $managementInfo = $request;
                }else{
                    $businessInfoJson = $this->formatManagementInfoFrom($request);
                    $managementInfo = json_decode($ucicDetails->management_info,true);
                    $managementInfo['management_info'] = $businessInfoJson['management_info'];
                }
                
                $ucicDetails->management_info = json_encode($managementInfo);
                $ucicDetails->app_id = $appId;
                $ucicDetails->updated_info_src = 0;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }

    public function saveManagementInfoUcic($request = [], $userUcicId = NULL){
        $result = False;
        if($userUcicId){
            $ucicDetails = UcicUser::find($userUcicId);
            if($ucicDetails){
                $businessInfoJson = $this->formatManagementInfoFrom($request);
                $managementInfo = json_decode($ucicDetails->management_info,true);
                $managementInfo['management_info'] = $businessInfoJson['management_info'];

                $ucicDetails->management_info = json_encode($managementInfo);
                $ucicDetails->updated_info_src = 2;
                $result = $ucicDetails->save();
            }
        }
        return $result;
    }

    public function copyUcicData($userId, $appId, $bizId, $oldAppId, $oldBizId, $appType){
        $ucicDetails = UcicUser::where('user_id',$userId)->first();
        if($ucicDetails){
            
            //Update Buziness Information
            $businessInfo = json_decode($ucicDetails->business_info,true) ?? [];
            if(!empty($businessInfo)){
                $business = Business::find($bizId);

                $business->update([
                    'biz_entity_name' => $businessInfo['business_info']['entity_name'],
                    'date_of_in_corp'=>Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['incorporation_date'])->format('Y-m-d'),
                    'entity_type_id'=>$businessInfo['business_info']['sub_industry'],
                    'nature_of_biz'=>$businessInfo['business_info']['industry'],
                    'turnover_amt'=>($businessInfo['business_info']['business_turnover'])? str_replace(',', '', $businessInfo['business_info']['business_turnover']): 0,
                    'biz_constitution'=>$businessInfo['business_info']['biz_constitution'],
                    'biz_segment'=>$businessInfo['business_info']['segment'],
                    'share_holding_date'=> isset($businessInfo['business_info']['share_holding_per']) ? Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['share_holding_per'])->format('Y-m-d') : null,
                    'busi_pan_comm_date'=> isset($businessInfo['business_info']['commencement_date']) ? Carbon::createFromFormat('d/m/Y', $businessInfo['business_info']['commencement_date'])->format('Y-m-d') : null,
                    'org_id'=>1,
                    'msme_type' => $businessInfo['business_info']['msme_type'],
                    'msme_no' => $businessInfo['business_info']['msme_no'],
                    'email' => $businessInfo['business_info']['email'],
                    'mobile' => $businessInfo['business_info']['mobile'],
                ]);

                if(isset($businessInfo['business_info']['gst_no']['is_gst_manual']) && $businessInfo['business_info']['gst_no']['is_gst_manual'] == '1'){
                    if(isset($businessInfo['business_info']['gst_no']['pan_gst_hash'])){
                        $bizpangst = BizPanGst::where(['biz_id'=>$bizId, 'type'=>'2', 'parent_pan_gst_id'=>'0'])
                        ->update(['pan_gst_hash'=>$businessInfo['business_info']['gst_no']['pan_gst_hash']]);                
                    }            
                }

                //update for CIN
                BizPanGst::where(['biz_id'=>$bizId, 'type'=>1, 'parent_pan_gst_id'=>0, 'biz_owner_id'=>null])
                ->update(['cin'=>(isset($businessInfo['business_info']['cin_no']))? $businessInfo['business_info']['cin_no']: NULL]);

                $productIds = $businessInfo['product_type'];
                unset($productIds['product_type']);
                $products = [];   
                foreach($productIds as $pidKey => $pidval){
                    $products[$pidKey] = [
                        'loan_amount' => $pidval['loan_amount'],   
                        'tenor_days' => $pidval['tenor']
                    ];
                }
                unset($productIds);
                
                $app = Application::where('biz_id',$bizId)->first();
                $app->products()->sync($products);

                $gst_address = array(
                    'addr_1' => $businessInfo['gst_address']['address'],
                    'city_name' => $businessInfo['gst_address']['state_id'],
                    'state_id' => $businessInfo['gst_address']['city'],
                    'pin_code' => $businessInfo['gst_address']['pincode'],
                    'location_id' => $businessInfo['gst_address']['address_label'],
                    'address_type' => 0,
                    'biz_id' => $bizId,
                    'biz_owner_id' => null
                );
                BusinessAddress::create($gst_address);

                $communication = array(
                    'addr_1' => $businessInfo['other_address']['communication']['address'],
                    'city_name' => $businessInfo['other_address']['communication']['state_id'],
                    'state_id' => $businessInfo['other_address']['communication']['city'],
                    'pin_code' => $businessInfo['other_address']['communication']['pincode'],
                    'location_id' => $businessInfo['other_address']['communication']['address_label'],
                    'address_type' => 1,
                    'biz_id' => $bizId,
                    'biz_owner_id' => null
                );
                BusinessAddress::create($communication);

                $gst = array(
                    'addr_1' => $businessInfo['other_address']['gst']['address'],
                    'city_name' => $businessInfo['other_address']['gst']['state_id'],
                    'state_id' => $businessInfo['other_address']['gst']['city'],
                    'pin_code' => $businessInfo['other_address']['gst']['pincode'],
                    'location_id' => $businessInfo['other_address']['gst']['address_label'],
                    'address_type' => 2,
                    'biz_id' => $bizId,
                    'biz_owner_id' => null
                );
                BusinessAddress::create($gst);

                $warehouse = array(
                    'addr_1' => $businessInfo['other_address']['warehouse']['address'],
                    'city_name' => $businessInfo['other_address']['warehouse']['state_id'],
                    'state_id' => $businessInfo['other_address']['warehouse']['city'],
                    'pin_code' => $businessInfo['other_address']['warehouse']['pincode'],
                    'location_id' => $businessInfo['other_address']['warehouse']['address_label'],
                    'address_type' => 3,
                    'biz_id' => $bizId,
                    'biz_owner_id' => null
                );
                BusinessAddress::create($warehouse);

                $factory = array(
                    'addr_1' => $businessInfo['other_address']['factory']['address'],
                    'city_name' => $businessInfo['other_address']['factory']['state_id'],
                    'state_id' => $businessInfo['other_address']['factory']['city'],
                    'pin_code' => $businessInfo['other_address']['factory']['pincode'],
                    'location_id' => $businessInfo['other_address']['factory']['address_label'],
                    'address_type' => 4,
                    'biz_id' => $bizId,
                    'biz_owner_id' => null
                );
                BusinessAddress::create($factory);
            }
            
            //Update Management Information
            $managementInfo = json_decode($ucicDetails->management_info,true) ?? [];
            if(!empty($managementInfo)){
                $appOwner = BizOwner::where('biz_id',$bizId)->get();
                $ucicOwnerIds = $managementInfo['management_info']['ownerIds'];
                $ucicOwner = $managementInfo['management_info']['owners'];

                $appOwnerIds = $appOwner->pluck('biz_owner_id');

                $i = 0;
                foreach ($ucicOwnerIds as $key => $ucicOwnerId) {
                    // $appOwnerId = $appOwnerIds[$key];
                    $owner = BizOwner::find($ucicOwnerId);
                    if($owner){
                        unset($appOwnerIds[$key]);
                        $owner->first_name = $ucicOwner[$i]['name'];                                                                      
                        $owner->is_promoter = $ucicOwner[$i]['owner_type'];  
                        $owner->applicant_type = $appType;  
                        $owner->mobile_no = $ucicOwner[$i]['mobile'];   
                        $owner->date_of_birth = $ucicOwner[$i]['dob'];  
                        $owner->gender = $ucicOwner[$i]['gender'];  
                        $owner->owner_addr = $ucicOwner[$i]['address'];  
                        $owner->comment = $ucicOwner[$i]['comment'];  
                        $owner->other_ownership = $ucicOwner[$i]['other_ownership'];                                                                                                                                                                                                                                                     
                        $owner->networth = $ucicOwner[$i]['networth'];  
                        $owner->share_per = $ucicOwner[$i]['shareholding'];  
                        $owner->designation = $ucicOwner[$i]['designation'];                                   
                        $owner->home_no = $ucicOwner[$i]['home_no'];  
                        $owner->biz_pan_gst_id = $ucicOwner[$i]['biz_pan_gst_id'];  
                        $owner->is_pan_verified = $ucicOwner[$i]['is_pan_verified'];  
                        $owner->edu_qualification = $ucicOwner[$i]['edu_qualification'];  
                        $owner->cibil_score = $ucicOwner[$i]['cibil_score'];  
                        $owner->is_cibil_pulled = $ucicOwner[$i]['is_cibil_pulled'];
                        $owner->ckyc_ref_no = $ucicOwner[$i]['ckyc_ref_no'];  
                        $owner->pan_number = $ucicOwner[$i]['pan_no'];  
                        $owner->mobile = $ucicOwner[$i]['mobile'];      
                        $owner->pan_card = $ucicOwner[$i]['verify_pan'];    
                        $owner->driving_license = $ucicOwner[$i]['verify_dl'];    
                        $owner->voter_id = $ucicOwner[$i]['verify_voter'];  
                        $owner->passport = $ucicOwner[$i]['verify_passport']; 
                        $owner->save();
                        
                    }else{
                        $newOwner = BizOwner::create([
                        'user_id' => $userId,
                        'biz_id' => $biz_id, 
                        'first_name' => $ucicOwner[$i]['name'],
                        'is_promoter' => $ucicOwner[$i]['owner_type'],
                        'applicant_type' => $appTyp,
                        'mobile_no' => $ucicOwner[$i]['mobile_no'],
                        'date_of_birth' => $ucicOwner[$i]['dob'],
                        'gender' => $ucicOwner[$i]['gender'],
                        'owner_addr' => $ucicOwner[$i]['address'],
                        'comment' => $ucicOwner[$i]['comment'],
                        'other_ownership' => $ucicOwner[$i]['other_ownership'],
                        'networth' => $ucicOwner[$i]['networth'],
                        'share_per' => $ucicOwner[$i]['shareholding'],
                        'designation' => $ucicOwner[$i]['designation'],
                        'home_no' => $ucicOwner[$i]['home_no'],
                        'biz_pan_gst_id' => $ucicOwner[$i]['biz_pan_gst_id'],
                        'is_pan_verified' => $ucicOwner[$i]['is_pan_verified'],
                        'edu_qualification' => $ucicOwner[$i]['edu_qualification'],
                        'cibil_score' => $ucicOwner[$i]['cibil_score'],
                        'is_cibil_pulled' => $ucicOwner[$i]['is_cibil_pulled'],
                        'ckyc_ref_no' => $ucicOwner[$i]['ckyc_ref_no'],
                        'pan_number' => $ucicOwner[$i]['pan_no'],
                        'mobile' => $ucicOwner[$i]['mobile'],
                        'pan_card' => $ucicOwner[$i]['verify_pan'],
                        'driving_license' => $ucicOwner[$i]['verify_dl'],
                        'voter_id' => $ucicOwner[$i]['verify_voter'],
                        'passport' => $ucicOwner[$i]['verify_passport'],
                        'created_by' =>  Auth::user()->user_id]);
                        
                        $newOwnerId = $newOwner->biz_owner_id;
                    }

                    $ownerAddress = array(
                        'addr_1' => $ucicOwner[$i]['address'],
                        'biz_id' => $biz_id,
                        'address_type' => 5,
                        'rcu_status' => 0,
                        'created_by' => Auth::user()->user_id,
                        'biz_owner_id' => $owner->biz_owner_id ?? $newOwnerId ?? NULL
                    );
                    BusinessAddress::create($ownerAddress);


                    /*
                    //Get Biz Owner Address
                  

                    //Get Biz API Data
                    $bizApiData  = BizApi::where('biz_id',$oldBizId);
                    foreach($bizApiData as $apiData) {
                        $bizApiArrData = $apiData ? $this->arrayExcept($apiData->toArray(), array_merge($excludeKeys, ['biz_api_id'])) : [];
                        $bizApiArrData['biz_id'] = $newBizId;
                        $bizApiArrData['biz_owner_id'] = $owner->biz_owner_id ?? $newOwnerId ?? NULL;
                        BizApi::create($bizApiArrData);
                    } 

                    
                    //Get and save application document files         
                    
                    $appDocFilesData = AppDocumentFile::where('app_id',$oldAppId);
                    foreach($appDocFilesData as $appDocFile) {
                        $appDocFilesArrData = $appDocFile ? $this->arrayExcept($appDocFile->toArray(), array_merge($excludeKeys, ['app_doc_file_id'])) : [];
                        $appDocFilesArrData['app_id'] = $newAppId; 
                        $appDocFilesArrData['biz_owner_id'] = $owner->biz_owner_id ?? $newOwnerId ?? NULL;
                        AppDocumentFile::create($appDocFilesArrData);
                    }  
                    */
                    $i++;  
                }

                if(!empty($appOwnerIds)){

                }

                // if(){
                //     rta_app_doc_file  
                //     rta_biz_addr      
                //     rta_biz_api       
                //     rta_biz_crif      
                //     rta_biz_owner     
                //     rta_biz_pan_gst   
                // }
            }
        }
    }
   
    public function updateUcicByAppId($appId, $attributes){
        return UcicUser::updateUcicByAppId($appId, $attributes); 
    }

    public function saveUserUcicDetail($attributes){
        return UcicUserDetail::saveUserUcicDetail($attributes);
    }

    public function deleteUcicUserDetail($userUcicId,$deleteMail){
        return UcicUserDetail::deleteUcicUserDetail($userUcicId,$deleteMail);
    }
}