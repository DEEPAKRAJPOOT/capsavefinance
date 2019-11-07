<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PartnerFormRequest;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Http\Requests\DocumentInformationRequest;
use Session;

class DocumentController extends Controller
{
    protected $DocumentRepo;

    public function __construct(InvDocumentRepoInterface $buss_repo){
    	$this->DocumentRepo = $buss_repo;
    }

    /**
     * Show the Document information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.document.bank-document');
    }

    public function saveDocument(DocumentRequest $request)
    {
        try {
            $arrFileData = $request->all();
            $document_info = $this->documentRepo->saveDocumentInfo($arrFileData,1);//Auth::user()->id
            if ($document_info) {
                Session::flash('message',trans('success_messages.basic_saved_successfully'));
                return redirect()->route('authorized_signatory_open');
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}