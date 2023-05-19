<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Models\Master\NewGroup;
use App\Http\Requests\Master\GroupRequest;
use Illuminate\Support\Facades\Route;
use Session;
use DB;

class GroupController extends Controller
{

    public function __construct(InvMasterRepoInterface $master){
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master.group.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $groupId = '';
        if ($request->has('group_id')) {
            $groupId = preg_replace('#[^0-9]#', '', $request->get('group_id'));
        }
        $groupData = [];
        $isGroupApproved = false;
        $currentActiveGroupSanction = 0;
        $currentGroupOutstanding = 0;
        if(!empty($groupId)){
            $isGroupApproved = $this->masterRepo->checkGroupIsApproved($groupId);
            $groupData = $this->masterRepo->getNewGroupById($groupId);
            $data = \Helpers::getGroupAppList($groupId);

            $total_sanction_amt = 0;
            $total_outstanding_amt = 0;
            foreach ($data as $key => $value) {
                $total_sanction_amt += ($value->sanction > 0) ? $value->sanction : 0;
                $total_outstanding_amt += ($value->outstanding > 0) ? $value->outstanding : 0;
            }

            $currentActiveGroupSanction = $total_sanction_amt ?? "";
            $currentGroupOutstanding = $total_outstanding_amt ?? "";
        }
        return view('master.group.create', compact('groupData', 'isGroupApproved', 'currentActiveGroupSanction', 'currentGroupOutstanding'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        try {
            DB::beginTransaction();

            $status = $result = $isGroupApproved = $groupId = false;
            $type = 1;
            $approveStatus = $activeStatus = 0;

            if(!empty($request->get('group_id'))){
                $groupId = preg_replace('#[^0-9]#', '', $request->get('group_id'));
                $isGroupApproved = $this->masterRepo->checkGroupIsApproved($groupId);
            }

            if ($groupId && $request->has('is_approve_group') && $request->get('is_approve_group') == 1) {
                $grpCode = \Helpers::generateGroupCode($groupId);
                $type = 2;
                $approveStatus = $activeStatus = 1;
            }

            $groupName = strtoupper(strtolower(trim($request->get('group_name'))));
            $arrSaveData = ['group_name' => $groupName];
            $arrSaveData['group_field_1'] = $request->get('group_field_1') ? trim($request->get('group_field_1')) : NULL; 
            $arrSaveData['group_field_2'] = $request->get('group_field_2') ? trim($request->get('group_field_2')) : NULL; 
            $arrSaveData['group_field_3'] = $request->get('group_field_3') ? trim($request->get('group_field_3')) : NULL; 
            $arrSaveData['group_field_4'] = $request->get('group_field_4') ? trim($request->get('group_field_4')) : NULL; 
            $arrSaveData['group_field_5'] = $request->get('group_field_5') ? trim($request->get('group_field_5')) : NULL; 
            $arrSaveData['group_field_6'] = $request->get('group_field_6') ? trim($request->get('group_field_6')) : NULL;
            if (!$isGroupApproved) {
                $arrSaveData['group_code'] = isset($grpCode) && $grpCode ? $grpCode : NULL;
                $arrSaveData['current_group_sanction'] = $request->get('current_group_sanction');
                $arrSaveData['current_group_outstanding'] = $request->get('current_group_outstanding');
                $arrSaveData['is_active']     = $activeStatus;
            }

            if($request->has('group_id') && $request->get('group_id') && $groupId){
                $status = $this->masterRepo->updateOrCreateNewGroup($arrSaveData, $groupId);
            }else{
                $status = $this->masterRepo->updateOrCreateNewGroup($arrSaveData);
            }

            if($status){
                $successMsg = $groupId ? trans('master_messages.group_edit_success') : trans('master_messages.group_add_success');
                $commit = true;

                if ((!$isGroupApproved && !$groupId) || ($groupId && $type == 2)) {
                    $model = new NewGroup();
                    $roleData = \Helpers::getUserRole();
                    $attributes = [
                        'table_name' => $model->getTable(),
                        'group_id'   => $status->group_id ?? $groupId,
                        'type'       => $type,
                        'route_name' => Route::currentRouteName(),
                        'status'     => $approveStatus
                    ];
                    $result = $this->masterRepo->saveMakerChecker($attributes);
                    if (!$result)
                        $commit = false;
                }

                if($result && $request->has('is_approve_group') && $request->get('is_approve_group') == 1) {
                    $successMsg = trans('master_messages.group_approved_success');
                }

                if ($commit) {
                    DB::commit();
                    Session::flash('message', $successMsg);
                }else {
                    DB::rollback();
                    Session::flash('error', trans('master_messages.something_went_wrong'));    
                }

                return redirect()->route('get_master_group_list');
            }else {
                DB::rollback();
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_master_group_list');
            }
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function viewGroupUcic(Request $request)
    {
        $groupId = $request->has('group_id') && $request->get('group_id') ? $request->get('group_id') : null;
        $group = NewGroup::find($groupId);

        if (!$group) {
            return redirect()->back()->with("error", "Group Not Found.");
        }

        $data = NewGroup::getCurrentActiveGroupSanction($groupId);
        $currentActiveGroupSanction = $data['sanctionAmt'];
        $currentGroupOutstanding = $data['outstandingAmt'];

        return view('master.group.view_ucic', compact('groupId', 'group', 'currentActiveGroupSanction', 'currentGroupOutstanding'));
    }
}
