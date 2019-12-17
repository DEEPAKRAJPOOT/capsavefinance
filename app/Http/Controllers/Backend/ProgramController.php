<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use Illuminate\Http\Request;

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
        return view('backend.anchor.show_mange_program')->with(['anchor_id' => $anchor_id]);
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
            $anchorData = $this->userRepo->getAnchorDataById($anchor_id)->toArray();
            $anchorList = array_reduce($anchorData, function ($output, $element) {
                $output[$element['user_id']] = $element['f_name'];
                return $output;
            }, []);
            $industryList = $this->appRepo->getIndustryDropDown()->toArray();
            return view('backend.anchor.add_program', compact('anchorList', 'industryList', 'anchor_id'));
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    public function saveProgram(Request $request)
    {
        try {
            $anchor_id = $request->get('anchor_id');
            $prepareDate = [
                'anchor_id' => $anchor_id,
                'anchor_user_id' => $request->get('anchor_user_id'),
                'prgm_name' => $request->get('prgm_name'),
                'prgm_detail' => $request->get('prgm_detail'),
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

}
