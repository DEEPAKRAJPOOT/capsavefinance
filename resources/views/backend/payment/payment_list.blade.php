@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')



<div class="content-wrapper">

<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="form-fields">
                    <div class="active" id="details">
                        <div class="form-sections">
						<div class="row">
						<div class="col-md-2">
						<label class="float-left">Current Date
						</label>
						<input type="date" class="form-control form-control-sm " value="12/18/2019">
						</div>
							<div class="col-md-2">
						<label>Type</label>
						<select class="form-control form-control-sm">
						<option>All</option>
						<option>Manual</option>
						<option>Excel Upload</option>
						</select>
						</div>
						<div class="col-md-2">
						<label>&nbsp;</label><br>
						<button type="button" class="btn btn-success btn-sm">Search</button>
						</div>
						<div class="col-md-6 text-right">
						<label>&nbsp;</label><br>
						<a href="{{route('add_payment')}}" class="btn btn-primary btn-sm">Add Manual</a>
						<a href="#" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#myModal">Excel</a>
						</div>
						</div>
						
						
						
						
						<div class="row">
                           <div class="col-sm-12">
						    <div class="table-responsive ps ps--theme_default mt-2" data-ps-id="3ccd63d3-9e64-9530-aeba-35579d95a7af">
                              <table id="supplier-listing" class="table table-striped cell-border dataTable no-footer overview-table" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;" width="100%" cellspacing="0">
                                 <thead>
                                    <tr role="row">
                                     
                                       <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" style="width:11.5%">Sr. No.</th>
									   
                                       <th class="sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="width:11.5%">Customer ID</th>
									   
                                      
									   
                                       <th class="white-space numericCol sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Recommended Limit (₹): activate to sort column ascending" style="width:11.5%">Virtual Account No.</th>
									   
									   <th width="10%">Amount</th>
									   
									     <th class="white-space sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Created By: activate to sort column ascending" style="width:11.5%">Type</th>
										 
										 
                                       <th class="white-space sorting" tabindex="0" aria-controls="supplier-listing" rowspan="1" colspan="1" aria-label="Created By: activate to sort column ascending" style="width:11.5%">Created By</th>
									   
                                      
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr role="row" class="odd">
                                       
                                       <td>1</td>
                                       <td>54125</td>
                                      
                                       <td>CAP54215</td>
									   <td>50,000</td>
                                       <td>Auto</td>
									   <td></td>
									    
									   
									   
                                   
                                      
                                    </tr>
                                    
                                    
                                   
                                    
                                    
                                    
                                   
                                    
                                    
                                 </tbody>
                              </table>
							  <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div><div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
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
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
			<h4><small>Upload Payment</small></h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
        <label for="txtCreditPeriod">Customer  <span class="error_message_label">*</span></label>
       <select required="" class="form-control form-control-sm">
                                                <option value=""> Select</option>
                                                <option value="Program-1"> Customer 1</option>
                                                <option value="Program-2"> Customer 2 </option>
                                            </select>
		</div>
		</div>
		<div class="col-md-6">
		<div class="form-group">
               <label>Upload</label>
			   <input type="file" class="form-control form-control-sm">
			   <a class="float-right" href="xls/payment-upload.xls"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Download Template </a>
			   </div></div>
			   </div>
			   <div class="clearfix"></div>
			     <button type="button" class="btn btn-success btn-sm mt-3 float-right ml-2" data-dismiss="modal">Upload</button>
			      <button type="button" class="btn btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Cancel</button>
              
            </div>
         
        </div>
    </div>
</div>
  </div>
    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_invoice_list_approve: "{{ URL::route('backend_get_invoice_list_approve') }}",
            upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
            get_program_supplier: "{{ URL::route('get_program_supplier') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            front_program_list: "{{ URL::route('front_program_list') }}",
            front_supplier_list: "{{ URL::route('front_supplier_list') }}",
            update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
            invoice_document_save: "{{ URL::route('invoice_document_save') }}",
            update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
            token: "{{ csrf_token() }}",
 };
 
 
  $(document).ready(function () {
       $("#program_bulk_id").append("<option value=''>No data found</option>");  
        $("#program_bulk_id").append("<option value=''>No data found</option>");                         
  /////// jquery validate on submit button/////////////////////
        
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
   
 ///////////////////////For Invoice Approve////////////////////////
  $(document).on('click','.approveInv',function(){
    if(confirm('Are you sure? You want to disbursment queue.'))  
    {
     var invoice_id =  $(this).attr('data-id'); 
      var postData =  ({'invoice_id':invoice_id,'status':9,'_token':messages.token});
      th  = this;
       jQuery.ajax({
        url: messages.update_invoice_approve,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                        alert(errorThrown);
                 },
                success: function (data) {
                    $(th).parent('td').parent('tr').remove();
                }
             });  
    }
    else
    {
        return false;
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
       $('.isloader').show();
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
 //////////////////// for upload invoice//////////////////////////////   
function uploadFile(app_id,id)
{
   $(".isloader").show(); 
   var file  = $("#file"+id)[0].files[0];
   var extension = file.name.split('.').pop().toLowerCase();
   var datafile = new FormData();
   datafile.append('_token', messages.token );
   datafile.append('app_id', app_id);
   datafile.append('doc_file', file);
   datafile.append('invoice_id', id);
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.invoice_document_save,
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
        enctype: 'multipart/form-data',
         success: function(r){
            $(".isloader").hide();
            location.reload();
        }
    });
}

//////////////////////////// for bulk approve invoice////////////////////


$(document).on('click','#bulkApprove',function(){
    var arr = [];
    i = 0;
    th  = this;
      $(".chkstatus:checked").each(function(){
           arr[i++] = $(this).val();
        });
        if(arr.length==0){
            alert('Please select atleast one checked');
            return false;
        }
        if(confirm('Are you sure? You want to disbursment queue.'))  
    { 
         var status =  $(this).attr('data-status');
        var postData =  ({'invoice_id':arr,'status':status,'_token':messages.token});
         jQuery.ajax({
          url: messages.update_bulk_invoice,
                  method: 'post',
                  dataType: 'json',
                  data: postData,
                  error: function (xhr, status, errorThrown) {
                  alert(errorThrown);

                  },
                  success: function (data) {
                      if(data==1)
                      {
                          
                   location.reload();
        }

          }
      });
  }
  else
  {
     return false; 
    }
    });
    
///////////////////////////////////////// change invoice amount////////////////
$(document).on('click','.changeInvoiceAmount',function(){
    
    var limit  = $(this).attr('data-limit');
    var approveAmount  = $(this).attr('data-approve');    
    var amount  = $(this).attr('data-amount'); 
    var invoiceId  = $(this).attr('data-id');
    $("#invoice_id").val(invoiceId);
    $("#invoice_amount").val(amount);
    $("#invoice_approve_amount").val(approveAmount);
    
  });
    
///////////////////////////////////////// change invoice amount////////////////
$(document).on('click','#UpdateInvoiceAmount',function(){
    
    var amount  = parseFloat($("#invoice_amount").val());
    var approveAmount  = parseFloat($("#invoice_approve_amount").val());
    if(approveAmount > amount)
    {
        $(".model7msg").show();
        $(".model7msg").html('Invoice Approve Amount should not greater amount');
        return false;
     }
     else
     {   $(".model7msg").hide();
         return true;
     }
 });
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list_approve.js') }}"></script>

@endsection
 