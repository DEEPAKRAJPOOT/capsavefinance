<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\BankAccountRequest;
use App\Http\Requests\Master\CompanyRegRequest;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class CompanyController extends Controller {
    
    protected $appRepo;
    protected $masterRepo;

    public function __construct(InvMasterRepoInterface $master, InvAppRepoInterface $app_repo) {
        $this->middleware('auth');
        $this->masterRepo = $master;
        $this->appRepo = $app_repo;
    }

    public function index(Request $request) {
        $filter =   [];
        $search_keyword = $request->get('search_keyword');
        $filter['search_keyword'] =   '';
        
        if($search_keyword!='' && $search_keyword!=null){
             $filter['search_keyword'] =$search_keyword;
        }
        $companiesList = $this->masterRepo->getAllCompanies($filter)->toArray();
//        dd($companiesList);
        return view('master.companies.index',['cmpData' => $companiesList]);
    }

    public function addCompanyForm(Request $request) {

        $company_data = [];
        $company_data['is_active'] = 2;
        $company_data['state']['name'] = '';
        $company_data['is_reg'] = 0;
        if (!empty($request->get('id'))) {
            $company_id = preg_replace('#[^0-9]#', '', $request->get('id'));
            $company_data = $this->masterRepo->findCompanyById($company_id);
//            dd($company_data);
        }
         $get_state_list  =  $this->masterRepo->getAddStateList();
        return view('master.companies.add_companies', ['comData' => $company_data, 'state' => $get_state_list ]);
    }

    public function saveCompanies(CompanyRegRequest $request) {
        try {
            $arrCompaniesData = $request->all();
            $arrCompaniesData['charge_prefix'] = strtoupper($request->get('charge_prefix'));
            $arrCompaniesData['interest_prefix'] = strtoupper($request->get('interest_prefix'));
            // dd($request->all());
            $status = false;
            $company_id = false;
            if($request->get('is_reg') == 1){
                $comp_name = trim($request->get('cmp_name'));
                $is_reg = (int)$request->get('is_reg');
                $data = $this->masterRepo->checkIsRegCompany($comp_name,$is_reg);
                $regComData = $data ? $data->toArray() : '';
//                dd($regComData);
                if (!empty($regComData)) {
                    $company_id = $regComData['comp_addr_id'];
                    $regComData['is_reg'] = 0;
//                    dd($regComData);
                    $status = $this->masterRepo->updateCompanies($regComData, $company_id);
                }
            }
            
            if (!empty($request->get('comp_addr_id'))) {
                $company_id = $request->get('comp_addr_id');
                $companies_data = $this->masterRepo->findCompanyById($company_id);
                if (!empty($companies_data)) {
                    $arrCompaniesData['updated_by'] = Auth::user()->user_id;
                    $status = $this->masterRepo->updateCompanies($arrCompaniesData, $company_id);
                }
            } else {
                $arrCompaniesData['company_id'] = 1;
                $arrCompaniesData['created_by'] = Auth::user()->user_id;
//                dd($arrCompaniesData);
                $status = $this->masterRepo->saveCompanies($arrCompaniesData);
            }
            if ($status) {
                Session::flash('message', $company_id ? trans('master_messages.company_edit_success') : trans('master_messages.company_add_success'));
                return redirect()->route('get_companies_list');
            } else {
                $companiesList = $this->masterRepo->getAllCompanies();
                Session::flash('error', trans('master_messages.something_went_wrong'));
                return redirect()->route('get_industries_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * add company bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addCompanyBankAccount(Request $request)
    {
        try {
//           dd($request->all());
            $bankAccount = [];
            $comp_id = $request->get('comp_addr_id');
            $bank_acc_id = false;
            $bankAccount['is_default'] = 0;
            if (!empty($request->get('bank_account_id'))) {
                $bank_acc_id = preg_replace('#[^0-9]#', '', $request->get('bank_account_id'));
                $bankAccount = $this->appRepo->getBankAccountDataByCompanyId($bank_acc_id,$comp_id)->first();
            }
            
            $bank_list = ['' => 'Please Select'] + $this->masterRepo->getBankList()->toArray();
            return view('master.companies.add_company_bank_account')
                            ->with(['bank_list' => $bank_list, 'companyId' => $comp_id, 'bankAccount' => $bankAccount]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save company bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveCompanyBankAccount(BankAccountRequest $request)
    {
        try {
            $by_default = ($request->get('by_default')) ? ((int)$request->get('by_default')) : 0;
            $bank_acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
            $compId = ($request->get('comp_addr_id')) ? \Crypt::decrypt($request->get('comp_addr_id')) : null;
        //    dd($request->all(),$compId,$bank_acc_id,$by_default);
            
            $prepareData = [
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'micr_code' => $request->get('micr_code'),
                'acc_type' => $request->get('acc_type'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active'),
                'user_id' => auth()->user()->user_id,
                'comp_addr_id' => $compId,
                'is_default' => $by_default,
                'sponser_bank_code' => $request->get('sponser_bank'),
            ];
//            dd($prepareData);
            if($by_default == 1){
                $companyIdsArr = null;
                $companiesArr = $this->masterRepo->getCompNameByCompId((int)$compId);
//                dd($companiesArr);
                foreach($companiesArr as $key => $value){
                    $companyIdsArr[$key] = $value->comp_addr_id;
                }
//                dd($companyIdsArr);
                $data = $this->appRepo->isDefalutCmpBankAcc($companyIdsArr, $by_default);
                $result = $data ? $data->toArray() : '';
//                dd($result);
                if(!empty($result)){
//                    dd($result);
                    $prev_def_acc_id = $result['bank_account_id'];
                    $result['is_default'] = 0;
                    $this->appRepo->saveBankAccount($result, $prev_def_acc_id);
                }
            }

            $this->appRepo->saveBankAccount($prepareData, $bank_acc_id);
            $messges = $bank_acc_id ? trans('success_messages.update_bank_account_successfully') : trans('success_messages.save_bank_account_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}
