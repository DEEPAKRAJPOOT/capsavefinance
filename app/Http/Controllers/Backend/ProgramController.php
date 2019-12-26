<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\ProgramRequest;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class ProgramController extends Controller {

    protected $userRepo;
    protected $appRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $app_repo)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
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
        if (empty($anchor_id)) {
            abort(400);
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
            if (empty($anchor_id)) {
                abort(400);
            }
            $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->toArray();
            $anchorList = array_reduce($anchorData, function ($output, $element) {
                $output[$element['user_id']] = $element['f_name'];
                return $output;
            }, []);
            $industryList = $this->appRepo->getIndustryDropDown()->toArray();
            return view('backend.lms.add_program', compact('anchorList', 'industryList', 'anchor_id'));
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
            $anchor_id = $request->get('anchor_id');
            $prepareDate = [
                'anchor_id' => $anchor_id,
                'anchor_user_id' => $request->get('anchor_user_id'),
                'prgm_name' => $request->get('prgm_name'),
                'prgm_type' => $request->get('prgm_type'),
                'industry_id' => $request->get('industry_id'),
                'sub_industry_id' => $request->get('sub_industry_id'),
                'anchor_limit' => $request->get('anchor_limit'),
                'is_fldg_applicable' => $request->get('is_fldg_applicable'),
                'status' => 1
            ];
            $this->appRepo->saveProgram($prepareDate);
            \Session::flash('message', trans('success_messages.program_save_successfully'));
            return redirect()->route('manage_program', ['anchor_id' => $anchor_id]);
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
            return view('backend.lms.show_sub_program', compact('anchor_id', 'program_id'));
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
            $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->first();
            $programData = $this->appRepo->getSelectedProgramData(['prgm_id' => $program_id], ['anchor_limit', 'prgm_type', 'anchor_user_id'])->first();
            $preSanction = $this->appRepo->getDocumentList(['doc_type_id' => 2, 'is_active' => 1])->toArray();
            $postSanction = $this->appRepo->getDocumentList(['doc_type_id' => 1, 'is_active' => 1])->toArray();
            $charges = $this->appRepo->getChargesList()->toArray();
            $anchorSubLimitTotal = $this->appRepo->getSelectedProgramData(['parent_prgm_id' => $program_id], ['anchor_sub_limit'])->sum('anchor_sub_limit');

            $remaningAmount = null;
            if (isset($programData->anchor_limit)) {
                $remaningAmount = $programData->anchor_limit - $anchorSubLimitTotal;
            }


            return view('backend.lms.add_sub_program',
                    compact(
                            'anchor_id',
                            'anchorData',
                            'programData',
                            'program_id',
                            'postSanction',
                            'preSanction',
                            'charges',
                            'remaningAmount'
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
            'anchor_user_id' => $request->get('anchor_user_id'),
            'product_name' => $request->get('product_name'),
            'interest_rate' => $request->get('interest_rate'),
            'anchor_sub_limit' => $request->get('anchor_sub_limit'),
            'anchor_limit' => $request->get('anchor_limit'),
            'min_loan_size' => $request->get('min_loan_size'),
            'max_loan_size' => $request->get('max_loan_size'),
            'interest_linkage' => $request->get('interest_linkage'),
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
            'status' => 1,
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
            $gst_rate = $request->get('gst_rate');
            foreach ($charge as $keys => $values) {
                $out[] = [
                    'prgm_id' => $program_id,
                    'chrg_applicable_id' => $values,
                    'chrg_calculation_amt' => isset($chrg_calculation_amt[$keys]) ? $chrg_calculation_amt[$keys] : null,
                    'gst_rate' => isset($gst_rate[$keys]) ? $gst_rate[$keys] : null,
                    'chrg_calc_min_rate' => isset($chrg_calc_min_rate[$keys]) ? $chrg_calc_min_rate[$keys] : null,
                    'chrg_calc_max_rate' => isset($chrg_calc_max_rate[$keys]) ? $chrg_calc_max_rate[$keys] : null,
                    'chrg_tiger_id' => isset($chrg_calc_max_rate[$keys]) ? $chrg_tiger_id[$keys] : null,
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
    public function saveSubProgram(Request $request)
    {

        try {
            $user_id = \Auth::user()->user_id;
            $dataForProgram = $this->prepareSubProgramData($request);

            /**
             * save program data
             */
            $lastInsertId = $this->appRepo->saveProgram($dataForProgram);

            /**
             * Save program charges data 
             */
            $this->saveProgramCharges($request, $lastInsertId);
            $pre = !empty($request->get('pre_sanction')) ? $request->get('pre_sanction') : [];
            $post = !empty($request->get('post_sanction')) ? $request->get('post_sanction') : [];

            $pre_sanction = array_filter($pre);
            $post_sanction = array_filter($post);

            $preSanctionData = array_reduce($pre_sanction, function($outinvoice_upload, $element) use($lastInsertId, $user_id) {
                $out[] = [
                    'user_id' => \Auth::user()->user_id,
                    'prgm_id' => $lastInsertId,
                    'doc_id' => (int) $element,
                    'wf_stage_id' => 10,
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

            \Session::flash('message', trans('success_messages.sub_program_save_successfully'));
            return redirect()->route('manage_sub_program', ['anchor_id' => $request->get('anchor_id'), 'program_id' => $request->get('parent_prgm_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}
