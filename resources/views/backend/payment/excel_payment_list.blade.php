@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
<div class="col-md-12 ">
   <section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-2">Manage Payment</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Manage Payment</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin">

   <div class="col-md-12 ">
      <div class="card">
         <div class="card-body">
              <div class="modal-content">
         <!-- Modal Header -->
         <form method="post" action="{{route('save_excel_payment')}}" enctype="multipart/form-data">
         <!-- Modal body -->
         @csrf
         <div class="modal-body ">
	     <div class="row">
                 
             <div class="col-md-6">
            <div class="form-group">
            <label for="txtCreditPeriod">Customer <span class="error_message_label">*</span></label>
           <select readonly="readonly" name="customer" class="form-control" id="supplier_bulk_id">
               <option value="">Please Select</option>
               @foreach($customer as $row)
               <option value="{{$row->user_id}}">{{$row->user->f_name}}  / {{$row->customer_id}}</option>
               @endforeach
           </select>
            <span id="supplier_bulk_id_msg" class="error" style="display: none;"></span>
           </div>
            </div>
										
            <div class="col-md-4">
            <label for="txtCreditPeriod">Upload <span class="error_message_label">*</span></label>
            <div class="custom-file  ">

               <input type="file" accept=".csv" class="custom-file-input fileUpload" id="customFile" name="upload">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span id="customFile_msg" class="error" style="display: none;"></span>
              <a href="http://admin.rent.local/backend/assets/invoice/invoice-template.csv" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
           
            </div>

            </div>
               <div class="col-md-2">
            <label for="txtCreditPeriod"> <span class="error_message_label"></span></label>
            <div class="custom-file  ">
                <input id="submit" type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2" value="Upload">
            </div>

            </div>							
									
            <div class="clearfix">
            </div>
            </div>	
         </div>
         </form>     
         <form id="signupForm" action="http://admin.rent.local/invoice/backend_save_bulk_invoice" method="post"> 
             <input type="hidden" name="_token" value="aQhbyHIEgDmON9SLjv3FSDyjn5eYwAfs5uY2ARO8">          <div class="row">
             
                                <div class="col-sm-12">
                                    <table class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;" width="100%" cellspacing="0">
                                        <thead>
                                            <tr role="row">
                                               
                                                <th>Sr. No.</th>
                                                <th>Payment Date</th>
                                                   <th>Virtual Acc. No.</th>
                                                   <th>Amount</th>
                                                    <th>Payment Refrence No.</th>
                                                <th>Remarks</th>
                                                 <th>Action</th>
                                            </tr>
                                        </thead>
 
                                        
                                        
                                        <tbody class="invoiceAppendData">
                                            <tr id="deleteRow37" class="appendExcel1">
                                                <td>1</td>
                                                <td><input type="text" id="invoice_date" name="invoice_date[]" readonly="readonly" placeholder="Invoice Date" class="form-control date_of_birth datepicker-dis-fdate" value="Payment Date"></td>
                                                <td> <input type="text"  id="invoice_no" name="invoice_no[]" class="form-control batchInvoice" value="" placeholder="Virtual Acc. No."></td>
                                                <td><input type="text" id="invoice_due_date"  name="invoice_due_date[]" class="form-control date_of_birth" placeholder="Amount" value=""></td>
                                                <td><input type="text" class="form-control subOfAmount" id="invoice_approve_amount1" name="invoice_approve_amount[]" placeholder="Payment Refrence No." value=""></td>
                                                <td><input type="text" class="form-control subOfAmount" id="invoice_approve_amount1" name="invoice_approve_amount[]" placeholder="Remarks" value=""></td>
                                                <td><i class="fa fa-trash deleteTempInv" data-id="37" aria-hidden="true"></i></td>
                                            </tr>
                                   
                                    </table>
                                </div>
              <div class="col-md-12">
                   <div class="col-md-8">
                           <label class="error" id="tenorMsg"></label>
                   </div>
                  <span id="final_submit_msg" class="error" style="display:none;">Total Amount  should not greater Program Limit</span>
                  <input type="hidden" value="90" id="tenor" name="tenor">
                  <input type="hidden" value="18" id="prgm_offer_id" name="prgm_offer_id">
                  <input type="submit" id="final_submit" class="btn btn-secondary btn-sm mt-3 float-right finalButton" value="Final Submit" style=""> 	
            </div> 
            
             </div>
        </form>
      </div>
         </div>
      </div>
   </div>
</div></div>
</div>
  {!!Helpers::makeIframePopup('modalUploadPayment','Upload Payment', 'modal-lg')!!}
    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_bulk_transaction: "{{ URL::route('backend_get_bulk_transaction') }}",
             data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
 };
 
 
  $(document).ready(function () {
      ////here code ////////////////
}); 
  
  
</script>
<script src="{{ asset('backend/js/ajax-js/bulk_transaction.js') }}"></script>

@endsection
 