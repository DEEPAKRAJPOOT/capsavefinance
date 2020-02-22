@extends('layouts.app')
@section('content')
<div class="content-wrapper">
<div class="col-md-12 ">
   <section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-2">Manage Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Manage Invoice</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin">

   <div class="col-md-12 ">
      <div class="card">
         <div class="card-body">
<ul class="nav nav-tabs" role="tablist">
           <li class="nav-item ">
      <a class="nav-link @if(Route::currentRouteName()=='get_invoice') active @endif"  href="{{Route('get_invoice')}}">Pending</a>
    </li>
    <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_approve_invoice') active @endif"  href="{{Route('get_approve_invoice')}}">Approved</a>
    </li>
  <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_disbursed_que_invoice') active @endif"  href="{{Route('get_disbursed_que_invoice')}}">Disbursement Queue</a>
    </li>
        
   <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='get_sent_to_bank') active @endif" href="{{Route('get_sent_to_bank')}}">Sent to Bank</a>
    </li>
	<li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='get_failed_disbursed_invoice') active @endif" href="{{Route('get_failed_disbursed_invoice')}}">Failed Disbursement</a>
    </li>
   <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_disbursed_invoice') active @endif"  href="{{Route('get_disbursed_invoice')}}">Disbursed</a>
    </li>
	<li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_repaid_invoice') active @endif" href="{{Route('get_repaid_invoice')}}">Repaid</a>
    </li>
   <li class="nav-item">
      <a class="nav-link @if(Route::currentRouteName()=='get_reject_invoice') active @endif" href="{{Route('get_reject_invoice')}}">Reject</a>

    </li>
  
   
  </ul>
  <div class="tab-content">
    
    <div id="menu1" class=" active tab-pane "><br>

       
    <div class="card">
        <div class="card-body">
                     <div class="row"><div class="col-md-6"></div>
                 <div class="col-md-2">				 
                     <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                     <select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
                           <option value="">Select Application  </option>
                           @foreach($get_bus as $row)
                           <option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
                           @endforeach
                          
                        
                  </select>
                     <span id="anchorMsg" class="error"></span>
                  
                   </div>
               <div class="col-md-2">				 
                                                              
                    <select class="form-control form-control-sm changeAnchor searchbtn"  name="search_anchor">
                           <option value="">Select Anchor  </option>
                           @foreach($anchor_list as $row)
                           <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                           @endforeach
                          
                        
                  </select>
                 
                   </div>
             <div class="col-md-2">		    
                                                            
                 <select readonly="readonly" class="form-control form-control-sm searchbtn" id="supplier_id" name="search_supplier">
                         
                    </select>
                     </div>    
                 
            </div>
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="hidden" name="front" value="front">
                                    <table id="invoiceListDisbursedQue" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>Invoice No</th> 
                                               <th>Anchor Name</th>
                                                 <th>Customer Name</th>
                                                <th>Program Name</th>
                                                <th>Invoice Date</th>
                                                   <th>Tenor</th>
                                                 <th>Invoice  Amount</th>
                                                <th>Invoice Approve Amount</th>
                                                <th>Status</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
         
    </div>
	
	
  
  </div>
        
           
         </div>
      </div>
   </div>
</div></div>




<div class="modal align-middle" id="myModal6" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Upload Invoices</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body ">
		 <form id="signupForm">
		 <div class="row">
		 <div class="col-md-6">
		<div class="form-group">
        <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span></label>
        <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id"  name="anchor_bulk_id">
                                             
                <option value="">Select Anchor  </option>
                @foreach($anchor_list as $row)
                <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                @endforeach
                                             </select>
                                             <span id="anc_limit"></span>
                
                </div></div>
		
		 <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtCreditPeriod">Product Program Name
                            <span class="error_message_label">*</span>
                        </label>
                         <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" name="supplier_bulk_id">
                                            </select>
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                                   <span id="pro_limit"></span>
               </div>
		</div>
            <div class="col-md-6">
            <div class="form-group">
            <label for="txtCreditPeriod">Customer Name <span class="error_message_label">*</span></label>
            <select readonly="readonly" class="form-control" id="supplier_bulk_id" name="supplier_bulk_id">
            </select>
            <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
            </div>
            </div>
                                    
									
									
			<div class="clearfix">
			</div>
			</div>	
                     <h5 id="submitInvoiceMsg" class="text-success"></h5>
                     <button type="submit" id="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Upload</button> 
				 <button type="reset" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 	
				 
				 </form>
            
         </div>
      </div>
   </div>
</div>
</div>
<div class="modal show" id="myModal7" style="display: none;">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
        
         <div class="modal-header">
			<h5>Confirm Invoice Approved Amount</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
        
         <div class="modal-body text-left">
             <form id="signupFormNew"  action="{{Route('update_invoice_amount')}}" method="post">
		@csrf	
                 <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Invoice Amount
                                 <span class="mandatory">*</span>
                                 </label>
                                  <input type="text" class="form-control" id="invoice_amount" value="" disabled="">
                                  <input type="hidden" name="invoice_id" id="invoice_id">
                              </div>
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Invoice Approved Amount
                                 <span class="mandatory">*</span>
                                 </label>
			      <input type="text" class="form-control" id="invoice_approve_amount" name="approve_invoice_amount" value="Enter Amount">
                                 
                              </div>
                           </div>
						   
						  

                        </div>
             <span class="model7msg error"></span>			
             <input type="submit" id="UpdateInvoiceAmount" class="btn btn-success float-right btn-sm mt-3" value="Submit"> 
             </form> 
         </div>
      </div>
   </div>
</div>
    @endsection
    @section('jscript')
<style>
    .image-upload > input
   {
      display: none;
   }

   .image-upload i
   {
      width: 80px;
      cursor: pointer;
   }
    .itemBackground 
    { 
      border: 2px solid blanchedalmond;  
      background-color:#5c9742;
    }
     .itemBackgroundColor 
    { 
      color:white;
    }
</style>    
<script>  
       var messages = {
        frontend_get_invoice_list_approve: "{{ URL::route('frontend_get_invoice_list_approve') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_user_program_supplier: "{{ URL::route('get_user_program_supplier') }}",
        get_user_biz_anchor: "{{ URL::route('get_user_biz_anchor') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
        invoice_document_save: "{{ URL::route('invoice_document_save') }}",
        update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
        token: "{{ csrf_token() }}",
    };</script>
<script src="{{ asset('frontend/js/ajax-js/invoiceAjax.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment_que.js') }}"></script>

@endsection
 