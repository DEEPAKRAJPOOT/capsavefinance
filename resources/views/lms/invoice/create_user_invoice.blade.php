@extends('layouts.backend.admin-layout')
@section('content')
<div class="content-wrapper">
   <section class="content-header">
      <div class="header-icon">
         <i class="fa  fa-list"></i>
      </div>
      <div class="header-title">
         <h3>Create User Invoice</h3>
         <small>Create User Invoice</small>
         <ol class="breadcrumb">
            <li style="color:#374767;"> Home </li>
            <li style="color:#374767;">View User Invoice</li>
            <li class="active">Create User Invoice</li>
         </ol>
      </div>
   </section>
   <div class="row grid-margin mt-3">
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
         <div class="card">
            <div class="card-body">
               <div class="form-fields">
                  <div class="active" id="details">
                     <form id="userInvoice" name="userInvoice" method="POST" action="{{ route('save_user_invoice', [ 'user_id' => $userInfo->user_id ] ) }}" target="_top">
                        @csrf
                        <div class="table-responsive ps ps--theme_default w-100">
                           <table class="table border-0">
                              <tbody>
                                 <tr>
                                    <td class="text-left border-0" width="30%"> <b>Billing Address</b> </td>
                                    <td class="text-right border-0" width="30%"> <b>Original Of Recipient</b> </td>
                                 </tr>
                              </tbody>
                           </table>
                           <hr>
                           <table class="table border-0">
                              <tbody>
                                 <tr>
                                    <!-- USER -->
                                    <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="state_id">State Name</label>
                                             <select class="form-control" name="state_id" id="state_id">
                                                <option disabled value="" selected>Select State</option>
                                                @foreach($state_list as $state)
                                                <option value="{{$state->state_code}}">{{$state->name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                       </div>
                                    </td>
                                    <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="app_id">Applications</label>
                                             <select class="form-control" name="app_id" id="app_id">
                                                <option disabled value="" selected>Select Application</option>
                                                @foreach($appInfo as $ad_id) 
                                                <option value="{{$ad_id->app_id}}">{{$ad_id->business->biz_entity_name}}</option>
                                                @endforeach
                                             </select>
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                   <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="pan_no">PAN Number</label>
                                             <select class="form-control" name="pan_no" id="pan_no">
                                                <option disabled value="" selected>Select PAN</option>
                                             </select>
                                          </div>
                                       </div>
                                    </td>
                                    <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="gstin">GSTIN</label>
                                             <select class="form-control" name="gstin" id="gstin">
                                                <option disabled value="" selected>Select GSTIN</option>
                                             </select>
                                          </div>
                                       </div>
                                    </td>
                                    
                                 <tr>
                                    <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="invoice_date">Invoice Date</label>
                                             <input type="text" class="form-control dateFilter" id="invoice_date" name="invoice_date" placeholder="Invoice Date" autocomplete="off">
                                          </div>
                                       </div>
                                    </td>
                                    <td class="text-left border-0" width="30%">
                                      @php 
                                        $rand_inv = mt_rand(1000, 9999);
                                      @endphp
                                       <div class="row">
                                          <div class="form-group col-4" style="margin-left: 25px;">
                                             <a href="javascript:void(0);" class="invoice-state"><i style="color: #FFF;" id="state_abbr">&nbsp;</i></a>
                                             <label>Invoice No</label>
                                             <a href="javascript:void(0);" class="invoice_no"><i style="color: #FFF;" id="invoice_no">{{$rand_inv}}</i></a>
                                             <input type="text" class="form-control" id="invoice_city" name="invoice_city" placeholder="City Code" maxlength="5" autocomplete="off">
                                          </div>
                                          <input type="hidden" readonly class="form-control" id="invoice_id" name="invoice_id" placeholder="Invoice ID" value="{{$rand_inv}}" autocomplete="off">
                                       </div>
                                    </td>
                                 </tr>
                                 </tr>
                                 <tr>
                                    <td class="text-left border-0" width="30%">
                                      <div class="row">
                                          <div class="form-group col-12">
                                             <label for="place_of_supply">Place Of Supply</label>
                                             <input type="text" class="form-control" id="place_of_supply" name="place_of_supply" placeholder="Place Of Supply" autocomplete="off">
                                          </div>
                                       </div>
                                    </td>
                                    <td class="text-left border-0" width="30%">
                                       <div class="row">
                                          <div class="form-group col-12">
                                             <label for="refrence_no">Refrence No</label>
                                             <input type="text" class="form-control" id="refrence_no" name="refrence_no"  placeholder="Refrence Number" autocomplete="off">
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td class="text-left border-0" width="30%" colspan="2">
                                      <input type="hidden" class="form-control" id="state_code" name="state_code">
                                      <div class="row">
                                          <div class="form-group col-12">
                                             <label for="address">Enter Address</label>
                                             <textarea class="form-control" id="address" name="address" placeholder="Enter Address"></textarea>
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="row">
                           <div class="form-group col-md-12 mb-0">
                              <input type="submit" class="btn btn-success btn-sm pull-right"  id="add_address" value="Submit" />
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('jscript')
<script type="text/javascript">
   var message = {
       token: "{{ csrf_token() }}",
       user_id: "{{ $userInfo->user_id }}",
       gst_address_url: "{{route('get_biz_add_user_invoice')}}",
       get_statecode_url: "{{route('get_user_state_code')}}",
       get_app_gstin_url: "{{route('get_app_gstin')}}",
   }
   $(document).ready(function() {
       $('#userInvoice').validate({ // initialize the plugin
           rules: {
               'state_id': {
                   required: true,
               },
               'app_id': {
                   required: true,
               },
               'gstin': {
                   required: true,
               },
               'invoice_state': {
                   required: true,
               },
               'invoice_city': {
                   required: true,
               },
               'invoice_id': {
                   required: true,
                   digits: true,
               },
               'pan_no': {
                   required: true,
               },
               'invoice_date': {
                   required: true,
               },
               'address': {
                   required: true,
               },
               'refrence_no': {
                   required: true,
               },
               'place_of_supply': {
                   required: true,
               },
               'state_code': {
                   required: true,
               },
           },
           messages: {
               'state_id': {
                   required: "This field is required",
               },
               'app_id': {
                   required: "This field is required",
               },
               'gstin': {
                   required: "This field is required",
               },
               'invoice_state': {
                   required: "This field is required",
               },
               'invoice_city': {
                   required: "This field is required",
               },
               'invoice_id': {
                   required: "This field is required",
               },
               'pan_no': {
                   required: "This field is required",
               },
               'invoice_date': {
                   required: "This field is required",
               },
               'address': {
                   required: "This field is required",
               },
               'refrence_no': {
                   required: "This field is required",
               },
               'place_of_supply': {
                   required: "This field is required",
               },
               'state_code': {
                   required: "This field is required",
               },
           }
       });
   });

   /*let invoice_id = document.getElementById('invoice_id');
   let invoice_city = document.getElementById('invoice_city');
   
   invoice_id.addEventListener('input', function() {
       let pinVal =  document.getElementById('invoice_id').value;
       let pinStr = pinVal.toString();
   
       if (isNaN(invoice_id.value) || pinStr.length >= 4) {
           invoice_id.value = "";
       }
   });
   invoice_city.addEventListener('input', function() {
       let pinVal =  document.getElementById('invoice_city').value;
       let pinStr = pinVal.toString();
   
       if (isNaN(invoice_city.value) || pinStr.length >= 4) {
           invoice_city.value = "";
       }
   });*/

   $('#state_id').on('change',function(){
     let state_id = $(this).val();
     var state = $("#state_id :selected").text()
     var place_of_supply = $('#place_of_supply');
     if(state_id) {
         $('#state_abbr').html(state_id);
         $('#state_code').val(state_id);
         $('#place_of_supply').val(state);
     }
   });
   
   //    Date picker
   $(document).ready(function(){
       $("#invoice_date").datetimepicker({
           format: 'dd/mm/yyyy',
           autoclose: true,
           minView : 2,
       });
   });
   $(document).on('change', '#gstin', function() {
       var gstin = $(this).val();
       if(!gstin.length) {
           return false;
       };
       $.ajax({
          type:"POST",
          data: {'gstin' : gstin, '_token' : message.token},
          url:message.gst_address_url,
          success:function(data){ 
           if(data){
               $('#address').val(data);
           } else {
               $('#address').val();
           }
          }
       });
   });
   $('#state_id').on('change', function() {
       var state_id = $(this).val();
       if(!state_id.length) {
           return false;
       };
       $.ajax({
          type:"POST",
          data: {'state_code' : state_id, '_token':message.token},
          url: message.get_statecode_url,
          success:function(data){ 
               $.each(data, function(key, value) {
                  if(data) {
                      $('#state_code').val(key)
                  }
               });
              
          }
       });
   });
  $('#app_id').on('change', function() {
       var app_id = $(this).val();
       if(!app_id.length) {
           return false;
       };
       $('#refrence_no').val(app_id);
       $.ajax({
          type:"POST",
          data: {'app_id' : app_id, '_token':message.token},
          url: message.get_app_gstin_url,
          success:function(data){ 
            if (data.status == 1) {
              gstInfo = data.gstInfo;
              panInfo = data.panInfo;
              gstHtml = '<option disabled value="" selected>Select GSTIN</option>';
              panHtml = '<option disabled value="" selected>Select PAN</option>';
              $.each(gstInfo, function(gstkey, gstVal) {
                 gstHtml += '<option value="' + gstVal.pan_gst_hash + '">' + gstVal.pan_gst_hash + '</option>'; 
              });
              $.each(panInfo, function(pankey, panVal) {
                 panHtml += '<option value="'+panVal.pan_gst_hash+'">' + panVal.pan_gst_hash + '</option>'; 
              });
              $('#gstin').html(gstHtml);
              $('#pan_no').html(panHtml);
            }else{
              alert(data.message);
            }             
          }
       });
   });
</script>
@endsection