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
use DB;
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
       return view('backend.application.invoice.uploadinvoice')
                   ->with(['get_user' => $get_user, 'get_anchor' => $get_anchor, 'get_program' => $get_program, 'app_id' => $app_id,'biz_id' => $biz_id]);
    }
    
     public function getAllInvoice()
    {
      
        $get_anchor = $this->invRepo->getLimitAllAnchor();
         return view('backend.invoice.upload_all_invoice')
         ->with(['get_anchor' => $get_anchor]);
  
    }

      public function viewInvoice() {
       
         $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
      
      public function getBulkInvoice() {
         $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();  
          return view('backend.invoice.bulk_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
        
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
      
      
        public function viewSentToBankInvoice() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.sent_to_bank')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
        public function viewfailedDisbursment() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.failed_disbursment')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
      
        public function viewdisbursed() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.disbursment')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
        public function viewRejectInvoice() {
        $getAllInvoice    =   $this->invRepo->getAllAnchor();
         $get_bus = $this->invRepo->getBusinessName();
        return view('backend.invoice.reject_invoice')->with(['get_bus' => $get_bus, 'anchor_list'=> $getAllInvoice]);
                
      }
       
    
    /* get suplier & program b behalf of anchor id */
      public function getProgramSupplier(Request $request){
       $attributes = $request->all();
       $invId   = $attributes['anchor_id'];
       $get_user  =   $this->invRepo->getUserBehalfAnchor($invId);
       return response()->json(['status' => 1,'userList' =>$get_user]);

      }
      
      /* failed invoice status iframe    */
       public function invoiceFailedStatus(Request $request){
           dd( $request->invoice_id);
         return view('backend.invoice.invoice_failed_status');
      }
      
       /* success invoice status iframe    */
       public function invoiceSuccessStatus(Request $request){
        
         return view('backend.invoice.invoice_success_status');
      }
      
       /* success invoice status iframe    */
       public function viewInvoiceDetails(Request $request){
          $invoice_id =  $request->get('invoice_id');  
          $res =  $this->invRepo->getSingleInvoice($invoice_id);
          $get_status  = DB::table('mst_status')->where('status_type',4)->get();
          return view('backend.invoice.view_invoice_details')->with(['invoice' => $res,'status' => $get_status]);
      }
     
   
      /* save bulk invoice */
      public function saveBulkInvoice(Request $request) { 
           $attributes = $request->all();
           $res =  $this->invRepo->saveBulk($attributes);
           if($res)
           {
                 
                  Session::flash('message', 'Invoice successfully saved');
                  return back();
           }
        else {
               Session::flash('message', 'Something wrong, Invoice is not saved');
               return back();
          }
          
      }
      /* update invoice amount  */
      public function saveInvoiceAmount(Request $request)
      {     $id = Auth::user()->user_id;
            $attributes = $request->all();
            $res =  $this->invRepo->updateInvoiceAmount($attributes);
           if($res)
           {
                
                  Session::flash('message', 'Invoice Amount successfully Updated');
                  return back();
           }
        else {
               Session::flash('message', 'Something wrong, Amount is not Updated');
               return back();
          }
      }
    /*   save invoice */

    public function saveInvoice(Request $request) {
        $attributes = $request->all();
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        
        if(isset($attributes['app_id']))
        {
            $appId = $attributes['app_id']; 
            $biz_id  = $attributes['biz_id'];
        }
        else {
           $res =  $this->invRepo->getSingleLimit($attributes['anchor_id']);
           $appId = $res->app_id; 
           $biz_id  = $res->biz_id;
        }
        $uploadData = Helpers::uploadAppFile($attributes, $appId);
        $userFile = $this->docRepo->saveFile($uploadData);
       
        $arr = array('anchor_id' => $attributes['anchor_id'],
            'supplier_id' => $attributes['supplier_id'],
            'program_id' => $attributes['program_id'],
            'app_id'    => $appId,
            'biz_id'  => $biz_id,
            'invoice_no' => $attributes['invoice_no'],
            'invoice_due_date' => ($attributes['invoice_due_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_due_date'])->format('Y-m-d') : '',
            'invoice_date' => ($attributes['invoice_date']) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_date'])->format('Y-m-d') : '',
            'invoice_approve_amount' => $attributes['invoice_approve_amount'],
            'invoice_amount' => $attributes['invoice_approve_amount'],
            'prgm_offer_id' => $attributes['prgm_offer_id'],
            'remark' => $attributes['remark'],
            'file_id'  =>$userFile->file_id,
            'created_by' => $id,
            'created_at' => $date);
           $result = $this->invRepo->save($arr);

        if ($result) {

            $this->invRepo->saveInvoiceActivityLog($result,7,null,$id);
            Session::flash('message', 'Invoice successfully saved');
            return back();
        } else {
            Session::flash('message', 'Something wrong, Invoice is not saved');
            return back();
        }
    }
    
}
