<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\ProgramRequest;
use App\Http\Requests\Lms\SubProgramRequest;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use Session;

class ProgramController extends Controller {

    protected $userRepo;
    protected $appRepo;
    protected $master;

    /**
     *
     * @var type mixed
     */
    protected $invRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $app_repo, InvMasterRepoInterface $master,
            InvoiceInterface $invRepo)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
        $this->master = $master;
        $this->invRepo = $invRepo;
    }

    /**
     * Show mange program
     * 
     * @param Request $request Object
     * @return type mixed
     */
    public function mangeProgramList(Request $request)
    {
        $anchor_id = (int) $request->get('anchor_id');
        \Session::forget('is_mange_program');
        if (empty($anchor_id)) {
            \Session::put('is_mange_program', 1);
        }
        return view('backend.lms.show_mange_program')
                        ->with(['anchor_id' => $anchor_id]);
    }

    /**
     * Add program
     * 
     * @param Request $request Object
     * @return type mixed
     */
    public function addProgram(Request $request)
    {
        try {
            $anchor_id = (int) $request->get('anchor_id');
            $program_id = $request->has('program_id') ? (int) $request->get('program_id') : null;
            $action_type = $request->has('type') ? $request->get('type') : '';
            
            if ($anchor_id) {
                $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->toArray();
            } else {
                $anchorData = $this->userRepo->getAllAnchor('comp_name')->toArray();
            }
            
            $anchors = $this->appRepo->getProgramAnchors();            
            
            $anchorList = array_reduce($anchorData, function ($output, $element) use ($anchors) {
                $output[$element['anchor_id']]['name'] = $element['f_name'];                      
                $output[$element['anchor_id']]['used'] = in_array($element['anchor_id'], $anchors) ? 1 : 0;
                return $output;
            }, []);

            if (\Session::has('is_mange_program')) {
                //$anchorList = ['' => 'Please Select'] + $anchorList;
            }

            $productItem = $this->master->getProductDataList()->toArray();
            foreach ($productItem as $key => $value) {
                if($key == 1){
                    $product[$key] = $value;
                }
            }
//            $productList = ['' => 'Please Select'] + $product;
            $productList = $product;
            $redirectUrl = (\Session::has('is_mange_program')) ? route('manage_program') : route('manage_program', ['anchor_id' => $anchor_id]);
            $industryList = $this->appRepo->getIndustryDropDown()->toArray(); 
            

            $programData = null;
            $subIndustryList = null;
            $utilizedLimit = 0;
            $remaningAmount = 0;
            if (!empty($program_id)) {
                $programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['*'])->first();
                $subIndustryList = $this->appRepo->getSubIndustryByWhere(['industry_id' => $programData->industry_id])->pluck('name','id')->toArray();    
                $subPrgms = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $program_id], ['prgm_id'])->pluck('prgm_id');
                $prgmIds = $subPrgms ? $subPrgms->toArray() : [];            
                $utilizedLimit = count($prgmIds) > 0 ? $this->appRepo->getPrgmBalLimit($prgmIds) : 0;
                
                $anchorSubLimitTotal = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $program_id, 'status' => 1], ['anchor_sub_limit'])->sum('anchor_sub_limit'); 
                $remaningAmount = $programData->anchor_limit - $anchorSubLimitTotal;
            
                $view = 'backend.lms.edit_program';
            } else {
                $view = 'backend.lms.add_program';
            }    
                                    
            return view($view)            
            ->with('industryList', $industryList)
            ->with('subIndustryList', $subIndustryList)
            ->with('anchor_id', $anchor_id)
            ->with('redirectUrl', $redirectUrl)
            ->with('productList', $productList)
            ->with('anchorList', $anchorList)
            ->with('program', $programData)
            ->with('utilizedLimit', $utilizedLimit)
            ->with('remaningAmount', $remaningAmount)
            ->with('action_type', $action_type);
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save program 
     * 
     * @param Request $request
     * @return type mixed 
     */
    public function saveProgram(ProgramRequest $request)
    {
        try {
//            dd($request->all());
            $anchor_id = $request->get('anchor_id');
            $prgm_name = $request->get('prgm_name');
            $program_id = $request->get('program_id');
            $action_type = $request->has('type') ? $request->get('type') : '';
            $status = $request->has('status') ? $request->get('status') : 1;
            
            if (empty($program_id)) {
                $programInfo = $this->appRepo->getProgramByProgramName($prgm_name);
                if (count($programInfo) > 0 ) {
                     \Session::flash('error', trans('success_messages.program_already_exist'));
                     return redirect()->back()->withInput();
                }
            } else {
                $programInfo = [];
                $programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['anchor_limit','modify_reason_type','modify_reason'])->first();
                $oldAnchorLimitAmount = $programData ? $programData->anchor_limit : 0;
                $anchorLimit = ($request->get('anchor_limit')) ? (float) str_replace(',', '', $request->get('anchor_limit')) : 0;
                $subPrgms = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $program_id], ['prgm_id'])->pluck('prgm_id');
                $prgmIds = $subPrgms ? $subPrgms->toArray() : [];
                $utilizedLimit = count($prgmIds) > 0 ? $this->appRepo->getPrgmBalLimit($prgmIds) : 0;
                if ($anchorLimit < $utilizedLimit ) {
                    Session::flash('error_code', 'error_anchor_limit');
                    Session::flash('msg', trans('error_messages.program_anchor_limit'));
                    return redirect()->back();
                }
            }
