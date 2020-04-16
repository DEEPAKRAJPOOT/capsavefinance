@extends('layouts.app')
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
                <form id="signupForm" method="post" action="{{Route('front_save_invoice')}}" enctype= multipart/form-data>
                   @csrf 
                    <div class="active" id="details">
                        <div class="form-sections">
                           
                           <div class="clearfix"></div>
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span><!--<span id="anc_limit" class="error" style="">--></span></label>
                                            <select readonly="readonly" class="form-control changeAnchor" id="anchor_id"  name="anchor_id">
                                             
                                            @if(count($get_anchor) > 0)
                                                <option value="">Please Select</option>
                                                @foreach($get_anchor as $row) 
                                                @php if(isset($row->anchorList->anchor_id)) { @endphp
                                                <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                @php } @endphp
                                                @endforeach
                                               
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
                                            </select>
                                           
                                
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Customer Name  <span class="error_message_label">*</span></label> <span id="pro_limit" class="error"></span> 
                                            <select readonly="readonly" class="form-control getTenor" id="supplier_id" name="supplier_id">
                                             
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice No. <span class="error_message_label">*</span> </label>
                                            <input type="text" maxlength="10" id="invoice_no" name="invoice_no" class="form-control" placeholder="Invoice No">
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
                                            <label for="txtCreditPeriod">Remarks <span class="error_message_label"></span> </label>
                                               <textarea class="form-control" name="remark" rows="5" cols="5" placeholder="Remarks"></textarea>
                                    </div>
                                    </div>
                
    </div> 
                <div class="row">
                   <div class="col-md-12">
                       <div class="col-md-8">
                           <label class="error" id="tenorMsg"></label>
                       </div>
                       <div class="text-right mt-2" id="ApprovePro">
                              
                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                           <input type="hidden" value="" id="prgm_offer_id" name="prgm_offer_id">
                            <input type="hidden" value="" id="tenor" name="tenor">
                             <input type="hidden" value="" id="exception" name="exception">
                             <input type="hidden" value="" id="tenor_old_invoice" name="tenor_old_invoice"> 
                             <input type="reset" id="cancel" class="btn btn-secondary btn-sm" value="Cancel">
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
  </div>
    @endsection
    @section('jscript')
<script type="text/javascript"> 
var messages = {
    token: "{{ csrf_token() }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    front_lms_program_list: "{{ URL::route('front_lms_program_list') }}",
    get_tenor: "{{ URL::route('get_tenor') }}",
    front_supplier_list: "{{ URL::route('front_supplier_list') }}",
    check_duplicate_invoice: "{{ URL::route('check_duplicate_invoice') }}",
   };
  
  </script> 
    <script src="{{ asset('frontend/js/single_invoice.js') }}"></script>
@endsection
 