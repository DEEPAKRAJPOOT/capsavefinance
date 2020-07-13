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
      <h3 class="mt-2">Add Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Add Invoice</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin ">
 <div class="col-md-12  mb-4">
      <div class="card">
         <div class="card-body">
           <div class="form-fields">
                <form id="signupForm" method="post" action="{{Route('backend_save_invoice')}}" enctype= multipart/form-data>
                   @csrf 
                    <div class="active" id="details">
                        <div class="form-sections">
                           
                           <div class="clearfix"></div>
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Anchor Business Name<span class="error_message_label">*</span><!--<span id="anc_limit" class="error" style="">--></span></label>
                                            <select readonly="readonly" class="form-control changeAnchor" id="anchor_id"  name="anchor_id">
                                             
                                            @if(count($get_anchor) > 0)
                                              @if($anchor==11)
                                               @foreach($get_anchor as $row) 
                                                    @php if(isset($row->anchorList->anchor_id)) {  @endphp
                                                    <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                    @php }  @endphp
                                                    @endforeach
                                              @else    
                                                <option value="">Please Select</option>
                                                    @foreach($get_anchor as $row) 
                                                    @php if(isset($row->anchorList->anchor_id)) { @endphp
                                                    <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                    @php } @endphp
                                                    @endforeach
                                               @endif  
                                            @endif
                                             </select>
                                             					 <!--<span><i class="fa fa-inr"></i> 50,000</span>-->
                                        </div>
                                    </div>
									
                                   
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Product Program Name
                                                <span class="error_message_label">*</span>   
                                            </label>
                                            <select readonly="readonly" class="form-control changeSupplier" id="program_id" name="program_id">
                                            @if($anchor==11)
                                            <option value="">Please Select</option>
                                            @if($get_program)
                                            {
                                             @foreach($get_program as $row1) 
                                              <option value="{{{$row1->program->prgm_id}}},{{{$row1->app_prgm_limit_id}}}">{{{$row1->program->prgm_name}}}</option>
                                                  
                                             @endforeach
                                              @endif
                                            @endif
                                            </select>
                                           
                                
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Customer Name  <span class="error_message_label">*</span></label> <span id="pro_limit" class="error"></span>
                                            <span id="adhoc_msg" style="display:none">
                                                <input name="limit_type" type="checkbox" id="limit_type" class="get_adhoc" value="1">
                                                <b> Adhoc </b>
                                            </span>
                                            <select readonly="readonly" class="form-control getTenor" id="supplier_id" name="supplier_id">
                                             
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice No. <span class="error_message_label">*</span> </label>
                                            <input type="text" minlength="3" maxlength="25" id="invoice_no" name="invoice_no" class="form-control" placeholder="Invoice No">
                                            <span id="msgInvoiceDupli" class="error"></span>  
                                        </div>
                                    </div> 
									<div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice Date <span class="error_message_label">*</span> </label>
                                            <input type="text" id="invoice_date" name="invoice_date" readonly="readonly" placeholder="Invoice Date" class="getInvoiceD form-control date_of_birth datepicker-dis-fdate">
                                        </div>
                                    </div>
									
									<div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice Due Date <span class="error_message_label">*</span> </label>
                                              <input type="text" id="invoice_due_date" readonly="readonly" name="invoice_due_date" class="form-control date_of_birth" placeholder="Invoice Due Date">
                                       
                                        </div>
                                    </div>
                                    
                                    				
					<div class="col-md-4">
                                        <div class="form-group">
                                             <label for="txtCreditPeriod">Invoice Amount <span class="error_message_label">*</span> </label><span id="pro_remain_limit" class="error"></span>
                                            <input type="text" class="form-control" maxlength="15" id="invoice_approve_amount" name="invoice_approve_amount" placeholder="Invoice Approve Amount">
                                            <span id="msgProLimit" class="error"></span>
                                         </div>
										 <div class="form-group">
                                            <label for="txtCreditPeriod">Upload Invoice Copy<span class="error_message_label">*</span></label>
											
		<div class="custom-file">
               <label for="email">Upload Document</label>
               <input type="file" class="custom-file-input" id="customFile" name="doc_file">
               <label class="custom-file-label" for="customFile">Choose file</label>
                <span id="msgFile" class="text-success"></span>
            </div>
			
			
			
                                            
                                        </div>
                                    </div>
					<div class="col-md-8">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Remarks <span class="error_message_label">*</span> </label>
                                            <textarea class="form-control remark" name="remark" id="remark" rows="5" cols="5" placeholder="Remarks"></textarea>
                                    </div>
                                    </div>
                
		</div> 
                <div class="row">
                   <div class="col-md-12">
                       <div class="col-md-8">
                           <span  id="tenorMsg" style="color:red;"></span>
                       </div>
                       <div class="text-right mt-2" id="ApprovePro">
                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                           <input type="hidden" value="" id="prgm_offer_id" name="prgm_offer_id">
                            <input type="hidden" value="" id="tenor" name="tenor">
                             <input type="hidden" value="" id="exception" name="exception">
                             <input type="hidden" value="" id="tenor_old_invoice" name="tenor_old_invoice"> 
                            <input type="reset"    class="btn btn-secondary btn-sm" value="Cancel">
                           <input type="submit" id="submit"   class="btn btn-primary ml-2 btn-sm" value="Submit">
                       </div>
                   </div>
               </div> 

                           
                           
                          
                             
                        </div>
                    </div>
                </form>   
           </div>
         </div>
      </div>
   </div>
</div>



<div class="modal" id="myModal1">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Edit manage list</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Full Name" required="">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Anchor
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Enter Anchor Name" required="">
                              </div>
                           </div>
                        </div>
						<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="hidden" name="send_otp" id="send-otp" value="">
                                 <input type="email" name="email" id="email" value="" class="form-control" tabindex="4" placeholder="Email" disabled="">
                              </div>
                           </div>
            
                           <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>
                                   
                                    <input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" disabled="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                                 <input name="password" id="passwordRegistration" type="hidden" oninput="removeSpace(this);" value="nr40od5m">
                              </div>
                        </div>
            <button type="submit" class="btn btn-success float-right btn-sm">Submit</button>  
         </div>
      </div>
   </div>
</div>

<div class="modal" id="myModal">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Add Supplier</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Full Name" required="">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Business Name" required="">
                              </div>
                           </div>
                        </div>
						<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="hidden" name="send_otp" id="send-otp" value="">
                                 <input type="email" name="email" id="email" value="" class="form-control" tabindex="4" placeholder="Email" required="">
                              </div>
                           </div>
                           <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>
                                   
                                    <input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                                 <input name="password" id="passwordRegistration" type="hidden" oninput="removeSpace(this);" value="nr40od5m">
                              </div>
                        </div>
            <button type="submit" class="btn btn-success float-right btn-sm">Submit</button>  
         </div>
      </div>
   </div>
</div>
<div class="modal" id="myModal2">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Assign Lead</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body text-left">
			<div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Select Sales Person
                                 <span class="mandatory">*</span>
                                 </label>
								 <select class="form-control" name="nate">
								   <option>Select</option>
								   <option>Sales Person 1</option>
								   <option>Sales Person 2</option>
								   <option>Sales Person 3</option>
								 </select>
                                 
                              </div>
                           </div>
						   
						   <div class="col-md-12">
						   <label>Comment</label>
						   <textarea class="form-control" placeholder="Add Comment"></textarea>
						   </div>

                        </div>
						
            <button type="submit" class="btn btn-success float-right btn-sm mt-3">Submit</button>  
         </div>
      </div>
   </div>
</div>

  </div>
    @endsection
    @section('jscript')
<script type="text/javascript"> 
var messages = {
    token: "{{ csrf_token() }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    front_program_list: "{{ URL::route('front_program_list') }}",
    front_supplier_list: "{{ URL::route('front_supplier_list') }}",
    get_tenor: "{{ URL::route('get_tenor') }}",
    get_adhoc: "{{ URL::route('get_adhoc') }}",
    check_duplicate_invoice: "{{ URL::route('check_duplicate_invoice') }}",
   };
 
  </script> 
  <script src="{{ asset('backend/js/single_invoice.js') }}"></script>
@endsection
 