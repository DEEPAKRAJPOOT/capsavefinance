@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
    
   <form id="createLeadForm" name="createLeadForm" method="POST" action="{{route('save_backend_lead')}}">
   @csrf
      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="full_name">Full Name<span class="mandatory">*</span></label>
               <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="form-control full_name" tabindex="1" placeholder="Full Name" />
               {!! $errors->first('full_name', '<span class="error">:message</span>') !!}
            </div>
         </div>
         <div class="col-6">
            <div class="form-group">
               <label for="comp_name">Business Name
               <span class="mandatory">*</span>
               </label>
               <input type="text" name="comp_name" id="comp_name" value="{{ old('comp_name') }}" class="form-control comp_name" tabindex="2" placeholder="Business Name" >
               {!! $errors->first('comp_name', '<span class="error">:message</span>') !!}
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="email">Email
               <span class="mandatory">*</span>
               </label>
               <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control email" tabindex="3" placeholder="Email" >
               @if(Session::has('error') && Session::get('error'))
                    <label class='error'>{{Session::get('error')}}</label>
               @endif
               {!! $errors->first('email', '<span class="error">:message</span>') !!}
            </div>
         </div>
         <div class="col-6">
            <div class="form-group">
               <label for="phone">Mobile
               <span class="mandatory">*</span>
               </label>

               <input class="form-control numbercls phone number_format" name="phone" id="phone" value="{{ old('phone') }}" tabindex="4" type="text" maxlength="10" placeholder="Mobile" required="">
               {!! $errors->first('phone', '<span class="error">:message</span>') !!}
            </div>
         </div>
      </div>           
                
      <div class="row">
         <div class="col-6">
            <div class="form-group">
               <label for="assigned_sale_mgr">Assigned Sale Manager
               <span class="mandatory">*</span>
               </label>                                  
               {!!
               Form::select('assigned_sale_mgr',
               [''=>'Please Select']+Helpers::getAllUsersByRoleId(4),
               old('phone') ?  old('phone') : Auth::user()->user_id,
               array('id' => 'assigned_sale_mgr',
               'class'=>'form-control'))
               !!}
               {!! $errors->first('assigned_sale_mgr', '<span class="error">:message</span>') !!}
            </div>
         </div>   
         <div class="col-6">
            <div class="form-group">
               <label for="is_buyer">User Type
               <span class="mandatory">*</span>
               </label>
                  <select class="form-control is_buyer" name="is_buyer" id="is_buyer">
                     <option value="">Please Select</option>
                     <option value="1" {{ (old("is_buyer") == "1" ? "selected":"") }}>Supplier</option>
                     <option value="2" {{ (old("is_buyer") == "2" ? "selected":"") }}>Buyer</option>
                  </select>
                    {!! $errors->first('is_buyer', '<span class="error">:message</span>') !!}
            </div>
         </div>                        
      </div>                 
                
      <button type="submit" class="btn  btn-success btn-sm float-right" id="saveLead">Submit</button>  
   </form>
</div>

@endsection
@php 
$is_accept = session()->get('is_accept', false);
$messages = session()->get('message', false);
@endphp
@section('jscript')
@if($is_accept == config('common.YES'))
<script>
    try {
    var p = window.parent;
    p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
    p.jQuery("#createLeadForm").modal('hide');
    p.oTables.draw();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
<script>

    var messages = {
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
//        is_accept: "{{ Session::get('is_accept') }}",
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
    
//   if(messages.is_accept == 1){
//      setTimeout(function() {
//         var p = window.parent;
//         p.jQuery("#createLeadForm").modal('hide'); 
//         p.oTables.draw(); 
//      }, 1000);
//   }
   $('#saveLead').on('click', function (event) {
         $('input.full_name').each(function () {
            $(this).rules("add",
                     {
                        required: true,
                        lettersonly: true , 
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