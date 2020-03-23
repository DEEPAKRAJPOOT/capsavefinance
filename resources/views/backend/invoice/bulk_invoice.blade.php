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
      <h3 class="mt-2">Upload Bulk Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Upload Bulk Invoice</li>
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
        <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span> <!--<span id="anc_limit" class="error"></span> --> </label>
        <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id" >
            <option value="">Select Anchor  </option>
              @foreach($anchor_list as $row)
                 @php if(isset($row->anchorOne->anchor_id)) { @endphp
                <option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}</option>
                @php } @endphp
                @endforeach
              </select>
             <span id="anchor_bulk_id_msg" class="error"></span>
                
                </div></div>
		
		 <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtCreditPeriod">Product Program Name
                            <span class="error_message_label">*</span>  <!-- <span id="pro_limit" class="error"></span> -->
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
             <span id="msgFile" class="text-success"></span>
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
                                                <th>Invoice Date</th>
                                                <th>Invoice Due Date</th>
                                                <th>Invoice  Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
 
                                        
                                        
                                        <tbody  class="invoiceAppendData">

                                        </tbody>
                                    </table>
                                </div>
              <div class="col-md-12">
                   <div class="col-md-8">
                           <label class="error" id="tenorMsg"></label>
                   </div>
                  <span class="exceptionAppend"></span>
                  <span id="final_submit_msg" class="error" style="display:none;">Total Amount  should not greater Program Limit</span>
                  <input type="hidden" value="" id="tenor" name="tenor">
                  <input type="hidden" value="" id="tenor_old_invoice" name="tenor_old_invoice"> 
                  <input type="hidden" value="" id="prgm_offer_id" name="prgm_offer_id">
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
 
  ///* upload image and get ,name  */
   $('input[type="file"]'). change(function(e){
        $("#customFile_msg").html('');
        var fileName = e. target. files[0]. name;
        $("#msgFile").html('The file "' + fileName + '" has been selected.' );
    });
  $(document).ready(function () {
        $(".finalButton").hide();
        $(".invoiceAppendData").append('<tr><td colspan="5">No data found...</td></tr>');
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
                             if(v.program!=null)
                             {
                                   $("#program_bulk_id").append("<option value='"+v.program.prgm_id+","+v.app_prgm_limit_id+"'>"+v.program.prgm_name+"</option>");  
                             }  
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
       var anchor_id =  $("#anchor_bulk_id").val(); 
      $("#supplier_bulk_id").empty();
      $("#pro_limit").empty();
      $("#pro_limit_hide").empty();
       var postData =  ({'bulk':1,'app_id':anchor_id,'program_id':program_id,'_token':messages.token});
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
                        if(data.uploadAcess==0)
                        {
                            $("#submit").css("pointer-events","none");
                            $("#tenorMsg").text("You don't have permission to upload invoice for this program.");           
                          
                        }
                        else
                        {
                             $("#tenorMsg").text(" ");           
                             $("#submit").css("pointer-events","inline");
                            
                        }
                        var obj1  = data.get_supplier;
                        var obj2   =  data.limit;
                        var offer_id   =  data.offer_id;
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#prgm_offer_id").val(offer_id);
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                         $("#pro_limit_hide").val(obj2.anchor_sub_limit);  
                         $(obj1).each(function(i,v){
                               var dApp = "000000" + v.app_id;
                                 //$("#supplier_id").append("<option value='"+v.user_id+","+v.app.app_id+"'>"+v.f_name+"&nbsp;"+v.l_name+"("+v.app.app_id+")</option>");  
                                 $("#supplier_bulk_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.biz_entity_name+"&nbsp;&nbsp;("+v.customer_id+")</option>");  
                          });
                       
                    }
                    else
                    {      $("#supplier_bulk_id").append("<option value=''>No data found</option>");  
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
      function ChangeDateFormat(date)
   {
            var datearray = date.split("/");
            return  newdate = datearray[1] + '/' + datearray[0] + '/' + datearray[2];

   }

    function findDaysWithDate(firstDate,secondDate)
    {
        var firstDate  =   ChangeDateFormat(firstDate);
        var secondDate  =  ChangeDateFormat(secondDate);
        var startDay = new Date(firstDate);
        var endDay = new Date(secondDate);
        var millisecondsPerDay = 1000 * 60 * 60 * 24;
        var  millisBetween = startDay.getTime() - endDay.getTime();
        var    days = millisBetween / millisecondsPerDay;
        return  Math.floor(days);
    } 
    /////////////// validation the time of final submit/////////////// 
      $(document).on('click','#final_submit',function(e){
        $("#final_submit_msg").hide();
        var p_limit =  $("#pro_limit_hide").val();  
        var sum = 0;
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
                
        //////// check total amount /////////////
        $(".subOfAmount").each(function() {
        sum += parseInt($(this).val().replace(/,/g, ''));
        });
        if(sum >  p_limit)
        {
            $("#final_submit_msg").show(); 
            e.preventDefault();
        }
        
        ////////// check tanor date///////////////////
        var count  = 0;
        $(".batchInvoiceDate").each(function(i,v) { count++;
        var  first =  $(".invoiceTanor"+count).val();
        var  second = $(this).val();
        var getDays  = parseInt(findDaysWithDate(first,second));
        var tenor  = parseInt($('#tenor').val());
         var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //As January is 0.
        var yyyy = today.getFullYear();
        var cDate  = dd+"/"+mm+"/"+yyyy;
        var getOldDays  = findDaysWithDate(cDate,second);
        var tenor  = $('#tenor').val();
        var tenor_old_invoice  = $('#tenor_old_invoice').val();
       /*if(getOldDays > tenor_old_invoice)
        {
           $("#tenorMsg").show(); 
          $("#tenorMsg").html('Invoice Date & Current Date diffrence should be '+tenor_old_invoice+' days'); 
           e.preventDefault();
        }
         else */
        if(getDays > tenor)
        {
           $(".appendExcel"+count).css("background-color","#ea9292");
           $("#tenorMsg").show(); 
           $("#tenorMsg").html('Invoice Date & Invoice Due Date diffrence should be '+tenor+' days'); 
           e.preventDefault();
        } 
         else if(getDays < 0)
        {
           
           $("#tenorMsg").show(); 
           $("#tenorMsg").html('Invoice Due Date should be  greater than invoice date'); 
           e.preventDefault();
        }
        else
        {
           $(".appendExcel"+count).css("background-color","white"); 
        }
        });
       
       } else {
        /// alert();
        }  
     
    });
    
    //////// String value not allowed in  amount filed//////////////////////
 $(document).on('keypress','.subOfAmount',function(event){       
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
     var  invoice_approve_amount  =  $(this).attr("id");
     document.getElementById(invoice_approve_amount).addEventListener('input', event =>
     event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
  
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
         if($("#pay_calculation_on").val()=='')
        {
             $("#pay_calculation_on_msg" ).show();
             $("#pay_calculation_on_msg" ).text("Please Select Payment Calculation");
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
        var pay_calculation_on  =  $("#pay_calculation_on").val();
        datafile.append('_token', messages.token );
        datafile.append('doc_file', file);
        datafile.append('anchor_bulk_id', anchor_bulk_id);
        datafile.append('program_bulk_id', program_bulk_id);
        datafile.append('supplier_bulk_id', supplier_bulk_id);
        datafile.append('pro_limit_hide', pro_limit_hide);
        datafile.append('pay_calculation_on', pay_calculation_on);
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
                   
                    var getDays  = parseInt(findDaysWithDate(invoice_due_date,invoice_date));
                    var tenor  = parseInt($('#tenor').val());
                    var getClass ="";
                    if(getDays > tenor)
                    {
                      var getClass = "background-color: #ea9292;";  
                    }
                     var invoice_approve_amount =  invoice_approve_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    
                     $(".invoiceAppendData").append('<tr id="deleteRow'+v.invoice_id+'" class="appendExcel'+j+'" style="'+getClass+'"><td>'+j+'</td><td><input type="hidden"  value="'+v.invoice_id+'" name="id[]"> <input type="text" maxlength="20" minlength="2" id="invoice_no'+v.invoice_id+'" name="invoice_no[]" class="form-control batchInvoice" value="'+v.invoice_no+'" placeholder="Invoice No"></td><td><input type="text" id="invoice_date'+v.invoice_id+'" name="invoice_date[]" readonly="readonly" placeholder="Invoice Date" class="form-control date_of_birth datepicker-dis-fdate batchInvoiceDate" value="'+invoice_date+'"></td><td><input type="text" id="invoice_due_date'+v.invoice_id+'" readonly="readonly" name="invoice_due_date[]" class="form-control date_of_birth datepicker-dis-pdate batchInvoiceDueDate invoiceTanor'+j+'" placeholder="Invoice Due Date" value="'+invoice_due_date+'"></td><td><input type="text" class="form-control subOfAmount" id="invoice_approve_amount'+j+'" name="invoice_approve_amount[]" placeholder="Invoice Approve Amount" value="'+invoice_approve_amount+'"></td><td><i class="fa fa-trash deleteTempInv" data-id="'+v.invoice_id+'" aria-hidden="true"></i></td></tr>');
                      
                    });
                      datepickerDisFdate();
                      datepickerDisPdate();
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
    
     $(document).on('click','.deleteTempInv',function(){
     if (confirm("Are you sure? You want to delete it.")) {
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
 