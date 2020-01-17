@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')



<div class="content-wrapper">
				
				

               
               <section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-3">View Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">View Invoice</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin mt-3">
   <div class=" col-md-12 col-sm-6 mb-4">
      <div class="card">
         <div class="card-body">
            
           <div class="row">
                        
                        
                     <div class="col-md-6">
                        <p class="mb-0"><b>Invoice No : </b>{{($invoice->invoice_no) ? $invoice->invoice_no : '' }}</p>
                     </div> 
                    
                  <div class="col-md-6">
                                     @php 
                                   $color  = ['7'=>"badge badge-warning",'8' => "badge badge-success",'9' =>"badge badge-success",'10' =>"badge badge-success",'11' => "badge badge-danger",'12' => "badge badge-danger",'13' =>"badge badge-success",'14' => "badge badge-danger"];
                                   @endphp
				   @foreach($status as $row)
                                   @if($row->id==$invoice->status_id)
                                    <button type="button" class="{{$color[$row->id]}} btn-sm float-right">{{$row->status_name}}
                                    </button>
                                    @endif
                                   @endforeach
  </div>   
                  </div>
         </div>
      </div>
   </div>
   
     <div class="  col-sm-6 mb-4">
      <div class="card h-100">
         <div class="card-body ">
            
           
					 <h5 class="sub-title mb-2">Customer/Supplier Details</h5>
					 
					 <ul class="p-0 m-0">
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Name </b></div>
                           <div class="supplier-right col-md-6">
                             {{($invoice->supplier->f_name) ? $invoice->supplier->f_name : '' }} {{($invoice->supplier->l_name) ? $invoice->supplier->l_name : '' }}
                           </div>
                        </li>
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Phone</b> </div>
                           <div class="supplier-right col-md-6">
                              {{($invoice->supplier->mobile_no) ? $invoice->supplier->mobile_no : '' }}
                           </div>
                        </li>
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Address</b> </div>
                           <div class="supplier-right col-md-6">
                              N/A
                           </div>
                        </li>
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"> <b>GST</b></div>
                           <div class="supplier-right col-md-6">
                            N/A
                           </div>
                        </li>
                        <li class="row ">
                           <div class="supplier-left col-md-6"><b>PAN</b></div>
                           <div class="supplier-right col-md-6">
                           N/A
                           </div>
                        </li>
                       
                     </ul>
         </div>
      </div>
   </div>
   
   
   <div class="  col-sm-6 mb-4">
      <div class="card">
         <div class="card-body">
            
           
					 <h5 class="mb-2 sub-title">Anchor Details</h5>
					 
					 <ul class="p-0 m-0">
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Name </b></div>
                           <div class="supplier-right col-md-6">
                          {{($invoice->anchor->comp_name) ? $invoice->anchor->comp_name : '' }}
                           </div>
                        </li>
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Phone</b> </div>
                           <div class="supplier-right col-md-6">
                               {{($invoice->anchor->comp_phone) ? $invoice->anchor->comp_phone : '' }}
                           </div>
                        </li>
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>GST</b> </div>
                           <div class="supplier-right col-md-6">
                            N/A
                           </div>
                        </li>
                     
                        <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>PAN</b></div>
                           <div class="supplier-right col-md-6">
                            N/A
                           </div>
                        </li>
						 <li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>Street</b></div>
                           <div class="supplier-right col-md-6">
                        {{($invoice->anchor->comp_addr) ? $invoice->anchor->comp_addr : '' }}
                           </div>
                        </li>
						<li class="row mb-2">
                           <div class="supplier-left col-md-6"><b>City</b></div>
                           <div class="supplier-right col-md-6">
                       {{($invoice->anchor->comp_city) ? $invoice->anchor->comp_city : '' }}
                           </div>
                        </li>
                       
                     </ul>
         </div>
      </div>
   </div>
   
   <div class=" col-md-12 col-sm-6 mb-4">
      <div class="card">
         <div class="card-body">
            <h4><small>Invoice Details</small></h4>
			
			
			
			 <table class="table table-striped dataTable no-footer overview-table" cellspacing="0" cellpadding="0">
                        <thead>
                           <tr>
                            
                              <th>Invoice Amount (₹)</th>
                              <th>Invoice Approved Amount (₹)</th>
                              <th>Issue Date</th>
                              <th>Credit Days</th> 
                              
                              
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                            
                              <td id="invoice-amount">
                                 {{($invoice->invoice_amount) ? $invoice->invoice_amount : '' }}
                              </td>
                              <td id="invoice-amount">
                                    {{($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount : '' }} <a href="#" data-toggle="modal" data-target="#myModal2"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								 
                              </td>
                              
                              
                              
                              
                              <td id="invoice-date">
                                 {{($invoice->invoice_date) ? $invoice->invoice_date : '' }} 
                              </td>
                                                            
                              <td>                                    
                                90                               
                              </td>
                              

                              
                           </tr>
                        </tbody>
                     </table>
						
						
						
						
						
						
						
         
         </div>
      </div>
   </div>  
   
   
    <div class=" col-md-12 col-sm-6 mb-4">
      <div class="card">
         <div class="card-body">
            <h4><small>Invoice History</small></h4>
			<div id="Anchor-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_length" id="Anchor-listing_length">
                                            <label>
                                                Show
                                                <select name="Anchor-listing_length" aria-controls="Anchor-listing" class="form-control form-control-sm">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                entries
                                            </label>
                                        </div>
                                    </div> 
									
                                    
                                </div>  </div>
			
			<table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellspacing="0" cellpadding="0">
                           <thead>
                              <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" style="width: 107px;">Sr.No</th><th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" style="width: 103px;">Docs </th><th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Comment : activate to sort column ascending" style="width: 164px;">Comment </th><th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 258px;">Status</th><th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Timestamp: activate to sort column ascending" style="width: 352px;">Timestamp</th></tr>
                           </thead>
						   
                           <tbody>
						   <tr role="row" class="odd">
						   <td class="sorting_1"></td>
						   <td></td>
						   <td></td>
						   <td></td>
						   <td></td>
						   </tr>
						   </tbody>
                        </table>
						
						
						<div class="row mt-3"><div class="col-sm-12 col-md-5"><div class="dataTables_info" id="invoice_history_info" role="status" aria-live="polite">Showing 1 to 1 of 1 entries</div></div><div class="col-sm-12 col-md-7"><div class="dataTables_paginate paging_simple_numbers float-right" id="invoice_history_paginate"><ul class="pagination mb-0"><li class="paginate_button page-item previous disabled" id="invoice_history_previous"><a href="#" aria-controls="invoice_history" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li><li class="paginate_button page-item active"><a href="#" aria-controls="invoice_history" data-dt-idx="1" tabindex="0" class="page-link">1</a></li><li class="paginate_button page-item next disabled" id="invoice_history_next"><a href="#" aria-controls="invoice_history" data-dt-idx="2" tabindex="0" class="page-link">Next</a></li></ul></div></div></div>
						
						
						
						
						
          
         </div>
      </div>
   </div> 
</div>










<div class="modal" id="myModal3">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Disburse Invoice</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Invoice Amount
                                 
                                 </label>
								<input type="text" class="form-control " value="60,000" disabled="">
                                 
                              </div>
							 
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Interest Rate (%) 
                                 
                                 </label>
								<input type="text" class="form-control" value="14%" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Processing Fee 
                                 
                                 </label>
								<input type="text" class="form-control" value="0" disabled="">
                                 
                              </div>
                           </div>
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Margin Rate(%) 
                                 
                                 </label>
								<input type="text" class="form-control" value="10%" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Funded Amount : 
                                 
                                 </label>
								<input type="text" class="form-control" value="₹56,000" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Final Funded Amount  : 
                                 
                                 </label>
								<input type="text" class="form-control" value="₹48146" disabled="">
                                 
                              </div>
                           </div>
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Limit Offered  : 
                                 
                                 </label>
								<input type="text" class="form-control" value="₹1,000,0000" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Limit available for disburse : 
                                 
                                 </label>
								<input type="text" class="form-control" value="₹99,40,000" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Tenor (Days): 
                                 
                                 </label>
								<input type="text" class="form-control" value="90" disabled="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Fund Date: 
                                 
                                 </label>
								<input type="date" class="form-control" value="2019-12-13">
                                 
                              </div>
                           </div>
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Processing Fee: (₹150,000.00) 
                                 
                                 </label>
								<input type="date" class="form-control" value="">
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-6">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Payment Receipt: 
                                 
                                 </label>
								<input type="file" class="form-control" value="">
                                 
                              </div>
                           </div>
						   
						  

                        </div>
			     <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Disburse</button> 
				 <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
            
         </div>
      </div>
   </div>
</div>




<div class="modal" id="myModal4">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Invoice Confirmation</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="row">
                          
						   
						  
						   
						 
						  
						   
						   <div class="col-md-12">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Payment Receipt:  <span class="error_message_label doc-error">*</span>
                                 
                                 </label>
								<input type="file" class="form-control" value="">
                                 
                              </div>
                           </div>
						   <div class="col-md-12">
                          
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Comment  <span class="error_message_label doc-error">*</span>
                                 
                                 </label>
								<textarea class="form-control" cols="4" rows="4"></textarea>
                                 
                              </div>
                           </div>
						   
						  

                        </div>
			     <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Save</button> 
				 <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
            
         </div>
      </div>
   </div>
</div>



<div class="modal" id="myModal5">
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
<div class="modal show" id="myModal2" style="display: none;">
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
                                  <input type="text" class="form-control" id="invoice_amount" value="{{($invoice->invoice_amount) ? $invoice->invoice_amount : '' }}" disabled="">
                                  <input type="hidden" name="invoice_id" value="{{($invoice->invoice_id) ? $invoice->invoice_id : '' }}">
                              </div>
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Invoice Approved Amount
                                 <span class="mandatory">*</span>
                                 </label>
			      <input type="text" class="form-control" id="invoice_approve_amount" name="approve_invoice_amount" value="{{($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount : '' }}">
                                 
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
  </div>
    @endsection
    @section('jscript')
<script>


    
///////////////////////////////////////// change invoice amount////////////////
$(document).on('click','#UpdateInvoiceAmount',function(){
    
    var amount  = parseFloat($("#invoice_amount").val());
    var approveAmount  = parseFloat($("#invoice_approve_amount").val());
    if(approveAmount > amount)
    {
        $(".model7msg").show();
        $(".model7msg").html('Invoice Approve Amount should not greater amount');
        return false;
     }
     else
     {   $(".model7msg").hide();
         return true;
     }
 });
</script>

@endsection
 