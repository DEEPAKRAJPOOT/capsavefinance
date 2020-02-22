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
                                   <table id="invoiceListDisbursed" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
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
                                                <th>Action</th>
                                               
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




<div class="modal show" id="myModal5" style="display: none;">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Repayment Details | Invoice Number : INV-112</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="listing-modal repayment-form-modal mb-3">
                                    <ul>
                                        <li>
                                            <div class="listing-modal-left"> Repay count: </div>
                                            <div class="listing-modal-right"> <span id="repay_count">1</span></div>
                                            <span id="overdue_percentage_raw" hidden="">16</span>
                                            <span id="remaining_overdue_raw" hidden="">110000.1328125</span>
                                        </li>
                                        <li>
                                            <!-- <span style="display:none" id="repay_count"></span> -->
                                            <div class="listing-modal-left"> Invoice Approved Amount (₹): </div>
                                            <div class="listing-modal-right"> <span id="invoice_approved_amount">₹60,000.00</span></div>
                                        </li>

                                        <li>
                                            <div class="listing-modal-left"> Funded Amount (₹): </div>
                                            <div class="listing-modal-right"> <span id="funded_amount">₹56,000.00</span></div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left"> Final Funded Amount (₹): </div>
                                            <div class="listing-modal-right"> <span id="final_funded_amount_repay">₹48146.00</span></div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left"> Funded Date:  </div>
                                            <div class="listing-modal-right"> <span id="funded_date_show">13-Dec-2019</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left"> Tenor (in days): </div>
                                            <div class="listing-modal-right"> <span id="term_days">90</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left"> Payment Due Date: </div>
                                            <div class="listing-modal-right"> <span id="payment_due_date" payment_due_date_raw="2019-10-17">14-March-2020</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Interest Per Annum (%): </div>
                                            <div class="listing-modal-right"> <span id="interest_percentage">12</span><span> %</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Processing Fee (%): </div>
                                            <div class="listing-modal-right"> <span id="processing_fee_repay">1</span><span> %</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Discount Type: </div>
                                            <div class="listing-modal-right"> <span id="discount_type">front end</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Grace period (in days): </div>
                                            <div class="listing-modal-right"> <span id="penal_grace">0</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Penal Interest Per Annum (%): </div>
                                            <div class="listing-modal-right"> <span id="penal_interest">0</span><span> %</span>
                                            </div>
                                        </li>
                                      
                                        <li>
                                            <div class="listing-modal-left">Repayment Amount: </div>
                                            <div class="listing-modal-right"> <span id="repayment_amount">₹0</span>
                                                
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Total Amount Repaid: </div>
                                            <div class="listing-modal-right"> <span id="already_repaid_amount">₹0</span></div>
                                          
                                        </li>  
                                         <li>
                                            <div class="listing-modal-left">Penal days: </div>
                                            <div class="listing-modal-right"> <span id="penal_days">41</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Penalty Amount: </div>
                                            <div class="listing-modal-right"> <span id="penalty_amount">₹0</span>
                                            </div>
                                        </li>

                                        
                                        
                                        <li>
                                            <div class="listing-modal-left">Principal Amount: </div>
                                            <div class="listing-modal-right"> <span id="remaining_overdue">₹60,000</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="listing-modal-left">Total Amount to Repay: </div>
                                            <div class="listing-modal-right"> <span id="remaining_repay_amount">₹0</span></div>
                                        </li>
                                        
                                        
                                       
                                    </ul>
                                </div>
								<div class="row">
								<div class="col-md-6">
								<div class="form-group">
                                    <label for="repaid_amount" class="form-control-label">Repayment Date :</label>
                                <input type="date" class="form-control " value="">
                                </div>
                                </div>
								
								<div class="col-md-6">
								<div class="form-group">
                                    <label for="repaid_amount" class="form-control-label">Repayment Amount :</label>
                                <input type="date" class="form-control " value="">
                               </div>
                                </div>
								<div class="col-md-6">
								<div class="form-group">
                                    <label for="repaid_amount" class="form-control-label">Payment Type :</label>
                               <select class="form-control">
                                        <option value=""> Select Payment Type </option>
                                        <option value="1"> Online RTGS/NEFT </option>
                                        <option value="2"> Cheque</option>
                                        <option value="3"> Other </option>
                                    </select>
                               </div>
                                </div>
								<div class="col-md-6">
								<div class="form-group">
                                    <label for="repaid_amount" class="form-control-label">Upload Documents :</label>
                                <input type="file" class="form-control " value="">
                               </div>
                                </div>
								<div class="col-md-12">
                                    <label for="repaid_amount" class="form-control-label">Comment : </label>
                               <textarea class="form-control" cols="4" rows="4"></textarea>
                               
                                </div>
								</div>
			     <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Save</button> 
				 <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
            
         </div>
      </div>
   </div>
</div>
    {!!Helpers::makeIframePopup('modalInvoiceDisbursed','Invoice Success Status', 'modal-lg')!!}
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
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment.js') }}"></script>

@endsection
 