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
		 
		 <ul class="nav nav-tabs" role="tablist">
             <li class="nav-item ">
      <a class="nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice')}}">Pending</a>
    </li>
    <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice')}}">Approved</a>
    </li>
  <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice')}}">Disbursment Que</a>
    </li>
        
   <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_sent_to_bank')}}">Sent to Bank</a>
    </li>
	<li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_failed_disbursment')}}">Failed Disbursment</a>
    </li>
    <li class="nav-item">
              <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_disbursed')}}">Disbursed</a>
         
    </li>
      <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice')}}">Repaid</a>
    </li>
    <li class="nav-item">
      <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_reject_invoice')}}">Reject</a>

    </li>
  
   
  </ul>



  <div class="tab-content">
    
    <div id="menu1" class=" active tab-pane "><br>

       
    <div class="card">
        <div class="card-body">
                     <div class="row"><div class="col-md-4"></div>
                 <div class="col-md-2">				 
                                                      
                     <select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
                           <option value="">Select Application  </option>
                           @foreach($get_bus as $row)
                           <option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
                           @endforeach
                          
                        
                  </select>
                     <span id="anchorMsg" class="error"></span>
                  
                   </div>
               <div class="col-md-2">				 
                                                              
                    <select class="form-control form-control-sm changeAnchor searchbtn"  name="search_anchor">
                           <option value="">Select Anchor  </option>
                           @foreach($anchor_list as $row)
                           <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                           @endforeach
                          
                        
                  </select>
                 
                   </div>
             <div class="col-md-2">		    
                                                            
                 <select readonly="readonly" class="form-control form-control-sm searchbtn" id="supplier_id" name="search_supplier">
                         
                    </select>
                     </div>    
                      <div class="col-md-2">	
                          <a href="{{Route('backend_bulk_invoice')}}"type="button" class="btn btn-success btn-sm ml-2"> Bulk Invoice Upload</a>

                   
            </div>
            
            </div>
            <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="invoiceList" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th><input type="checkbox" id="chkAll"></th> 
                                               <th>Anchor Name</th>
                                                <th>Supplier Name</th>
                                                <th>Program Name</th>
                                                <th>Invoice Date</th>
                                                <th>Invoice  Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
         
    </div>
	
	
  
  </div>
        
           
         </div>
      </div>
   </div>
</div></div>




<div class="modal align-middle" id="myModal6" style="display: none;" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
         <!-- Modal Header -->
         <div class="modal-header">
			<h5>Upload Invoices</h5>
            <button type="button" class="close close-btns" data-dismiss="modal">×</button>
         </div>
         <!-- Modal body -->
         <div class="modal-body ">
		 <form id="signupForm">
		 <div class="row">
		 <div class="col-md-6">
		<div class="form-group">
        <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span></label>
        <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id"  name="anchor_bulk_id">
                                             
                <option value="">Select Anchor  </option>
                @foreach($anchor_list as $row)
                <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                @endforeach
                                             </select>
                                             <span id="anc_limit"></span>
                
                </div></div>
		
		 <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtCreditPeriod">Product Program Name
                            <span class="error_message_label">*</span>
                        </label>
                         <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" name="supplier_bulk_id">
                                            </select>
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                                   <span id="pro_limit"></span>
               </div>
		</div>
            <div class="col-md-6">
            <div class="form-group">
            <label for="txtCreditPeriod">Customer Name <span class="error_message_label">*</span></label>
           <select readonly="readonly" class="form-control" id="supplier_bulk_id" name="supplier_bulk_id">
           </select>
            <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
            </div>
            </div>
										
										 <div class="col-md-6">
										 <label for="txtCreditPeriod">Upload Invoice <span class="error_message_label">*</span></label>
										<div class="custom-file  ">
              
                                                                                    <input type="file" onchange="uploadInvoice()" class="custom-file-input fileUpload" id="customFile" name="file_id">
               <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
										
										</div>
                                    
									
									
			<div class="clearfix">
			</div>
			</div>	
                     <h5 id="submitInvoiceMsg" class="text-success"></h5>
                     <button type="submit" id="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Upload</button> 
				 <button type="reset" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 	
				 
				 </form>
            
         </div>
      </div>
   </div>
</div>



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
            token: "{{ csrf_token() }}",
 };
 
 
  $(document).ready(function () {
       $("#program_bulk_id").append("<option value=''>No data found</option>");  
        $("#program_bulk_id").append("<option value=''>No data found</option>");                         
  /////// jquery validate on submit button/////////////////////
  $('#submit').on('click', function (e) {
     
     if ($('form#signupForm').validate().form()) {     
        $("#anchor_bulk_id" ).rules( "add", {
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
          $("#program_bulk_id" ).rules( "add", {
        required: true,
        messages: {
        required: "Please Select Product Program Name",
        }
        });
       
        $("#customFile" ).rules( "add", {
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
                       
                        $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  '+obj2.anchor_limit+'');
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
    
    
    function uploadInvoice()
    {
//        $('.isloader').show();
       $("#submitInvoiceMsg").empty();
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
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Invoice Successfully uploaded');
                }
                else
                {
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Total Amount if invoice should not greater Program Limit');
                 } 
            }
        });
    }
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list.js') }}"></script>

@endsection
 