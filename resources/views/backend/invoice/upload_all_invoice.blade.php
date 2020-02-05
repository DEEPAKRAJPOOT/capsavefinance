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
                                            <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span><!--<span id="anc_limit" class="error" style="">--></span></label>
                                            <select readonly="readonly" class="form-control changeAnchor" id="anchor_id"  name="anchor_id">
                                             
                                            @if(count($get_anchor) > 0)
                                                <option value="">Please Select</option>
                                                @foreach($get_anchor as $row)  
                                                <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
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
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                                
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Customer Name  <span class="error_message_label">*</span></label>
                                            <select readonly="readonly" class="form-control" id="supplier_id" name="supplier_id">
                                             
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice No. <span class="error_message_label">*</span> </label>
                                             <input type="text" id="invoice_no" name="invoice_no" class="form-control" placeholder="Invoice No">
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
                                              <input type="text" id="invoice_due_date" readonly="readonly" name="invoice_due_date" class="form-control date_of_birth datepicker-dis-pdate" placeholder="Invoice Due Date">
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
                                        <div class="text-right mt-2">
                                            <input type="hidden" value="" id="prgm_offer_id" name="prgm_offer_id">
                                             <input type="hidden" value="" id="tenor" name="tenor">
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
   };
   
   $('[name="invoice_approve_amount"]').on('change blur keyup', function() {
     var pro_limit = parseInt($("#pro_limit_hide").val());
     var invoice_approve_amount = parseInt($("#invoice_approve_amount").val());
   
     if(invoice_approve_amount  > pro_limit)
     {
       
         $("#msgProLimit").text('Invoice amount should not more than program limit');
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
        var tenor  = $('#tenor').val();
     
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
        minlength: 6,
        messages: {
        required: "Please enter Invoice No",
        minlength: "Please, at least 6  characters are necessary",
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
         if(getDays < tenor)
        {
           $("#tenorMsg").show(); 
           $("#tenorMsg").html('Invoice Date & Invoice Due Date diffrence should be '+tenor); 
           e.preventDefault();
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
                           $("#program_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                           
                                   $("#program_id").append("<option value='"+v.program.prgm_id+","+v.app_prgm_limit_id+"'>"+v.program.prgm_name+"</option>");  
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
      if(program_id=='')
      {
          return false; 
      }
      $("#supplier_id").empty();
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
                        var offer_id   =  data.offer_id;
                        var tenor   =  data.tenor;
                        $("#prgm_offer_id").val(offer_id);
                        $("#tenor").val(tenor);
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_sub_limit+'');
                         $("#pro_limit_hide").val(obj2.anchor_sub_limit);  
                         $("#supplier_id").append("<option value=''>Please Select</option>");  
                            $(obj1).each(function(i,v){
                            
                                   $("#supplier_id").append("<option value='"+v.app.user.user_id+"'>"+v.app.user.f_name+"</option>");  
                            });
                       
                    }
                    else
                    {
                       
                               $("#supplier_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
    
    
  </script> 
@endsection
 