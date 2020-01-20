<?php

namespace App\Http\Controllers\Lms;

use File;
use Session;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;

class BankAccountController extends Controller {

    //  use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $storage;

    /**
     * The pdf instance.
     *
     * @var Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, MasterInterface $master, StorageManagerInterface $storage)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->master = $master;
        $this->storage = $storage;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * bank account list
     * 
     * @return type mixed
     */
    public function bankAccountList(Request $request)
    {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            return view('lms.customer.bank_account_list')->with(['userInfo' => $userInfo]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * add bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addBankAccount(Request $request)
    {
        try {
            $acc_id = $request->get('bank_account_id');
            $bankAccount = $this->appRepo->getBankAccountData(['bank_account_id' => $acc_id])->first();
            $bank_list = ['' => 'Please Select'] + $this->master->getBankList()->toArray();
            return view('lms.customer.add_bank_account')
                            ->with(['bank_list' => $bank_list, 'bankAccount' => $bankAccount]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveBankAccount(BankAccountRequest $request)
    {
        try {
            $acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
            $prepareData = [
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active'),
                'user_id' => auth()->user()->user_id
            ];

            $lastInsertId = $this->appRepo->saveBankAccount($prepareData, $acc_id);
            $this->uploadBankDoc($request, $lastInsertId);
            $messges = $acc_id ? trans('success_messages.update_bank_account_successfully') : trans('success_messages.save_bank_account_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * upload bank Doc
     * 
     * @param array $attr
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    function uploadBankDoc($attr, $id)
    {
        try {
            foreach ($attr->file() as $files):
                $docname = $files->getClientOriginalName();
                $ext = $files->getClientOriginalExtension();
                $fileSize = $files->getClientSize();
                if ($fileSize < config('common.MAX_UPLOAD_SIZE')) {
                    $userBaseDir = 'appDocs/Document/bankDoc/' . auth()->user()->user_id;
                    $userFileName = rand(0, 9999) . time() . '.' . $ext;
                    $pathName = $files->getPathName();
                    $this->storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($pathName));
                    $doc = [
                        'doc_name' => $userFileName,
                        'doc_ext' => $ext,
                        'enc_id' => md5(rand(1, 9999)),
                    ];
                    $this->appRepo->saveBankAccount($doc, $id);
                    File::delete($pathName);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                }
            endforeach;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
