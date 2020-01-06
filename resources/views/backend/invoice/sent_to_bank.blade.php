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
      <h3 class="mt-3">Manage Invoice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Manage Invoice</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin mt-3">
   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
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
         <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice')}}">Disbursed</a>
    </li>
  <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice')}}">Repaid</a>
    </li>
  
   
  </ul>



  <div class="tab-content">
    
    <div id="menu1" class=" active tab-pane "><br>

       
    <div class="card">
        <div class="card-body">
                     <div class="row"><div class="col-md-5"></div>
                 <div class="col-md-2">				 
                                                      
                    <select class="form-control form-control-sm changeAnchor"  name="search_biz">
                           <option value="">Select Application  </option>
                           @foreach($get_bus as $row)
                           <option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
                           @endforeach
                          
                        
                  </select>

                  
                   </div>
               <div class="col-md-2">				 
                                                              
                    <select class="form-control form-control-sm changeAnchor"  name="search_anchor">
                           <option value="">Select Anchor  </option>
                           @foreach($anchor_list as $row)
                           <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                           @endforeach
                          
                        
                  </select>

                  
                   </div>
             <div class="col-md-2">		    
                                                            
                 <select readonly="readonly" class="form-control form-control-sm" id="supplier_id" name="search_supplier">
                         
                    </select>

                   
                </div>    
           <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>

               

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
</div>

  </div>
    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_invoice_list: "{{ URL::route('backend_get_invoice_list') }}",
            get_program_supplier: "{{ URL::route('get_program_supplier') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
 };
 
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
  $("#supplier_id").append("<option value=''>No data found</option>");  
  $("#supplier_id").append("<option value=''>Select Supplier</option>");  
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

                                   $("#supplier_id").append("<option value='"+v.user_id+"'>"+v.f_name+"</option>");  
                            });
                        }
                        else
                        {
                                  $("#supplier_id").append("<option value=''>No data found</option>");  
                           
                        }
                        
                     
                    }
                    
                }
        }); }); 
  
  
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list.js') }}"></script>

@endsection
 