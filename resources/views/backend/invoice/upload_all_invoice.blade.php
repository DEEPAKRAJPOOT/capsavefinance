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
      <h3 class="mt-3">Upload Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Upload Invoice</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin mt-3">
   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
      <div class="card">
         <div class="card-body">
            
           <div class="form-fields">
               <form id="signupForm" method="post" action="{{Route('backend_save_invoice')}}" enctype= multipart/form-data>
                   @csrf
                    <div class="active" id="details">
                        <div class="form-sections">
                            <div class="col-md-8 col-md-offset-2">
                          
                                <div class="clearfix"></div>
                                <div class="row">
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Anchor Name <!-- from enchor table --> <span class="error_message_label">*</span></label>
                            
                                            <select readonly="readonly" class="form-control changeAnchor" id="anchor_id"  name="anchor_id">
                                             
                                            @if(count($get_anchor) > 0)
                                                <option value="">Please Select</option>
                                                @foreach($get_anchor as $row)  
                                                <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                @endforeach
                                               
                                                @endif
                                             </select>
                                             <span id="anc_limit"></span>
                                        </div>
                                    </div>
                   
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Product Program Name
                                                <span class="error_message_label">*</span>
                                            </label>
                                             <select readonly="readonly" class="form-control changeSupplier" id="program_id" name="program_id">
                                            </select>
                                   <input type="text" id="pro_limit_hide">
                                   <span id="pro_limit"></span>
                                        </div>
                                    </div>
                                  
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Supplier Name <span class="error_message_label">*</span></label>
                                             <select readonly="readonly" class="form-control" id="supplier_id" name="supplier_id">
                                             
                                            </select>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice  No. <span class="error_message_label">*</span> </label>
                                            <input type="text" id="invoice_no" name="invoice_no" class="form-control" placeholder="Invoice No">
                                        </div>
                                    </div> 
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice Due Date. <span class="error_message_label">*</span> </label>
                                            <input type="text" id="invoice_due_date" readonly="readonly" name="invoice_due_date" class="form-control date_of_birth datepicker-dis-fdate" placeholder="Invoice Due Date">
                                        </div>
                                    </div> 
                  <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice Date <span class="error_message_label">*</span> </label>
                                            <input type="text" id="invoice_date" name="invoice_date" readonly="readonly" placeholder="Invoice Date" class="form-control date_of_birth datepicker-dis-fdate">
                                        </div>
                                    </div>
                  
                  <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice  Amount <span class="error_message_label">*</span> </label>
                                            <input type="text" class="form-control" id="invoice_approve_amount" name="invoice_approve_amount" placeholder="Invoice Approve Amount">
                                        </div>
                                    </div>
                   <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Upload Invoice Copy<span class="error_message_label">*</span></label>
                                            <input type="file" name="doc_file" name="invoice_file" id="invoice_copy">
                                        </div>
                                    </div>
                  
                  <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Remarks <span class="error_message_label">*</span> </label>
                                           <textarea class="form-control" name="remark" rows="5" cols="5" placeholder="Remarks"></textarea>
                                        </div>
                                    </div>
                  
                  
                                    
                                </div> 
                            </div>
                            <div class="ima"></div> 
                           
                            <div class="col-md-8 col-md-offset-2">
                               <div class="row">
                                    <div class="col-md-12">
                                        <div class="text-right mt-3">
                                            <input type="reset"    class="btn btn-secondary btn-sm" value="Cancel">
                                            <input type="submit" id="submit"   class="btn btn-primary ml-2 btn-sm" value="Submit">
                                        </div>
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
    front_program_list: "{{ URL::route('front_program_list') }}",
    front_supplier_list: "{{ URL::route('front_supplier_list') }}",
   };
   
 $(document).ready(function () {
       $("#program_id").append("<option value=''>No data found</option>");  
        $("#supplier_id").append("<option value=''>No data found</option>");                         
  /////// jquery validate on submit button/////////////////////
  $('#submit').on('click', function (e) {
     var pro_limit = $("#pro_limit").text();
     alert(pro_limit);
     var invoice_approve_amount = $("#invoice_approve_amount").val();
     
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
        $("#invoice_copy" ).rules( "add", {
        required: true,
        messages: {
        required: "Please upload Invoice Copy",
        }
        }); 
        if(invoice_approve_amount > pro_limit)        {
          $("#invoice_approve_amount").rules("add", {
          compare:true,
          messages: {
              required: "Invoice Amount should not more then Program Limit",
          }
          });
          }
          
         
        } else {
         alert();
        }  
     });         
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
                           
                                   $("#program_id").append("<option value='"+v.program.prgm_id+"'>"+v.program.prgm_name+"</option>");  
                            });
                           
                        
                       
                    }
                    else
                    {
                       
                               $("#program_id").append("<option value=''>No data found</option>");  
                           
                      
                    }
                  
                }
        }); }); 
  
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeSupplier',function(){
    
      var program_id =  $(this).val(); 
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
                       
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_limit+'');
                         $("#pro_limit_hide").val(obj2.anchor_limit);  
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
 