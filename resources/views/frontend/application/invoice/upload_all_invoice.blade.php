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
                                                <span class="error_message_label">*</span>   <!-- <span id="pro_limit" class="error"></span> -->
                                            </label>
                                            <select readonly="readonly" class="form-control changeSupplier" id="program_id" name="program_id">
                                            </select>
                                           
                                
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Customer Name  <span class="error_message_label">*</span></label>
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
                                             <label for="txtCreditPeriod">Invoice Amount <span class="error_message_label">*</span> </label>
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
   ///* upload image and get ,name  */
   $('input[type="file"]'). change(function(e){
        $("#customFile-error").hide();
        var fileName = e. target. files[0]. name;
        $("#msgFile").html('The file "' + fileName + '" has been selected.' );
    });

   ///////////////// invoice approve amount check here///////////
   $(document).on('change blur keyup','#invoice_approve_amount', function() {
     var pro_limit = parseInt($("#pro_limit_hide").val());
     var invoice_approve_amount = $("#invoice_approve_amount").val();
     var invoice_approve_amount = invoice_approve_amount.replace(/\,/g,'');
      if(invoice_approve_amount  > pro_limit)
     {
         $("#msgProLimit").text('Invoice amount should not be more than offered limit amount.');
         $("#submit").css("pointer-events","none");
         return false;
     }
     else
     {
         $("#msgProLimit").empty();
         $("#submit").css("pointer-events","auto");
         return true;
     }
     
});

 //////////// check duplicate invoice ////////////////////
 
  $(document).on('change blur keyup','#invoice_no,#supplier_id', function() {
     var invoice = $("#invoice_no").val();
     var user_id  = $("#supplier_id").val();
     var user_id  =  user_id.split(',');
     var user  =  user_id[0];
     if(user==""  || invoice=="")
     {
         return false;
     }
    
      var postData =  ({'user_id':user,'invoice':invoice,'_token':messages.token});
       jQuery.ajax({
        url: messages.check_duplicate_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                      if(data.status==1)
                        {
                            $("#msgInvoiceDupli").text('Invoice No already exists');
                            $("#submit").css("pointer-events","auto");
                            $("#submit").css("pointer-events","none");
                            return false;
                        }
                        else
                        {
                            $("#msgInvoiceDupli").empty();
                           return true;
                        }
                }
            });
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
  

 $(document).ready(function () {
      //////////// comma seprate value in amount   //////////////////////// 
      
        document.getElementById('invoice_approve_amount').addEventListener('input', event =>
        event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
        $("#program_id").append("<option value=''>No data found</option>");  
        $("#supplier_id").append("<option value=''>No data found</option>");                         
  /////// jquery validate on submit button/////////////////////
  $('#submit').on('click', function (e) {
        $("#tenorMsg").hide();
        var first  = $('#invoice_due_date').val();
        var second = $('#invoice_date').val();
        var getDays  = findDaysWithDate(first,second);
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //As January is 0.
        var yyyy = today.getFullYear();
        var cDate  = dd+"/"+mm+"/"+yyyy;
        var getOldDays  = findDaysWithDate(cDate,second);
        var tenor  = $('#tenor').val();
        var tenor_old_invoice  = $('#tenor_old_invoice').val();
     if ($('form#signupForm').validate().form()) {  
       $("#anchor_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Anchor name",
        }
        });
       
      $("#supplier_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please Select Supplier Name",
        }
        });
          $("#program_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please Select Product Program Name",
        }
        });
        $("#invoice_no" ).rules( "add", {
        required: true,
        maxlength: 20,
        messages: {
        required: "Please enter Invoice No",
        maxlength: "Maximum 20  characters are necessary",
        }
        });
        
        $("#invoice_due_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Due Date",
        }
        }); 
        $("#invoice_date" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Date",
        }
        }); 
        
        $("#invoice_approve_amount" ).rules( "add", {
        required: true,
        messages: {
        required: "Please enter Invoice Approve Amount",
        }
        }); 
        $("#customFile" ).rules( "add", {
        required: true,
        messages: {
        required: "Please upload Invoice Copy",
        }
        }); 
         if(getDays > tenor)
        {
           $("#tenorMsg").show(); 
           $("#tenorMsg").html('Invoice date & invoice due date difference should not be more than '+tenor+' days'); 
           e.preventDefault();
        }
       else if(getOldDays > tenor_old_invoice)
        {
          // $("#tenorMsg").show(); 
          // $("#tenorMsg").html('Invoice date & current date difference should not be more than '+tenor_old_invoice+' days.'); 
          /// e.preventDefault();
          $("#exception").val(28);
        }
         
        } else {
        /// alert();
        }  
     });         
  });  
  
  ////////////// get due date depend on tenor date ///////////
   $(document).on('keyup change','.getInvoiceD',function(){
        var date = $(this).val(); 
        if($("#program_id").val()!='' && date!='')
      {
       
        var date = ChangeDateFormat(date);
        var oldDate = new Date(date);
        var days  = parseInt($('#tenor').val());
        var nextday =new Date(oldDate.getFullYear(),oldDate.getMonth(),oldDate.getDate()+days);
        var dueDate  = (nextday.getDate()+'/'+(nextday.getMonth()+1)+'/'+nextday.getFullYear());
        $("#invoice_due_date").val(dueDate);
    }
   });
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeAnchor',function(){
      
      var anchor_id =  $(this).val(); 
      if(anchor_id=='')
      {
            $("#pro_limit").empty();
             $("#pro_limit_hide").empty();
      }
      $("#program_id").empty();
      $("#anc_limit").empty();
      var postData =  ({'anchor_id':anchor_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.front_lms_program_list,
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
                           $("#program_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                             if(v.program!=null)
                             {                                 
                                   $("#program_id").append("<option value='"+v.program.prgm_id+","+v.app_prgm_limit_id+"'>"+v.program.prgm_name+"</option>");  
                              }                   
                             });
                           
                        
                       
                    }
                    else
                    {
                       
                               $("#program_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
   
    //////// String value not allowed in  amount filed//////////////////////
 $(document).on('keypress','#invoice_approve_amount',function(event){       
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeSupplier',function(){
      $("#invoice_date").val('');
      var program_id =  $(this).val(); 
      var anchor_id =  $("#anchor_id").val(); 
      if(program_id=='')
      {
          return false; 
      }
      $("#supplier_id").empty();
      $("#pro_limit").empty();
      $("#pro_limit_hide").empty();
      var postData =  ({'bulk':0,'program_id':program_id,'_token':messages.token});
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
                            $("#tenorMsg").text("You don't have permission to upload invoice for this program.");           
                            $("#ApprovePro").hide();
                            
                        }
                        else
                        {
                             $("#ApprovePro").show();
                             $("#tenorMsg").text(" ");           
                           
                            
                        }
                        var obj1  = data.get_supplier;
                        var obj2   =  data.limit;
                        var offer_id   =  data.offer_id;
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#prgm_offer_id").val(offer_id);
                      //  $("#tenor_old_invoice").val(tenor_old_invoice);
                      //  $("#tenor").val(tenor);
                      //  $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                      //  $("#pro_limit_hide").val(obj2.anchor_sub_limit);  
                        $("#supplier_id").empty();
                        $("#supplier_id").append("<option value=''>Please Select Customer</option>");  
                        $(obj1).each(function(i,v){
                                 var dApp = v.appCode;
                                 //$("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.f_name+"&nbsp;"+v.l_name+" ("+ dApp +")</option>");
                                 $("#supplier_id").append("<option value='"+v.user_id+","+v.app_id+","+v.prgm_offer_id+"'>"+v.biz_entity_name+"&nbsp;&nbsp;("+v.customer_id+")</option>");  
                            });
                       
                    }
                    else
                    {
                        
                               $("#supplier_id").append("<option value=''>No data found</option>");  
                      
                    }
                  
                }
        }); }); 
 //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.getTenor',function(){
      var program_id =  $("#program_id").val(); 
      var anchor_id =  $("#anchor_id").val(); 
      var supplier_id  = $(this).val();
       $("#invoice_date, #invoice_due_date, #invoice_approve_amount").val(''); 
      if(supplier_id=='')
      {
          return false; 
      }
     var postData =  ({'bulk':0,'anchor_id':anchor_id,'supplier_id':supplier_id,'program_id':program_id,'_token':messages.token});
       jQuery.ajax({
        url: messages.get_tenor,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                alert(errorThrown);
                
                },
                success: function (data) {
                        var tenor   =  data.tenor;
                        var tenor_old_invoice  = data.tenor_old_invoice;
                        $("#tenor_old_invoice").val(tenor_old_invoice);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+data.limit+'');
                        $("#pro_limit_hide").val(data.limit);  
                      
                }
        }); }); 
  $(document).on('change','#supplier_id',function(){
    var selValue = $(this).val();
    var selValueArr = selValue.split(",");
    $("#prgm_offer_id").val(selValueArr[2]);       
  });
  </script> 
@endsection
 