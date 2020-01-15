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
              <div class="modal-content">
         <!-- Modal Header -->
       
         <!-- Modal body -->
         <div class="modal-body ">
		
                 
		 <div class="row">
                    
                     
                     
		 <div class="col-md-6">
		<div class="form-group">
        <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span> <span id="anc_limit" class="error"></span></label>
        <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id" >
                                             
                <option value="">Select Anchor  </option>
                @foreach($anchor_list as $row)
                <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                @endforeach
                                             </select>
        
                                               <span id="anchor_bulk_id_msg" class="error"></span>
                
                </div></div>
		
		 <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtCreditPeriod">Product Program Name
                            <span class="error_message_label">*</span>  <span id="pro_limit" class="error"></span>
                        </label>
                         <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" >
                                            </select>
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                                  
                                    <span id="program_bulk_id_msg" class="error"></span>
               </div>
		</div>
            <div class="col-md-6">
            <div class="form-group">
            <label for="txtCreditPeriod">Customer Name <span class="error_message_label">*</span></label>
           <select readonly="readonly" class="form-control" id="supplier_bulk_id" >
           </select>
            <span id="supplier_bulk_id_msg" class="error"></span>
            <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
            </div>
            </div>
										
            <div class="col-md-4">
            <label for="txtCreditPeriod">Upload Invoice <span class="error_message_label">*</span></label>
            <div class="custom-file  ">

               <input type="file" accept=".csv"   class="custom-file-input fileUpload" id="customFile" name="file_id">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span id="customFile_msg" class="error"></span>
            </div>

            </div>
                                    
		 <div class="col-md-2">
            <label for="txtCreditPeriod"> <span class="error_message_label"></span></label>
            <div class="custom-file  ">
        <a  id="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Upload</a>
            </div>

            </div>							
									
			<div class="clearfix">
			</div>
			</div>	
                   
                    
				
            
         </div>
         <form id="signupForm" action="{{Route('backend_save_bulk_invoice')}}" method="post"> 
             @csrf
          <div class="row">
             
                                <div class="col-sm-12">
                                    <table  class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                               
                                                <th>Sr. No.</th>
                                                <th>Invoice No</th>
                                                 <th>Invoice Due Date</th>
                                                <th>Invoice Date</th>
                                                <th>Invoice  Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
 
                                        
                                        
                                        <tbody  class="invoiceAppendData">

                                        </tbody>
                                    </table>
                                </div>
              <div class="col-md-12">
                  <span id="final_submit_msg" class="error" style="display:none;">Total Amount  should not greater Program Limit</span>
                  <input type="submit" id="final_submit" class="btn btn-secondary btn-sm mt-3 float-right finalButton" value="Final Submit"> 	
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
            backend_get_invoice_list: "{{ URL::route('backend_get_invoice_list') }}",
            upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
            get_program_supplier: "{{ URL::route('get_program_supplier') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            front_program_list: "{{ URL::route('front_program_list') }}",
            front_supplier_list: "{{ URL::route('front_supplier_list') }}",
            delete_temp_invoice: "{{ URL::route('delete_temp_invoice') }}",
            
            token: "{{ csrf_token() }}",
 };
 
 
  $(document).ready(function () {
      
       $(".finalButton").hide();
       $(".invoiceAppendData").append('<tr><td colspan="5">No data found...</td></tr>');
                      
       $("#program_bulk_id").append("<option value=''>No data found</option>");  
        $("#program_bulk_id").append("<option value=''>No data found</option>");                         
   
  }); 
  
  
   //////////////// for checked & unchecked////////////////
     $(document).on('click','#chkAll',function(){
        var isChecked =  $("#chkAll").is(':checked');
        if(isChecked)
       {
         $('input:checkbox').attr('checked','checked');
       }
       else
       {
              $('input:checkbox').removeAttr('checked');
       }     
     });
   
 
  //////////////////// onchange anchor  id get data /////////////////

  $("#supplier_id").append("<option value=''>Select customer</option>");  
  $(document).on('change','.changeAnchor',function(){
     var anchor_id =  $(this).val(); 
      $("#supplier_id").empty();
     
      var postData =  ({'anchor_id':anchor_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.get_program_supplier,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                   
                    if(data.status==1)
                    {
                        var obj1  = data.userList;
                    
                      ///////////////////// for suppllier array///////////////  
                  
                      if(obj1.length > 0)
                      {
                               $("#supplier_id").append("<option value=''> Select Supplier </option>"); 
                            $(obj1).each(function(i,v){
                         
                                   $("#supplier_id").append("<option value='"+v.app.user.user_id+"'>"+v.app.user.f_name+"</option>");  
                          
                          });
                        }
                        else
                        {
                                  $("#supplier_id").append("<option value=''>No data found</option>");  
                           
                        }
                        
                     
                    }
                    
                }
        }); }); 
  
  
  /////////// for pop up//////////////////
  
  
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeBulkAnchor',function(){
        $("#anchor_bulk_id_msg" ).hide();
      var anchor_id =  $(this).val(); 
      if(anchor_id=='')
      {
            $("#pro_limit").empty();
             $("#pro_limit_hide").empty();
      }
      $("#program_bulk_id").empty();
      $("#anc_limit").empty();
      var postData =  ({'anchor_id':anchor_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.front_program_list,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                    if(data.status==1)
                    {
                      
                        var obj1  = data.get_program;
                        var obj2   =  data.limit;
                       
                        $("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_limit+'');
                          
                     
                           $("#program_bulk_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                           
                                   $("#program_bulk_id").append("<option value='"+v.program.prgm_id+"'>"+v.program.prgm_name+"</option>");  
                            });
                           
                        
                       
                    }
                    else
                    {
                       
                               $("#program_bulk_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
  
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeBulkSupplier',function(){
    
       $("#program_bulk_id_msg" ).hide  ();
      var program_id =  $(this).val(); 
      $("#supplier_bulk_id").empty();
      $("#pro_limit").empty();
      $("#pro_limit_hide").empty();
      var postData =  ({'program_id':program_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.front_supplier_list,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                    if(data.status==1)
                    {
                      
                        var obj1  = data.get_supplier;
                        var obj2   =  data.limit;
                       
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                         $("#pro_limit_hide").val(obj2.anchor_limit);  
                         $("#supplier_bulk_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                            
                                   $("#supplier_bulk_id").append("<option value='"+v.app.user.user_id+"'>"+v.app.user.f_name+"</option>");  
                            });
                       
                    }
                    else
                    {
                       
                               $("#supplier_bulk_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
    
      $(document).on('change','#supplier_bulk_id',function(){
       if($("#supplier_bulk_id").val()!='')
        {
           $("#supplier_bulk_id_msg" ).hide();
        }
      });
      
      
        $(document).on('change','.fileUpload',function(){
       
          $("#customFile_msg" ).hide();
       
      });
       
    /////////////// validation the time of final submit/////////////// 
      $(document).on('click','#final_submit',function(){
          
       if ($('form#signupForm').validate().form()) {     
        $(".batchInvoice" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter invoice no",
        }
        });
          $(".batchInvoiceDueDate" ).rules( "add", {
        required: true,
    
        messages: {
        required: "Please enter currect invoice due date",
        }
        });
          $(".batchInvoiceDate" ).rules( "add", {
        required: true,
     
        messages: {
        required: "Please enter currect invoice due date",
        }
        });
          $(".subOfAmount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter currect invoice amount",
        }
        });
       } else {
        /// alert();
        }  
      if (confirm("Are you sure? You want to update it")) {         
        $("#final_submit_msg").hide();  
        var p_limit =  $("#pro_limit_hide").val();  
        var sum = 0;
            $(".subOfAmount").each(function() {
            sum += parseInt($(this).val());
            });
            if(sum >  p_limit)
            {
                $("#final_submit_msg").show(); 
                return false;
            }
            return true;
        }
        else
        {
            return false;
        }
    });
    
    //////// String value not allowed in  amount filed//////////////////////
 $(document).on('keypress','.subOfAmount',function(event){       
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});

    $(document).on('click','#submit',function(e){  
    
        if($("#anchor_bulk_id").val()=='')
        {
             $("#anchor_bulk_id_msg" ).show();
            $("#anchor_bulk_id_msg" ).text('Please Select Anchor Name');
            return false;
        }
          if($("#program_bulk_id").val()=='')
        {
              $("#program_bulk_id_msg" ).show();
              $("#program_bulk_id_msg" ).text("Please Select Product Program Name");
              return false;
        }
        if($("#supplier_bulk_id").val()=='')
        {
             $("#supplier_bulk_id_msg" ).show();
             $("#supplier_bulk_id_msg" ).text("Please Select Supplier Name");
             return false;
        }
        if($("#customFile").val()=='')
        {
             $("#customFile_msg" ).show();
             $("#customFile_msg" ).text("Please Select Csv file");
             return false;
        }
        else
        {
        if (confirm("Are you sure? You want to upload CSV")) {     
       $(".invoiceAppendData").empty();
      
        var file  = $("#customFile")[0].files[0];
        var datafile = new FormData();
        var anchor_bulk_id  = $("#anchor_bulk_id").val();
        var program_bulk_id  = $("#program_bulk_id").val();
        var supplier_bulk_id  = $("#supplier_bulk_id").val();
        var pro_limit_hide  =  $("#pro_limit_hide").val();
        datafile.append('_token', messages.token );
        datafile.append('doc_file', file);
        datafile.append('anchor_bulk_id', anchor_bulk_id);
        datafile.append('program_bulk_id', program_bulk_id);
        datafile.append('supplier_bulk_id', supplier_bulk_id);
        datafile.append('pro_limit_hide', pro_limit_hide);
        $('.isloader').show();
        $.ajax({
            headers: {'X-CSRF-TOKEN':  messages.token  },
            url : messages.upload_invoice_csv,
            type: "POST",
            data: datafile,
            processData: false,
            contentType: false,
            cache: false, // To unable request pages to be cached
            enctype: 'multipart/form-data',

            success: function(r){
               
                $(".isloader").hide();

                if(r.status==1)
                {
                    $('.isloader').hide(); 
                    $(".finalButton").show();
                   
                     j =0;
                     $(r.data).each(function(i,v){ j++;
                    var invoice_approve_amount =  v.invoice_approve_amount;  
                    var  date1 = v.invoice_due_date;
                    var dateAr = date1.split('-');
                    var invoice_due_date = '';
                    var invoice = '';
                    if (dateAr != '')
                    {

                    var invoice_due_date = dateAr[2] + '/' + dateAr[1] + '/' + dateAr[0];
                    }
                    var  date2 = v.invoice_date;
                    var dateAr1 = date2.split('-');
                    if (dateAr1 != '')
                    {

                      var invoice_date = dateAr1[2] + '/' + dateAr1[1] + '/' + dateAr1[0];
                    }
                    if(parseInt(v.invoice_approve_amount)=='0.00')
                    {
                       var invoice_approve_amount = "";
                    }
                   
                    $(".invoiceAppendData").append('<tr id="deleteRow'+v.invoice_id+'"><td>'+j+'</td><td><input type="hidden"  value="'+v.invoice_id+'" name="id[]"> <input type="text" maxlength="10" minlength="6" id="invoice_no'+v.invoice_id+'" name="invoice_no[]" class="form-control batchInvoice" value="'+v.invoice_no+'" placeholder="Invoice No"></td><td><input type="text" id="invoice_due_date'+v.invoice_id+'" readonly="readonly" name="invoice_due_date[]" class="form-control date_of_birth datepicker-dis-fdate batchInvoiceDueDate" placeholder="Invoice Due Date" value="'+invoice_due_date+'"></td><td><input type="text" id="invoice_date'+v.invoice_id+'" name="invoice_date[]" readonly="readonly" placeholder="Invoice Date" class="form-control date_of_birth datepicker-dis-fdate batchInvoiceDate" value="'+invoice_date+'"></td><td><input type="text" class="form-control subOfAmount" id="invoice_approve_amount'+v.invoice_id+'" name="invoice_approve_amount[]" placeholder="Invoice Approve Amount" value="'+invoice_approve_amount+'"></td><td><i class="fa fa-trash deleteTempInv" data-id="'+v.invoice_id+'" aria-hidden="true"></i></td></tr>');
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
                     $(".invoiceAppendData").append('<tr><td colspan="5" class="error">Something went wrong, Please try again!</td></tr>'); 
                   
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
    
     $(document).on('click','.deleteTempInv',function(){
     if (confirm("Are you sure?")) {
      var temp_id =  $(this).attr('data-id'); 
      var postData =  ({'temp_id':temp_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.delete_temp_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                    if(data.status==1)
                    {
                         $(".finalButton").show();
                         $("#deleteRow"+data.id).remove();
                      }
                  }
                });
            }
            else
            {
                return false;
            }
         });        
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list.js') }}"></script>

@endsection
 