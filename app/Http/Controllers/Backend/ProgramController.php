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
            if ($anchor_id) {
                $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->toArray();
            } else {
                $anchorData = $this->userRepo->getAllAnchor('comp_name')->toArray();
            }

            $anchorList = array_reduce($anchorData, function ($output, $element) {
                $output[$element['anchor_id']] = $element['f_name'];
                return $output;
            }, []);

            if (\Session::has('is_mange_program')) {
                $anchorList = ['' => 'Please Select'] + $anchorList;
            }

            $productItem = $this->master->getProductDataList()->toArray();
            foreach ($productItem as $key => $value) {
                if($key == 1){
                    $product[$key] = $value;
                }
            }
            $productList = ['' => 'Please Select'] + $product;
            $redirectUrl = (\Session::has('is_mange_program')) ? route('manage_program') : route('manage_program', ['anchor_id' => $anchor_id]);
            $industryList = $this->appRepo->getIndustryDropDown()->toArray();
            return view('backend.lms.add_program', compact('anchorList', 'industryList', 'anchor_id', 'redirectUrl', 'productList'));
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
            $programInfo = $this->appRepo->getProgramByProgramName($prgm_name);
//            dd(count($programInfo));
            if(count($programInfo)<=0){
                $prepareDate = [
                    'anchor_id' => $anchor_id,
                    //   'anchor_user_id' => $request->get('anchor_user_id'),
                    'prgm_name' => $request->get('prgm_name'),
                    'industry_id' => $request->get('industry_id'),
                    'sub_industry_id' => $request->get('sub_industry_id'),
                    'anchor_limit' => ($request->get('anchor_limit')) ? str_replace(',', '', $request->get('anchor_limit')) : null,
                    'is_fldg_applicable' => $request->get('is_fldg_applicable'),
                    'product_id'=>$request->get('product_id'),
                    'status' => 1
                ];
                $this->appRepo->saveProgram($prepareDate);
                \Session::flash('message', trans('success_messages.program_save_successfully'));
                $redirect = redirect()->route('manage_program', ['anchor_id' => $anchor_id]);
                if (\Session::has('is_mange_program')) {
                    $redirect = redirect()->route('manage_program');
                }
                return $redirect;
            }else{
                 \Session::flash('error', trans('success_messages.program_already_exist'));
                 return redirect()->back()->withInput();
            }
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
//            dd($anchor_id,$program_id);
            \Session::put('list_program_id', $program_id);

            $is_prg_list = $redirectUrl = (\Session::has('is_mange_program')) ? route('manage_program') : route('manage_program', ['anchor_id' => $anchor_id]);
            return view('backend.lms.show_sub_program', compact('anchor_id', 'program_id', 'redirectUrl'));
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
            
            if ($request->has('parent_program_id')) {  //Edit Sub Program
                $parent_program_id = (int) $request->get('parent_program_id');
                $whereCond = ['sub_program_id_nte' => $program_id];
            } else {
                $parent_program_id = $program_id;
                $whereCond = [];
            }
            $action = $request->get('action');
            $subProgramData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id, 'is_null_parent_prgm_id' => true], ['*'], ['programDoc', 'programCharges'])->first();
//            dd($subProgramData);
            $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->first();
//          dd($program_id);
            $programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['*'], ['programDoc', 'programCharges'])->first();
//            dd($programData);
            $preSanction = $this->appRepo->getDocumentList(['doc_type_id' => 2, 'is_active' => 1])->toArray();
            $postSanction = $this->appRepo->getDocumentList(['doc_type_id' => 3, 'is_active' => 1])->toArray();
            $charges = $this->appRepo->getChargesList()->toArray();

            $anchorSubLimitTotal = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $parent_program_id]+$whereCond, ['anchor_sub_limit'])->sum('anchor_sub_limit');            
            $baserate_list =$this->master->getBaseRateDropDown();
//            dd($baserate_list);
            
            $remaningAmount = null;
            if (isset($programData->anchor_limit)) {
                $remaningAmount = $programData->anchor_limit - $anchorSubLimitTotal;
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
                            'baserate_list'
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
            'anchor_sub_limit' => ($request->get('anchor_sub_limit')) ? str_replace(',', '', $request->get('anchor_sub_limit')) : null,
            'anchor_limit' => $request->get('anchor_limit'),
            'min_loan_size' => ($request->get('min_loan_size')) ? str_replace(',', '', $request->get('min_loan_size')) : null,
            'max_loan_size' => ($request->get('max_loan_size')) ? str_replace(',', '', $request->get('max_loan_size')) : null,
            'base_rate_id' => $request->get('interest_linkage'),
            'interest_borne_by' => $request->get('interest_borne_by'),
            'margin' => $request->get('margin'),
            'is_adhoc_facility' => $request->get('is_adhoc_facility'),
            'adhoc_interest_rate' => $request->get('adhoc_interest_rate'),
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
                    'chrg_applicable_id' => isset($chrg_tiger_id[$keys]) ? $chrg_tiger_id[$keys] : null,
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

}