//            dd(count($programInfo));
            
                $prepareDate = [
                    'anchor_id' => $anchor_id,
                    //   'anchor_user_id' => $request->get('anchor_user_id'),
                    'prgm_name' => $request->get('prgm_name'),
                    'industry_id' => $request->get('industry_id'),
                    'sub_industry_id' => $request->get('sub_industry_id'),
                    'anchor_limit' => ($request->get('anchor_limit')) ? str_replace(',', '', $request->get('anchor_limit')) : null,
                    'is_fldg_applicable' => $request->get('is_fldg_applicable'),
                    'product_id'=>$request->get('product_id'),
                    'status' => $status
                ];
                if (!empty($program_id)) {
                    $this->appRepo->updateProgramData($prepareDate, ['prgm_id' => $program_id]);

                    if ($action_type == 'anchor_program') {
                        //$programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['anchor_limit'])->first();
                        //$anchorLimitAmount = $programData ? $programData->anchor_limit : 0;
                        $addlData=[];
                        $addlData['reason']  = $programData ? $programData->modify_reason_type : null;
                        $addlData['comment'] = $programData ? $programData->modify_reason : null;
                        $addlData['anchor_limit'] = $anchorLimit;
                        $subPrgms = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $program_id,'status'=>1], ['parent_prgm_id','is_edit_allow', 'prgm_id']);
                        //dd($subPrgms);
                        foreach($subPrgms as $prgm) {
                            $result = $this->copyProgram($prgm->prgm_id, $addlData);
                            //if (empty($result['new_prgm_id'])) {
                            //    Session::flash('error_code', 'error_prgm_limit');
                            //    return redirect()->back();
                            //}
                        }
                    } else {
                        if ($oldAnchorLimitAmount != $anchorLimit) {
                            $this->appRepo->updateProgramData(['status' => 0, 'anchor_limit' => $anchorLimit], ['parent_prgm_id' => $program_id]);
                        }
                    }
                    Session::flash('is_accept', 1);
                    Session::flash('msg', trans('success_messages.program_save_successfully'));                    
                    Session::put('route_url', route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => $program_id]));
                    return redirect()->back();
                } else {
                    $this->appRepo->saveProgram($prepareDate);
                }
                \Session::flash('message', trans('success_messages.program_save_successfully'));
                $redirect = redirect()->route('manage_program', ['anchor_id' => $anchor_id]);
                if (\Session::has('is_mange_program')) {
                    $redirect = redirect()->route('manage_program');
                }
                return $redirect;
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * mange sub program
     * 
     * @param Request $request
     * @return type mixed
     */
    public function mangeSubProgram(Request $request)
    {
        try {
            $anchor_id = (int) $request->get('anchor_id');
            $program_id = (int) $request->get('program_id');            
            
            $programData = $this->appRepo->getProgram($program_id);
            $programStatus = ($programData)? $programData->status : 0;             
            \Session::put('list_program_id', $program_id);

            $is_prg_list = $redirectUrl = (\Session::has('is_mange_program')) ? route('manage_program') : route('manage_program', ['anchor_id' => $anchor_id]);
            return view('backend.lms.show_sub_program', compact('anchor_id', 'program_id', 'redirectUrl', 'programStatus'));
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * get sub program
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addSubProgram(Request $request)
    {
        try {
            $anchor_id = (int) $request->get('anchor_id');
            $program_id = (int) $request->get('program_id');
            $reason_type = $request->has('reason_type') ? $request->get('reason_type') : null;
            $copied_prgm_id = $request->has('copied_prgm_id') ? $request->get('copied_prgm_id') : null;
            
            if ($request->has('parent_program_id')) {  //Edit Sub Program
                $parent_program_id = (int) $request->get('parent_program_id');
                $whereCond = ['sub_program_id_nte' => $program_id];                                                
            } else {
                $parent_program_id = $program_id;
                $whereCond = [];
            }
            $action = $request->get('action');
            $subProgramData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id, 'is_null_parent_prgm_id' => true], ['*'], ['programDoc', 'programCharges'])->first();
            //dd($subProgramData);
            $copied_prgm_id = $subProgramData ? $subProgramData->copied_prgm_id : null;
            
            $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->first();
//          dd($program_id);
            $programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['*'], ['programDoc', 'programCharges'])->first();
//            dd($programData);
            $preSanction = $this->appRepo->getDocumentList(['doc_type_id' => 2, 'is_active' => 1])->toArray();
            $postSanction = $this->appRepo->getDocumentList(['doc_type_id' => 3, 'is_active' => 1])->toArray();
            $charges = $this->appRepo->getChargesList()->toArray();

            $anchorSubLimitTotal = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $parent_program_id, 'status' => 1]+$whereCond, ['anchor_sub_limit'])->sum('anchor_sub_limit');            
            $baserate_list =$this->master->getBaseRateDropDown();

            //$subPrgms = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $parent_program_id], ['prgm_id'])->pluck('prgm_id');
            //$prgmIds = $subPrgms ? $subPrgms->toArray() : [];            
            //$utilizedLimit = $this->appRepo->getPrgmBalLimit([$copied_prgm_id,$program_id]); 
            $utilizedLimit = \Helpers::getPrgmBalLimit($program_id);
            $remaningAmount = 0;
            if (isset($programData->anchor_limit)) {
                $remaningAmount = $programData->anchor_limit - $anchorSubLimitTotal; //- $utilizedLimit;
            }
            $anchorUtilizedBalance = \Helpers::getAnchorUtilizedLimit($parent_program_id);
            $pAnchorLimit = 0;
            $pAnchorSubLimit = 0;
            if (!empty($copied_prgm_id)) {
                $copiedPrgm = $this->appRepo->getSelectedProgramData(['prgm_id' => $copied_prgm_id], ['anchor_limit', 'anchor_sub_limit']);
                if (isset($copiedPrgm[0])) {
                    $pAnchorLimit    = $copiedPrgm[0]->anchor_limit;
                    $pAnchorSubLimit = $copiedPrgm[0]->anchor_sub_limit;                    
                }
            }
            /**
             * get DOA list 
             */
            $doaLevelList = $this->master->getDoaLevelList()->toArray();

            $programDoc = $programCharges = $sanctionData = [];
            if (isset($subProgramData->programDoc)) {
                $programDoc = $subProgramData->programDoc->toArray();
                $sanctionData = array_reduce($programDoc, function ($out, $elem) {
                    if (in_array($elem['doc_type_id'], [2])) {
                        $out['pre'][] = $elem['id'];
                    } else if ($elem['doc_type_id'] == 3) {
                        $out['post'][] = $elem['id'];
                    }
                    return $out;
                }, []);
            }

            /**
             * program charges 
             */
            if (isset($subProgramData->programCharges)) {
                $programCharges = $subProgramData->programCharges->toArray();
            }
            $doaResult = [];
            $invoiceDataCount = 0;

            if (isset($subProgramData->prgm_id)) {
                $getDoaLevel = $this->master->getProgramDoaLevelData(['prgm_id' => $subProgramData->prgm_id])->toArray();
                $doaResult = array_reduce($getDoaLevel, function ($out, $elem) {
                    $out[] = $elem['doa_level_id'];
                    return $out;
                }, []);
                $invoiceDataCount = $this->invRepo->getInvoiceData(['program_id' => $subProgramData->prgm_id], ['invoice_id'])->count();
            }
            
            $redirectUrl = (\Session::has('is_mange_program')) ? route('manage_program') : route('manage_program', ['anchor_id' => $anchor_id]);
            return view('backend.lms.add_sub_program',
                    compact(
                            'anchor_id',
                            'anchorData',
                            'programData',
                            'program_id',
                            'postSanction',
                            'preSanction',
                            'charges',
                            'remaningAmount',
                            'redirectUrl',
                            'doaLevelList',
                            'sanctionData',
                            'programCharges',
                            'subProgramData',
                            'action',
                            'doaResult',
                            'invoiceDataCount',
                            'baserate_list',
                            'reason_type',
                            'action',
                            'copied_prgm_id',
                            'utilizedLimit',
                            'anchorSubLimitTotal',
                            'pAnchorLimit',
                            'pAnchorSubLimit',
                            'anchorUtilizedBalance'
            ));
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * prepare Data
     * 
     * @param type $request
     * @return type mixed
     */
    public function prepareSubProgramData($request)
    {
        return [
            'parent_prgm_id' => $request->get('parent_prgm_id'),
            'anchor_id' => $request->get('anchor_id'),
            'product_id' => $request->get('product_id'),
            'anchor_user_id' => $request->get('anchor_user_id'),
            'product_name' => $request->get('product_name'),
            'prgm_name' => $request->get('product_name'),
            'prgm_type' => $request->get('prgm_type'),
            'interest_rate' => $request->get('interest_rate'),
            'anchor_sub_limit' => ($request->get('anchor_sub_limit')) ? str_replace(',', '', $request->get('anchor_sub_limit')) : 0,
            'anchor_limit' => str_replace(',', '', $request->get('anchor_limit')),
            'min_loan_size' => ($request->get('min_loan_size')) ? str_replace(',', '', $request->get('min_loan_size')) : 0,
            'max_loan_size' => ($request->get('max_loan_size')) ? str_replace(',', '', $request->get('max_loan_size')) : 0,
            'base_rate_id' => $request->get('interest_linkage'),
            'interest_borne_by' => $request->get('interest_borne_by'),
            'overdue_interest_borne_by' => $request->get('overdue_interest_borne_by'),
            'margin' => $request->get('margin'),
            'is_adhoc_facility' => $request->get('is_adhoc_facility'),
            'adhoc_interest_rate' => $request->get('is_adhoc_facility') ? $request->get('adhoc_interest_rate') : null,
            'max_interest_rate' => $request->get('max_interest_rate'),
            'min_interest_rate' => $request->get('min_interest_rate'),
            'overdue_interest_rate' => $request->get('overdue_interest_rate'),
            'is_grace_period' => $request->get('is_grace_period'),
            'grace_period' => $request->get('grace_period'),
            'disburse_method' => $request->get('disburse_method'),
            'invoice_upload' => !empty($request->get('invoice_upload')) ? implode(',', $request->get('invoice_upload')) : null,
            'bulk_invoice_upload' => !empty($request->get('bulk_invoice_upload')) ? implode(',', $request->get('bulk_invoice_upload')) : null,
            'invoice_approval' => !empty($request->get('invoice_approval')) ? implode(',', $request->get('invoice_approval')) : null,
            'status' => $request->get('status'),
        ];
    }

    /**
     * save program charges data
     * 
     * @param type $request 
     * @param type $program_id int
     * @throws \App\Http\Controllers\Backend\Exception
     * @throws BlankDataExceptions 
     */
    public function saveProgramCharges($request, $program_id)
    {
        try {
            if (empty($program_id)) {
                throw new BlankDataExceptions(trans('error_message.no_data_found'));
            }
            $charge = $request->get('charge');
            $chrg_calculation_amt = $request->get('chrg_calculation_amt');
            $chrg_calc_min_rate = $request->get('chrg_calc_min_rate');
            $chrg_calc_max_rate = $request->get('chrg_calc_max_rate');
            $chrg_applicable_id = $request->get('chrg_applicable_id');
            $chrg_tiger_id = $request->get('chrg_tiger_id');
            $chrg_calculation_type = $request->get('chrg_calculation_type');
            $chrg_type = $request->get('chrg_type');
            $gst_rate = $request->get('gst_rate');
            $is_gst_applicable = $request->get('is_gst_applicable');
            foreach ($charge as $keys => $values) {
                $out[] = [
                    'prgm_id' => $program_id,
                    'charge_id' => $values,
                    // 'chrg_applicable_id' => ,
                    'chrg_calculation_type' => isset($chrg_calculation_type[$keys]) ? $chrg_calculation_type[$keys] : null,
                    'chrg_type' => isset($chrg_type[$keys]) ? $chrg_type[$keys] : null,
                    'chrg_calculation_amt' => isset($chrg_calculation_amt[$keys]) ? str_replace(',', '', $chrg_calculation_amt[$keys]) : null,
                    'is_gst_applicable' => isset($is_gst_applicable[$keys]) ? $is_gst_applicable[$keys] : null,
                    'gst_percentage' => isset($gst_rate[$keys]) ? $gst_rate[$keys] : null,
                    'chrg_calc_min_rate' => isset($chrg_calc_min_rate[$keys]) ? $chrg_calc_min_rate[$keys] : null,
                    'chrg_calc_max_rate' => isset($chrg_calc_max_rate[$keys]) ? $chrg_calc_max_rate[$keys] : null,
                    'chrg_applicable_id' => isset($chrg_applicable_id[$keys]) ? $chrg_applicable_id[$keys] : null,
                    'chrg_tiger_id' => isset($chrg_tiger_id[$keys]) ? $chrg_tiger_id[$keys] : null,
                    'created_at' => \carbon\Carbon::now(),
                    'created_by' => \Auth::user()->user_id
                ];
            }
            if (isset($out) && count($out) > 0) {
                $this->appRepo->deleteProgramData(['prgm_id' => $program_id]);
                $this->appRepo->saveProgramChrgData($out);
            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save sub program data
     * @param Request $request
     */
    public function saveSubProgram(SubProgramRequest $request)
    {
        try {
//            dd($request->all());
            $user_id = \Auth::user()->user_id;
            $dataForProgram = $this->prepareSubProgramData($request);
            $pkeys = $request->get('program_id');
            
            $program_list_id = (\Session::has('list_program_id')) ? \Session::get('list_program_id') : $request->get('parent_prgm_id');
            if ($request->has('is_reject') && $request->get('is_reject') == '1') {
                $copied_prgm_id = $request->get('copied_prgm_id');
                $this->appRepo->updateProgramData(['status' => 3], ['prgm_id' => $pkeys]); //Rejected
                $this->appRepo->updateProgramData(['status' => 1], ['prgm_id' => $copied_prgm_id]);                
                return redirect()->route('manage_sub_program', ['anchor_id' => $request->get('anchor_id'), 'program_id' => $program_list_id]);                
            }
            
            if($request->get('interest_rate') == 1) {
                $dataForProgram['base_rate_id'] = '';
            }
            
            $anchorSubLimit = ($request->get('anchor_sub_limit')) ? str_replace(',', '', $request->get('anchor_sub_limit')) : 0;
            $utilizedLimit = \Helpers::getPrgmBalLimit($pkeys);            
            if ($anchorSubLimit < $utilizedLimit ) {
                return redirect()->back()->withErrors(trans('error_messages.program_anchor_limit'))->withInput();
            }
                
            /**
             * save program data
             */
            if (!empty($pkeys)) {
                unset($dataForProgram['parent_prgm_id']);
                // dd($dataForProgram);                
                $this->appRepo->updateProgramData($dataForProgram, ['prgm_id' => $pkeys]);
                $lastInsertId = $pkeys;
            } else {
                $lastInsertId = $this->appRepo->saveProgram($dataForProgram);
            }

            $program = [];
            $program['anchor_limit'] = str_replace(',', '', $request->get('anchor_limit'));
            $this->appRepo->updateProgramData($program, ['parent_prgm_id' => $program_list_id ,'status' => 1]);
            $this->appRepo->updateProgramData($program, ['prgm_id' => $program_list_id]);
            
            /**
             * Save program charges data 
             */
            $this->saveProgramCharges($request, $lastInsertId);
            $pre = !empty($request->get('pre_sanction')) ? $request->get('pre_sanction') : [];
            $post = !empty($request->get('post_sanction')) ? $request->get('post_sanction') : [];

            $pre_sanction = array_filter($pre);
            $post_sanction = array_filter($post);
            $this->appRepo->deleteDoc(['prgm_id' => $lastInsertId]);
            $preSanctionData = array_reduce($pre_sanction, function($out, $element) use($lastInsertId, $user_id) {
                $out[] = [
                    'user_id' => \Auth::user()->user_id,
                    'prgm_id' => $lastInsertId,
                    'doc_id' => (int) $element,
                    'wf_stage_id' => 49,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => \carbon\Carbon::now(),
                    'updated_at' => \carbon\Carbon::now(),
                ];
                return $out;
            }, []);

            if (count($preSanctionData)) {
                $this->appRepo->saveProgramDoc($preSanctionData);
            }

            $postSanctionData = array_reduce($post_sanction, function($out, $element) use($lastInsertId, $user_id) {
                $out[] = [
                    'user_id' => \Auth::user()->user_id,
                    'prgm_id' => $lastInsertId,
                    'doc_id' => (int) $element,
                    'wf_stage_id' => 55,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => \carbon\Carbon::now(),
                    'updated_at' => \carbon\Carbon::now()
                ];
                return $out;
            }, []);
            if (count($postSanctionData)) {
                $this->appRepo->saveProgramDoc($postSanctionData);
            }

            /**
             * save DOA level 
             */
            $this->saveDoaLevel($request, $lastInsertId);

            if ($request->has('copied_prgm_id') && !empty($request->get('copied_prgm_id'))) {
                //Update status of existing program id
                $updatePrgmData = [];
                $updatePrgmData['status'] = 2;         

                $whereUpdatePrgmData = [];
                $whereUpdatePrgmData['prgm_id'] = $request->get('copied_prgm_id');
                $this->appRepo->updateProgramData($updatePrgmData, $whereUpdatePrgmData);
            }
            
            \Session::flash('message', trans('success_messages.sub_program_save_successfully'));
            $program_list_id = (\Session::has('list_program_id')) ? \Session::get('list_program_id') : $request->get('parent_prgm_id');
            return redirect()->route('manage_sub_program', ['anchor_id' => $request->get('anchor_id'), 'program_id' => $program_list_id]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * Save DOA level
     * 
     * @param type $request
     * @return type mixed
     */
    function saveDoaLevel($request, $program_id)
    {
        try {
            $doa_level = !empty($request->get('doa_level')) ? $request->get('doa_level') : [];
            $prepareSaveDoa = [];
            foreach ($doa_level as $key => $value) :
                $prepareSaveDoa[$key] = [
                    'doa_level_id' => $value,
                    'prgm_id' => $program_id,
                ];
            endforeach;

            if (count($prepareSaveDoa)) {
                $this->master->deleteDoaLevelBywhere(['prgm_id' => $program_id]);
                $this->master->insertDoaLevel($prepareSaveDoa);
            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    protected function copyProgram($prgmId, $addlData=[])
    {
        \DB::beginTransaction();

        try {
            
            $excludeKeys = ['created_at', 'created_by','updated_at', 'updated_by'];
            $newPrgmId = null;
            
            //Get and save Program Data
            $whereCond=[];
            $whereCond['prgm_id'] = $prgmId;
            $prgms = $this->appRepo->getSelectedProgramData($whereCond);
            if (isset($prgms[0])) {
                $prgm = $prgms[0];
                $insPrgmData = $prgm ? $this->arrayExcept($prgm->toArray(), array_merge($excludeKeys, ['prgm_id', 'is_edit_allow'])) : [];
                $insPrgmData['copied_prgm_id'] = $prgmId;
                $insPrgmData['status'] = 0;     
                $insPrgmData['modify_reason_type'] = $addlData['reason'];
                $insPrgmData['modify_reason'] = $addlData['comment'];
                if (isset($addlData['anchor_limit']) && !empty($addlData['anchor_limit'])) {
                    $insPrgmData['anchor_limit'] = $addlData['anchor_limit'];
                }
                $newPrgmId = $this->appRepo->saveProgram($insPrgmData);
                
                if ($newPrgmId) {                    
                    //Update status of existing program id
                    $updatePrgmData = [];
                    $updatePrgmData['status'] = 2;
                    //$updatePrgmData['modify_reason_type'] = $addlData['reason'];
                    //$updatePrgmData['modify_reason'] = $addlData['comment'];            

                    $whereUpdatePrgmData = [];
                    $whereUpdatePrgmData['prgm_id'] = $prgmId;             
                    $this->appRepo->updateProgramData($updatePrgmData, $whereUpdatePrgmData);


                    //Get and save Program Charge Data
                    $whereCond=[];
                    $whereCond['prgm_id'] = $prgmId;
                    $prgmCharges = $this->appRepo->getPrgmChargeData($whereCond);
                    foreach($prgmCharges as $prgmCharge) {  
                        $insPrgmChargeData = $prgmCharge ? $this->arrayExcept($prgmCharge->toArray(), array_merge($excludeKeys, ['id'])) : [];
                        $insPrgmChargeData['prgm_id'] =  $newPrgmId;                    
                        $insPrgmChargeData['created_by'] =  \Auth::user()->user_id;
                        $insPrgmChargeData['created_at'] =  \Carbon\Carbon::now();
                        //$insPrgmChargeData['updated_by'] =  \Auth::user()->user_id;
                        //$insPrgmChargeData['updated_at'] =  \Carbon\Carbon::now();

                        $this->appRepo->saveProgramChrgData($insPrgmChargeData);                    
                    }


                    //Get and save Progam Docs
                    $whereCond=[];
                    $whereCond['prgm_id'] = $prgmId;
                    $prgmDocs = $this->appRepo->getPrgmDocs($whereCond);
                    foreach($prgmDocs as $prgmDoc) {
                        $insPrgmDocsData = $prgmDoc ? $this->arrayExcept($prgmDoc->toArray(), array_merge($excludeKeys, ['prgm_doc_id'])) : [];
                        $insPrgmDocsData['prgm_id'] =  $newPrgmId;
                        $insPrgmDocsData['created_by'] =  \Auth::user()->user_id;
                        $insPrgmDocsData['created_at'] =  \Carbon\Carbon::now();
                        $insPrgmDocsData['updated_by'] =  \Auth::user()->user_id;
                        $insPrgmDocsData['updated_at'] =  \Carbon\Carbon::now();

                        $this->appRepo->saveProgramDoc($insPrgmDocsData);
                    }
                }
            }
                   
            \DB::commit();
            
            $result = [];
            $result['new_prgm_id'] = $newPrgmId;
            
            return $result;
            // all good
        } catch (\Exception $e) {
            \DB::rollback();
            // something went wrong
            dd($e->getFile(), $e->getLine(), $e->getMessage());       
            return [];
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

    
    /**
     * Confirm End Program
     * 
     * @param Request $request
     * @return type mixed
     */
    public function confirmEndProgram(Request $request)
    {
        $anchor_id  = (int) $request->get('anchor_id');
        $program_id = (int) $request->get('program_id');
        $parent_program_id = $request->get('parent_program_id');
        $action            = $request->get('action');               
        $action_type       = $request->has('type') ? $request->get('type') : '';       
        
        $reasonList = config('common.program_modify_reasons');
        
        return view('backend.lms.confirm_end_program')
            ->with('reasonList', $reasonList)
            ->with('anchor_id', $anchor_id)
            ->with('program_id', $program_id)
            ->with('parent_program_id', $parent_program_id)
            ->with('action', $action)
            ->with('action_type', $action_type);
    }
    
    /**
     * Save End Program
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveEndProgram(Request $request)
    {
        try {            
            $anchor_id = (int) $request->get('anchor_id');
            $program_id = (int) $request->get('program_id');
            $parent_program_id = $request->get('parent_program_id');
            $action            = $request->get('action');            
            $reason_type = $request->get('reason');
            $comment = $request->get('comment');
            
            $addlData = [];
            $addlData['reason'] = $reason_type;
            $addlData['comment'] = $comment;
            if ($request->has('type') && $request->get('type') == 'anchor_program') { 
                $this->appRepo->updateProgramData(['status' => 0, 'modify_reason_type' => $reason_type, 'modify_reason' => $comment], ['prgm_id' => $program_id]);
                
                Session::flash('is_accept_redirect', 1);
                //Session::put('route_url', route('manage_sub_program', ['anchor_id' => $anchor_id, 'program_id' => $parent_program_id]));
                Session::put('route_url', route('edit_program', ['anchor_id' => $anchor_id, 'program_id' => $program_id, 'type' => 'anchor_program']));
                return redirect()->back();                
            } else {
                $result = $this->copyProgram($program_id, $addlData);
            
                if (empty($result['new_prgm_id'])) {
                    Session::flash('error_code', 'error_prgm_limit');
                    return redirect()->back();
                }

                $new_prgm_id = $result['new_prgm_id'];
                                
                /*
                $updatePrgmData = [];
                $updatePrgmData['is_edit_allow'] = 1;

                $whereUpdatePrgmData = [];
                $whereUpdatePrgmData['prgm_id'] = $program_id;
                $this->appRepo->updateProgramData($updatePrgmData, $whereUpdatePrgmData);
                */

                Session::flash('is_accept', 1);
                Session::put('route_url', route('add_sub_program', ['anchor_id' => $anchor_id, 'program_id' => $new_prgm_id, 'parent_program_id' => $parent_program_id, 'action' => $action, 'reason_type' => $reason_type, 'copied_prgm_id' => $program_id]));
                return redirect()->back();                
            }            

        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * View End Program Reason
     * 
     * @param Request $request
     * @return response
     */
    public function viewEndPrgmReason(Request $request)
    {
        $prgmId = (int) $request->get('program_id');
        
        $reason  = '';
        $comment = '';
        
        $reasonList = config('common.program_modify_reasons');
        
        $whereCond=[];
        $whereCond['prgm_id'] = $prgmId;
        $prgms = $this->appRepo->getSelectedProgramData($whereCond);
        if (isset($prgms[0])) {
            $prgm = $prgms[0];
            $reason  = $reasonList[$prgm->modify_reason_type];
            $comment = $prgm->modify_reason;
        }
                        
        return view('backend.lms.view_end_program_reason')
            ->with('reason', $reason)
            ->with('comment', $comment);
    }
}
