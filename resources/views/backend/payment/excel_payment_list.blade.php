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
       
         <div class="modal-body ">
	     <div class="row">
                 
             <div class="col-md-6">
            <div class="form-group">
            <label for="txtCreditPeriod">Customer <span class="error_message_label">*</span></label>
           <select readonly="readonly" name="customer" class="form-control" id="customer">
               <option value="">Please Select</option>
               @foreach($customer as $row)
               <option value="{{$row->user_id}}">{{$row->user->f_name}}  / {{$row->customer_id}}</option>
               @endforeach
           </select>
            <span id="customer_msg" class="error" style="display: none;"></span>
           </div>
            </div>
										
            <div class="col-md-4">
            <label for="txtCreditPeriod">Upload <span class="error_message_label">*</span></label>
            <div class="custom-file  ">

               <input type="file" accept=".csv" class="custom-file-input fileUpload" id="upload" name="upload">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span id="upload_msg" class="error" style="display: none;"></span>
              <a href="http://admin.rent.local/backend/assets/invoice/invoice-template.csv" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
           
            </div>

            </div>
               <div class="col-md-2">
            <label for="txtCreditPeriod"> <span class="error_message_label"></span></label>
            <div class="custom-file  ">
                <button id="submit" id="submit"  class="btn btn-success float-right btn-sm mt-3 ml-2"> Upload </button>
            </div>

            </div>							
									
            <div class="clearfix">
            </div>
            </div>	
         </div>
         
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
    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_bulk_transaction: "{{ URL::route('backend_get_bulk_transaction') }}",
            save_excel_payment: "{{ URL::route('save_excel_payment') }}", 
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
 };
 
 
 $(document).on('click','#submit',function(e){  
       if($("#customer").val()=='')
        {
              $("#customer_msg" ).show();
              $("#customer_msg" ).text('Please Select Customer');
              return false;
        }
        if($("#upload").val()=='')
        {
              $("#upload_msg" ).show();
              $("#upload_msg" ).text("Please Upload File");
              return false;
        }
        
        else
        {
      if (confirm("Are you sure? You want to upload CSV")) {     
        $(".invoiceAppendData").empty();
        var file  = $("#upload")[0].files[0];
        var datafile = new FormData();
        var customer  = $("#customer").val();
        datafile.append('_token', messages.token );
        datafile.append('upload', file);
        datafile.append('customer', customer);
        $('.isloader').show();
        $.ajax({
            headers: {'X-CSRF-TOKEN':  messages.token  },
            url : messages.save_excel_payment,
            type: "POST",
            data: datafile,
            processData: false,
            contentType: false,
            cache: false, // To unable request pages to be cached
            enctype: 'multipart/form-data',
            success: function(r){
               console.log(r);
                $(".isloader").hide();
                if(r.status==1)
                {
                    $('.isloader').hide(); 
                    $(".finalButton").show();
                   
                     j =0;
                    $(r.data).each(function(i,v){ j++;
                    var payment_temp_id = v.payment_temp_id;  
                    var amount = v.amount; 
                    var virtual_account_no = v.virtual_account_no; 
                    var payment_ref_no = v.payment_ref_no; 
                    var remark = v.remark;
                    var date1 = v.payment_date;
                    var dateAr = date1.split('-');
                    var payment_date = '';
                 
                    if (dateAr != '')
                    {
                       var payment_date = dateAr[2] + '/' + dateAr[1] + '/' + dateAr[0];
                    }
                   
                    if(parseInt(v.amount)=='0.00')
                    {
                       var amount = "";
                    }
                       $(".invoiceAppendData").append('here input');  
                    });
                      datepickerDisFdate();
                     return false; 
                }
                 else if(r.status==2)
                {
                           $("#customFile_msg").show();  
                }
                else
                {
                     ///$("#submitInvoiceMsg").show();
                     $(".invoiceAppendData").append('<tr><td colspan="5" class="error">'+r.message+'</td></tr>'); 
                   
                      return false;
                 } 
              }  
          });
          }
           else
           {
             return false;
           }
          }
    });
  
  
</script>
<script src="{{ asset('backend/js/ajax-js/bulk_transaction.js') }}"></script>

@endsection
 