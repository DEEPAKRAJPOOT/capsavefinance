@extends('layouts.app')
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
               <form id="signupForm" method="post" action="{{Route('front_save_invoice')}}" enctype= multipart/form-data>
                   <input type="hidden" name="app_id" value="{{($app_id) ? $app_id :  ' '}}">
                  <input type="hidden" name="biz_id" value="{{($biz_id) ? $biz_id :  ' '}}">
                   @csrf
                    <div class="active" id="details">
                        <div class="form-sections">
                            <div class="col-md-8 col-md-offset-2">
                               
                                <div class="clearfix"></div>
                                <div class="row">
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Anchor Name <!-- from enchor table --> <span class="error_message_label">*</span></label>
      
                                            <select readonly="readonly" class="form-control changeAnchor" id="anchor_id" name="anchor_id">
                                              @if(count($get_anchor) > 1)
                                                <option value="">Please Select</option>
                                                @foreach($get_anchor as $row)  
                                                <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                @endforeach
                                                @else
                                           
                                                 @foreach($get_anchor as $row)  
                                                <option value="{{{$row->anchorList->anchor_id}}}">{{{$row->anchorList->comp_name}}}</option>
                                                @endforeach
                                                
                                                @endif
                                                </select>   
                                        </div>
                                    </div>
                   
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Product Program Name
                                                <span class="error_message_label">*</span>
                                            </label>

                                            <select readonly="readonly" class="form-control" id="program_id" name="program_id">
                                            @if(count($get_program) > 1)
                                                <option value="">Please Select</option>
                                                @foreach($get_program as $row)  
                                                <option value="{{{$row->program->prgm_id}}}">{{{$row->program->prgm_name}}}</option>
                                                @endforeach
                                              
                                                @endif
                                                
                                            </select>
                                        </div>
                                    </div>
                                  
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Supplier Name <span class="error_message_label">*</span></label>
                                            <input type="hidden"  class="form-control sn" id="supplier_id" name="supplier_id" value="{{ isset($get_user->user_id) ? $get_user->user_id : ''}}">
                                             <input type="text" readonly="readonly" class="form-control text-capitalize" value="{{isset($get_user->f_name) ? $get_user->f_name : ''}} {{isset($get_user->l_name) ? $get_user->l_name : ''}}"> 
                                             
                                           
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice No. <span class="error_message_label">*</span> </label>
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
                                            <input type="text" id="invoice_date" placeholder="Invoice Date"  name="invoice_date" readonly="readonly" class="form-control date_of_birth datepicker-dis-fdate">
                                        </div>
                                    </div>
                  
                  <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Invoice  Amount <span class="error_message_label">*</span> </label>
                                            <input type="text" class="form-control" maxlength="15" id="invoice_approve_amount" name="invoice_approve_amount" placeholder="Invoice Approve Amount">
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
   };
   
 $(document).ready(function () {
    
  /////// jquery validate on submit button/////////////////////
  $('#submit').on('click', function (e) {
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
          
         
        } else {
         alert();
        }  
     });         
  });  
   //////// String value not allowed in  amount filed//////////////////////
 $(document).on('keypress','#invoice_approve_amount',function(event){       
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
}); 
  //////////////////// onchange anchor  id get data /////////////////
  $(document).on('change','.changeAnchor',function(){
      var anchor_id =  $(this).val(); 
      $("#program_id").empty();
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
  
  </script> 
@endsection
 