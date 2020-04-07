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
                         <p class="mb-0" style="text-transform:uppercase"><b>Invoice No : </b>{{($invoice->invoice_no) ? $invoice->invoice_no : '' }}</p>
                     </div> 
                    
                  <div class="col-md-6">
                    
                                  @php 
                                   $color  = ['0' =>'','7'=>"badge badge-warning",'8' => "badge badge-success",'9' =>"badge badge-success",'10' =>"badge badge-success",'11' => "badge badge-danger",'12' => "badge badge-danger",'13' =>"badge badge-success",'14' => "badge badge-danger",'28' =>"badge badge-danger"];
                                   @endphp
                                   @foreach($status as $row)
                                   @if($row->id==$invoice->status_id)
                                   <button type="button" class="{{$color[$row->id]}} btn-sm float-right" style="font-size: revert;">{{$row->status_name}}
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
           <h4 class="sub-title mb-2">Customer/Supplier Details</h4>
					 
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
                           <div class="supplier-left col-md-6"> <b>GST</b></div>
                           <div class="supplier-right col-md-6">
                           {{($invoice->gst->pan_gst_hash) ? $invoice->gst->pan_gst_hash : '' }}
                           </div>
                        </li>
                        <li class="row ">
                           <div class="supplier-left col-md-6"><b>PAN</b></div>
                           <div class="supplier-right col-md-6">
                          {{($invoice->pan->pan_gst_hash) ? $invoice->pan->pan_gst_hash : '' }}
                           </div>
                        </li>
                       
                     </ul>
         </div>
      </div>
   </div>
   
   
   <div class="  col-sm-6 mb-4">
      <div class="card">
         <div class="card-body">
            
           
					 <h4 class="mb-2 sub-title">Anchor Details</h4>
					 
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
			
            <input type="hidden" value="{{($invoice->invoice_id) ? $invoice->invoice_id : '' }}" name="inv_name">
			
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
                                    {{($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount : '' }} <a href="#" data-toggle="modal" data-target="#myModal2">
                                        @php if($invoice->status_id==7 && $role==1) { @endphp
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                         @php  } @endphp
                                       </a>
								 
                              </td>
                              
                              
                              
                              
                              <td id="invoice-date">
                                 {{($invoice->invoice_date) ? $invoice->invoice_date : '' }} 
                              </td>
                                                            
                              <td>  
                            @php                                  
                                $now = strtotime($invoice->invoice_date); // or your date as well
                                $your_date = strtotime($invoice->invoice_due_date);
                                $datediff = abs($now - $your_date);
                               echo  $tenor = round($datediff / (60 * 60 * 24));     
                               @endphp
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
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="invoiceActivityList" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                              <th>Sr. No.</th>
                                                <th>Comment </th> 
                                                <th>Status</th>
                                                <th>Timestamp</th>
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
                                  <input type="text" class="form-control" id="invoice_amount" value="{{($invoice->invoice_amount) ? number_format($invoice->invoice_amount) : '' }}" disabled="">
                                  <input type="hidden" name="invoice_id" value="{{($invoice->invoice_id) ? $invoice->invoice_id : '' }}">
                              </div>
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Invoice Approved Amount
                                 <span class="mandatory">*</span>
                                 </label>
			      <input type="text" class="form-control" id="invoice_approve_amount" name="approve_invoice_amount" value="{{($invoice->invoice_approve_amount) ? number_format($invoice->invoice_approve_amount) : '' }}">
                                 
                              </div>
                               
							   <div class="form-group">
                                 <label for="txtCreditPeriod">Comment  <span class="error_message_label doc-error">*</span>
                                 
                                 </label>
								<textarea class="form-control" name="comment" id="comment" cols="4" rows="4"></textarea>
                                 
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
    var messages = {
            backend_activity_invoice_list: "{{ URL::route('backend_activity_invoice_list') }}",
            token: "{{ csrf_token() }}",
 };
 
$(document).ready(function(){
 ///////////// use for amount comma seprate//////////////////////////   
document.getElementById('invoice_approve_amount').addEventListener('input', event =>
event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));

});
    
///////////////////////////////////////// change invoice amount////////////////
$(document).on('click','#UpdateInvoiceAmount',function(){
    
    var amount = parseFloat($("#invoice_amount").val().replace(/,/g, ''));
        var approveAmount = parseFloat($("#invoice_approve_amount").val().replace(/,/g, ''));
        if (approveAmount > amount)
        {
            $(".model7msg").show();
            $(".model7msg").html('Invoice Approve Amount should not greater amount');
            return false;
        } else
        {
            $(".model7msg").hide();
            return true;
        }
 });
</script>
<script src="{{ asset('backend/js/ajax-js/view_invoice_detail.js') }}"></script>
@endsection
 