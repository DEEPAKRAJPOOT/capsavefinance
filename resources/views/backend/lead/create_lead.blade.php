@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
<div class="row">                
   <div class="col-md-12">   
         @if (Session::has('error') && Session::get('error'))  
         <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
               <ul>                  
                  <label class='error'>{{Session::get('error')}}</label><br>               
               </ul>
         </div>
         @endif
         @if ($errors->any())
            <div class="alert alert-danger">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
               <ul>
                     @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                     @endforeach
               </ul>
            </div>
         @endif
<!--         @if (Session::has('message') && Session::get('message'))  
         <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
               <ul>                  
                  <label class='success'>{{Session::get('message')}}</label><br>               
               </ul>
         </div>
         @endif-->
   </div>
</div>
   <form id="createLeadForm" name="createLeadForm" method="POST" action="{{route('save_backend_lead')}}">
   @csrf
      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="txtCreditPeriod">Full Name<span class="mandatory">*</span></label>
               <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="form-control full_name" tabindex="1" placeholder="Full Name" />
            </div>
         </div>
         <div class="col-6">
            <div class="form-group">
               <label for="txtSupplierName">Business Name
               <span class="mandatory">*</span>
               </label>
               <input type="text" name="comp_name" id="comp_name" value="{{ old('comp_name') }}" class="form-control comp_name" tabindex="2" placeholder="Business Name" >
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="txtEmail">Email
               <span class="mandatory">*</span>
               </label>
               <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control email" tabindex="3" placeholder="Email" >
            </div>
         </div>
         <div class="col-6">
            <div class="form-group">
               <label for="txtMobile">Mobile
               <span class="mandatory">*</span>
               </label>

               <input class="form-control numbercls phone number_format" name="phone" id="phone" value="{{ old('phone') }}" tabindex="4" type="text" maxlength="10" placeholder="Mobile" required="">
               <div class="failed">
                  <div style="color:#FF0000">
                     <small class="erro-sms" id="erro-sms">
                     </small>
                  </div>
               </div>
            </div>
         </div>
      </div>           
                
      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="txtEmail">Assigned Sale Manager
               <span class="mandatory">*</span>
               </label>                                  
               {!!
               Form::select('assigned_sale_mgr',
               [''=>'Please Select']+Helpers::getAllUsersByRoleId(4),
               old('phone') ?  old('phone') : Auth::user()->user_id,
               array('id' => 'assigned_sale_mgr',
               'class'=>'form-control'))
               !!}
            </div>
         </div>   
         <div class="col-6">
            <div class="form-group">
               <label for="txtEmail">User Type
               <span class="mandatory">*</span>
               </label>
                  <select class="form-control is_buyer" name="is_buyer" id="is_buyer">
                     <option value="">Please Select</option>
                     <option value="1" {{ (old("is_buyer") == "1" ? "selected":"") }}>Supplier</option>
                     <option value="2" {{ (old("is_buyer") == "2" ? "selected":"") }}>Buyer</option>
                  </select>
            </div>
         </div>                        
      </div>                 
                
      <button type="submit" class="btn  btn-success btn-sm float-right" id="saveLead">Submit</button>  
   </form>
</div>

@endsection

@section('jscript')
<script>

    var messages = {
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        is_accept: "{{ Session::get('is_accept') }}"
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {
    
    $(document).on('input', '.number_format', function (event) {
        // skip for arrow keys
        if (event.which >= 37 && event.which <= 40)
            return;

        // format number
        $(this).val(function (index, value) {
            return value.replace(/\D/g, "");
        });
    });
    
   if(messages.is_accept == 1){
      setTimeout(function() {
         parent.jQuery("#createLeadForm").modal('hide'); 
         parent.oTables.draw(); 
      }, 1000);
   }
   $('#saveLead').on('click', function (event) {
         $('input.full_name').each(function () {
            $(this).rules("add",
                     {
                        required: true
                     })
         });
         $('input.comp_name').each(function () {
            $(this).rules("add",
                     {
                        required: true
                     })
         });
         $('input.email').each(function () {
            $(this).rules("add",
                     {
                        required: true,
                        email: true                            
                     })
         });
         $('input.phone').each(function () {
            $(this).rules("add",
                     {
                        required: true,
                        number: true,
                        minlength:10,
                        maxlength:10
                     })
         });
         $('#assigned_sale_mgr').each(function () {
            $(this).rules("add",
                     {
                        required: true,
                     })
         });
         $('#is_buyer').each(function () {
            $(this).rules("add",
                     {
                        required: true,
                     })
         });   
         if($("#createLeadForm").valid()){
           $('#saveLead').hide();
         }              
   })
   $('form#createLeadForm').validate();
});
</script>
@endsection