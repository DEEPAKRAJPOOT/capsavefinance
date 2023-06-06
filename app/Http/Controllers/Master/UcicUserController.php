<?php

namespace App\Http\Controllers\Master;

use Helpers;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\BizApi;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\AppGroupDetail;
use App\Http\Requests\BusinessInformationRequest;
use App\Inv\Repositories\Contracts\UserInterface;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Models\Master\LocationType;
use App\Inv\Repositories\Contracts\DocumentInterface;
use App\Inv\Repositories\Contracts\UcicUserInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use Illuminate\Support\Facades\Validator;

class UcicUserController extends Controller
{
    use ActivityLogTrait;
    use ApplicationTrait;
	use LmsTrait;

    protected $appRepo;
    protected $ucicuser_repo;
    protected $master;
    protected $userRepo;
    protected $docRepo;

    public function __construct( ApplicationInterface $app_repo, UcicUserInterface $ucicuser_repo, MasterInterface $master, UserInterface $user_repo, DocumentInterface $doc_repo){
        $this->appRepo = $app_repo;
        $this->ucicuser_repo = $ucicuser_repo;
        $this->masterRepo = $master;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
    }

    public function list() {
        return view('master.ucic.index');
    }

    public function businessInfo(Request $request) {
        try {
            $data = [];
            $cinList = [];
			$gstList = [];
            $userUcicId = $request->get('userUcicId');

			$states = State::getStateList()->get();
			$industryList = $this->appRepo->getIndustryDropDown()->toArray();
			$constitutionList = $this->appRepo->getConstitutionDropDown()->toArray();
			$segmentList = $this->appRepo->getSegmentDropDown()->toArray();
			$locationType = LocationType::getLocationDropDown();
			$product_types = $this->masterRepo->getProductDataList();
            $ucicDetails = UcicUser::with('ucicUserDetail:user_ucic_id,invoice_level_mail')->find($userUcicId);
            $emailIds = $ucicDetails->ucicUserDetail->pluck('invoice_level_mail')->filter()->toArray();

            if (empty($emailIds)) {
                $commaSeparatedEmails = '';
            } else {
                $commaSeparatedEmails = implode(',',$emailIds);
            }
            if($ucicDetails){
                $data = json_decode($ucicDetails->business_info ?? '{}',true); 
                $businessDetails = $ucicDetails->app->business->cin ?? null;
                $cinList = $ucicDetails->app->business->cins ?? NULL;
                $gstList = $ucicDetails->app->business->gsts ?? NULL;
            }

			if (!empty($app_data->products)) {
				foreach($app_data->products as $product){
					$product_ids[$product->pivot->product_id]= array(
						"loan_amount" => $product->pivot->loan_amount,
						"tenor_days" => $product->pivot->tenor_days
					);
				}
			}

			if ($data) {
				return view('master.ucic.info')
                        ->with('ucic',$ucicDetails)
						->with('states',$states)
						->with('industryList',$industryList)
						->with('constitutionList',$constitutionList)
						->with('segmentList',$segmentList)
						->with('locationType',$locationType)
						->with('product_types',$product_types)
						->with('cinList',$cinList)
						->with('gstList',$gstList)
						->with('data',$data)
						->with('commaSeparatedEmails',$commaSeparatedEmails);
			} else {
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}   
    }

	public function saveBusinessInfo(BusinessInformationRequest $request){
		try {
			$arrFileData = $request->all();
            $invoiceLevelMail = explode(',',$arrFileData['invoice_level_mail']);
            $userUcicId = $arrFileData['userUcicId'];
            $ucicDetails = UcicUser::with('ucicUserDetail:user_ucic_id,invoice_level_mail')->find($userUcicId);
            $emailIds = $ucicDetails->ucicUserDetail->pluck('invoice_level_mail')->toArray();
            $deleteMails = array_diff($emailIds,$invoiceLevelMail);
            $emailIds = array_diff($invoiceLevelMail,$emailIds);
            $UcicUserDetail = [];

            $validator = Validator::make($request->all(), [
                'invoice_level_mail' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $emails = array_map('trim', explode(',', $value));
                        $uniqueEmails = [];
                        $invalidEmails = [];
                        $duplicateEmails = [];
            
                        foreach ($emails as $email) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $invalidEmails[] = $email;
                            } elseif (in_array($email, $uniqueEmails)) {
                                $duplicateEmails[] = $email;
                            } else {
                                $uniqueEmails[] = $email;
                            }
                        }
            
                        if (!empty($invalidEmails)) {
                            $fail("Invalid email IDs: " . implode(', ', $invalidEmails));
                            return;
                        }
            
                        if (!empty($duplicateEmails)) {
                            $fail("Duplicate email IDs: " . implode(', ', $duplicateEmails));
                            return;
                        }
                    },
                ],
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            if($userUcicId ){
                if(!empty($deleteMails)){
                    foreach($deleteMails as $deleteMail){
                        $UcicUserDetailDelete = $this->ucicuser_repo->deleteUcicUserDetail($userUcicId,$deleteMail);
                    }
                }
                if(!empty($emailIds)){
                    foreach($emailIds as $emailId){
                        $UcicUserDetail = ['invoice_level_mail' => trim($emailId),'user_ucic_id' => $userUcicId];
                        $userUcicDetail = $this->ucicuser_repo->saveUserUcicDetail($UcicUserDetail);
                    }
                }               
                unset($arrFileData['invoice_level_mail']);
                $result = $this->ucicuser_repo->saveBusinessInfoUcic($arrFileData,$userUcicId);
                if($result){
                    $whereActivi['activity_code'] = 'company_details_save';
                    $activity = $this->masterRepo->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Save Company Details (Business Information) UCIC Information.';
                        $arrActivity['user_ucic_id'] = $userUcicId;
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
                    }
                    Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                    return redirect()->route('management_details',['userUcicId' =>  $userUcicId]);
                }
			} else {
				return redirect()->back()->withInput()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withInput()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

    public function showPromoterDetails(Request $request) {
        $userUcicId = $request->get('userUcicId');
        $data = [];
        $editFlag = 0;
        $ownerPanApi = null;
        $cin = null;
        $getProductType = null;
        $appData = null;
        $ucicDetails = UcicUser::find($userUcicId);
        if($ucicDetails){
            $data = json_decode(($ucicDetails->management_info ?? "{}"),true); 
            $ownerPanApi = $data['management_info']['ownerIds'] ?? [];
            foreach($ownerPanApi as $ownerKey => $ownerId){
                if($ownerId){
                    $ownerApi = BizApi::where('biz_owner_id',$ownerId)->get();
                    foreach ($ownerApi as $row1) {
                        if ($row1->type == 7) {
                            $data['management_info']['owners'][$ownerKey]['mobileNo'] = $row1->karza ? json_decode($row1->karza->req_file,true) : '';
                        }
                        else if ($row1->type == 8) {
                            $data['management_info']['owners'][$ownerKey]['mobileOtpNo'] = $row1->karza ? json_decode($row1->karza->req_file,true) : '';
                        }
                        else if ($row1->type == 9) {
                            $data['management_info']['owners'][$ownerKey]['panVerifyNo'] = $row1->karza ? json_decode($row1->karza->req_file,true) : '';
                        }
                    }
                }else{
                    $data['management_info']['owners'][$ownerKey]['mobileNo'] = [];
                    $data['management_info']['owners'][$ownerKey]['mobileOtpNo'] = [];
                    $data['management_info']['owners'][$ownerKey]['panVerifyNo'] = [];
                }
            }
            $cin = null;
            $getProductType = 0;
        }
        
        return view('master.ucic.managementInfo')->with([
            'ucic' => $ucicDetails,
            'ownerDetails' => $ownerPanApi,
            'cin_no' => $cin,
            'edit' => $editFlag,
            'is_lease' => $getProductType,
            'manInfoData' => $data['management_info']['owners'] ?? [],
            'manInfoDocData' => $data['document_upload']??[]
        ]);
    }
    
    public function updatePromoterDetail(Request $request) {
        try {
            $arrFileData = $request->all();
            $userUcicId = $arrFileData['userUcicId'];
                        
            if($userUcicId){
                $result = $this->ucicuser_repo->saveManagementInfoUcic($arrFileData,$userUcicId);
                if($result){
                    $whereActivi['activity_code'] = 'promoter_details_save';
                    $activity = $this->masterRepo->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Save Promoter Details (Management Information) UCIC Information.';
                        $arrActivity['user_ucic_id'] = $userUcicId;
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
                    }
                    return response()->json(['message' =>trans('success_messages.promoter_saved_successfully'),'status' => 1]);
                } else {
                    return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(\Helpers::getExceptionMessage($ex));
        }
    }

    public function groupLinking(Request $request) {
        $userUcicId = $request->get('userUcicId');
        $arrCamData = array();
        $ucic = UcicUser::find($userUcicId);
        $data['group_id'] =  $ucic && $ucic->group_id ? $ucic->group_id : '';
        $data['user_ucic_id'] = $ucic->user_ucic_id;
        
        $allNewGroups =  $this->masterRepo->getAllNewActiveGroup();

        return view('master.ucic.group_linking', compact('allNewGroups', 'data', 'ucic'));
    }

    public function saveGroupLinking(Request $request){
        try{
            $arrData = $request->all();

            $request->validate([
                'group_id' => 'required|exists:App\Inv\Repositories\Models\Master\NewGroup,group_id'
            ], [], ['group_id' => 'Group Name']);

            $userUcicId = $arrData['userUcicId'];
            $old_group_id = $arrData['old_group_id'];
            $group_id = $arrData['group_id'];
            $groupconfirm = $arrData['groupconfirm'];
            $appIds = $arrData['appIds'];
            unset($arrData);
            if(!isset($groupconfirm)) {
                return redirect()->back()->withErrors("Please check confirm box");
            }elseif($appIds) {
                AppGroupDetail::whereIn('app_id',$appIds)->delete();
                UcicUserUcic::whereIn('app_id',$appIds)->update(['group_id' => $group_id]);
                
                $ucic = $this->ucicuser_repo->getUcicData(['user_ucic_id' => $userUcicId]);
                
                $ucic->update([
                    'group_id' => $group_id,
                    'updated_info_src' => 2,
                ]);
                
                Session::flash('message', trans('Group name updated successfully for the '.$ucic->ucic_code));
                Session::flash('operation_status',1);
                return redirect()->back();
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(\Helpers::getExceptionMessage($ex));
        }
    }

	public function ucicPromoterDocumentSave(Request $request){
		try {
			$arrFileData = $request->all();
			$ownerId = $request->get('owner_id');
            $userUcicId = $request->get('userUcicId');
            
			$uploadData = Helpers::uploadUCICFile($arrFileData, $userUcicId);
			$userFile = $this->docRepo->saveFile($uploadData);

			$resultResponse = ['result' => '','status' => 0];
			if(!empty($userFile->file_id)) {
				$fileId   = $userFile->file_id;
				$response = $this->docRepo->getFileByFileId($fileId);
				$filePath = Storage::url($response->file_path);
				$ucicData = $this->ucicuser_repo->getUcicData(['user_ucic_id' => $userUcicId]);
				\Helpers::updateOrDeleteDocFileToUcic($ucicData, $ownerId, $arrFileData['doc_type_name'], $fileId, $filePath, NULL, 'ucic');
				$resultResponse = ['result' => $response,'status' => 1, 'file_path' => $filePath];
			}

			return response()->json($resultResponse);
		} catch (Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}

	public function ucicPromoterDocumentDelete(Request $request){
		try {
			$fileId = $request;
			$ownerId = $request->get('owner_id');
            $userUcicId = $request->get('userUcicId');
			$response = $this->docRepo->deleteFile($fileId);
            $ucicData = $this->ucicuser_repo->getUcicData(['user_ucic_id' => $userUcicId]);
			\Helpers::updateOrDeleteDocFileToUcic($ucicData, $fileId['owner_id'], $fileId['doc_type_name'], $fileId['file_id'], $filePath = '', NULL, 'ucic', $update = false, $delete = true);

			if (!$response) {
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
			Session::flash('message',trans('success_messages.deleted'));
			return redirect()->back();
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

    public function groupChangeConfirmation(Request $request) {
		$group_id = $request->get('group_id');
        $new_group_id = $request->get('newGroupId');
		$userUcicId = $request->get('userUcicId');
        $GroupDetailsArray  =
        Application::select('app.app_id','app.app_code','mst_status.status_name')
            ->join('user_ucic_user','app.app_id','user_ucic_user.app_id')
            ->join('user_ucic','user_ucic_user.ucic_id','user_ucic.user_ucic_id')
            ->join('mst_status','app.curr_status_id','mst_status.id')
            ->where('user_ucic_user.ucic_id',$userUcicId)
            ->where('user_ucic_user.group_id','<>',$new_group_id)
            ->whereIn('app.curr_status_id',[49,20,56,55,23])
            ->groupBy('app.app_id')
            ->get();

        $appIds = $GroupDetailsArray->pluck('app_id');
		return view('master.group.group_confirmation')
				->with('old_group_id', $group_id)
				->with('userUcicId', $userUcicId)
                ->with('GroupDetailsArray',$GroupDetailsArray)
                ->with('appIds',$appIds);
	}


}
