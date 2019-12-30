<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\InvoiceInterface as InvoiceInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use App\Libraries\Pdf;
use Carbon\Carbon;

class InvoiceController extends Controller {

    protected $invRepo;
    protected $docRepo;
    public function __construct(InvoiceInterface $invRepo, InvDocumentRepoInterface $docRepo) {
        $this->invRepo = $invRepo;
        $this->docRepo = $docRepo;
        $this->middleware('auth');
        //$this->middleware('checkBackendLeadAccess');
    }

    /* Invoice upload page  */

  
    public function getInvoice(Request $request) {
        $anchor_id = $request->anchor_id;
        $user_id = $request->user_id;
        $app_id = $request->app_id;
        $biz_id = $request->biz_id;
        $get_user = $this->invRepo->getUser($user_id);
        $get_anchor = $this->invRepo->getAnchor($anchor_id);
        $get_program = $this->invRepo->getProgram($anchor_id);
        return view('frontend.application.invoice.uploadinvoice')
                   ->with(['get_user' => $get_user, 'get_anchor' => $get_anchor, 'get_program' => $get_program, 'app_id' => $app_id,'biz_id' => $biz_id]);
    }

      public function viewInvoice() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
      
       public function viewApproveInvoice() {
         $getAllInvoice    =   $this->invRepo->getAllAnchor();
          $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.approve_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
       public function viewDisbursedInvoice() {
         $getAllInvoice    =   $this->invRepo->getAllAnchor();
          $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.disbursed_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
       public function viewRepaidInvoice() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.repaid_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }

    
    /* get suplier & program b behalf of anchor id */
      public function getProgramSupplier(Request $request){
       $attributes = $request->all();
       $invId   = $attributes['anchor_id'];
       $get_user  =   $this->invRepo->getUserBehalfAnchor($invId);
       return response()->json(['status' => 1,'userList' =>$get_user]);

      }
     /*   save invoice */

    public function saveInvoice(Request $request) {
        $attributes = $request->all();
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        $appId = $request->app_id;
        $uploadData = Helpers::uploadAppFile($attributes, $appId);
        $userFile = $this->docRepo->saveFile($uploadData);
       
        $arr = array('anchor_id' => $attributes['anchor_id'],
            'supplier_id' => $attributes['supplier_id'],
            'program_id' => $attributes['program_id'],
            'invoice_no' => $attributes['invoice_no'],
            'invoice_date' => ($attributes['invoice_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_date'])->format('Y-m-d') : '',
            'invoice_approve_amount' => $attributes['invoice_approve_amount'],
            'remark' => $attributes['remark'],
            'file_id'  =>$userFile->file_id,
            'created_by' => $id,
            'created_at' => $date);
        $result = $this->invRepo->save($arr);

        if ($result) {


            Session::flash('message', 'Invoice successfully saved');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Invoice is not saved');
            return back();
        }
    }

}
