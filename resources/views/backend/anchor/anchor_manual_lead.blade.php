@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
                
                {!!
                Form::open(
                array(
                'route' => 'add_manual_anchor_lead',
                'name' => 'anchorForm',
                'autocomplete' => 'off', 
                'id' => 'anchorForm',
                'target' => '_top',
                'method'=> 'POST'
                )
                )
                !!}
                
                        <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="f_name">First Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="f_name" id="f_name" value="{{ old('f_name') }}" class="form-control f_name" tabindex="1" placeholder="First Name" onkeyup="return checkFname(this.value)">
                                 {!! $errors->first('f_name', '<span class="error">:message</span>') !!}                                 
                                 <p><small style="font-size: 80%;">You can include first and middlle name (e.g Varun Dudani)</small></p>
                              </div>
                           </div>
                            <div class="col-6">
                              <div class="form-group">
                                 <label for="l_name">Last Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="l_name" id="l_name" value="{{ old('l_name') }}" class="form-control l_name" tabindex="1" placeholder="Last Name" onkeyup="return checkLname(this.value)">
                                 {!! $errors->first('l_name', '<span class="error">:message</span>') !!}
                              </div>
                           </div>
                            </div>
                           <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="comp_name">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="comp_name" id="comp_name" value="{{ old('comp_name') }}" class="form-control comp_name" tabindex="3" placeholder="Business Name" >
                                 {!! $errors->first('comp_name', '<span class="error">:message</span>') !!}
                              </div>
                           </div>
                           @php 
                            $role_id=Helpers::getUserRole(Auth::user()->user_id);
                            @endphp
                            @if ($role_id[0]->pivot->role_id == '11')
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="anchor_user_type">User Type
                                    <span class="mandatory">*</span>
                                    </label>
                                    <select class="form-control anchor_user_type" name="anchor_user_type" id="anchor_user_type">
                                        <option value="">Please Select</option>
                                        <option value="1" {{ (old("anchor_user_type") == "1" ? "selected":"") }}>Supplier</option>
                                        <option value="2" {{ (old("anchor_user_type") == "2" ? "selected":"") }}>Buyer</option>
                                    </select>
                                    {!! $errors->first('anchor_user_type', '<span class="error">:message</span>') !!}
                                </div>
                            </div>
                            @endif
<!--                           <div class="col-6">
                              <div class="form-group">
                                 <label for="pan_no">PAN No.
                                 <span class="mandatory">*</span>
                                 </label>
                                  <input type="text" name="pan_no" id="pan_no" value="{{ old('pan_no') }}" maxlength="10" class="form-control pan_no" tabindex="3" placeholder="PAN Number" >
                                 {!! $errors->first('pan_no', '<span class="error">:message</span>') !!}
                              </div>
                           </div>   -->
                           </div>
                
                       <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="email">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control email" tabindex="4" placeholder="Email">
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
                                    <input class="form-control numbercls phone number_format" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        
                           
                     </div>  
                @if ($role_id[0]->pivot->role_id!= '11')
                <div  class="row">  
                    <div class="col-6">
                              <div class="form-group">
                                 <label for="anchor_user_type">User Type
                                 <span class="mandatory">*</span>
                                 </label>
                                  <select class="form-control anchor_user_type" name="anchor_user_type" id="anchor_user_type">
                                      <option value="">Please Select</option>
                                      <option value="1" {{ (old("anchor_user_type") == "1" ? "selected":"") }}>Supplier</option>
                                      <option value="2" {{ (old("anchor_user_type") == "2" ? "selected":"") }}>Buyer</option>
                                  </select>
                                  {!! $errors->first('anchor_user_type', '<span class="error">:message</span>') !!}
                              </div>
                           </div>
                      <div class="col-6">
                              <div class="form-group">
                                 <label for="assigned_anchor">Anchor
                                 <span class="mandatory">*</span>
                                 </label>        
                                     <select class="form-control assigned_anchor" name="assigned_anchor" id="assigned_anchor">
                            <option value="">Please Select</option>
                             @foreach($anchDropUserList as $key => $value)
                             <option value="{{$value->anchor_id}}"> {{$value->comp_name}} </option>
                             @endforeach
                         </select>
                              {!! $errors->first('assigned_anchor', '<span class="error">:message</span>') !!}    
                              </div>
                           </div> 
                       
                </div>
                @endif
                <!-- <div class="row supplier_code">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="supplier_code">Supplier Code
                            <span class="mandatory">*</span>
                            </label>
                            <input type="text" name="supplier_code" id="supplier_code" value="{{ old('supplier_code') }}" class="form-control" tabindex="4" placeholder="Supplier Code">
                            {!! $errors->first('supplier_code', '<span class="error">:message</span>') !!}
                        </div>
                    </div>
                </div> -->
                <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch">Submit</button>  
          {!!
        Form::close()
        !!}
         </div>
     
@endsection
@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp
@section('jscript')
@if($operation_status == config('common.YES'))
<script>
    try {
    var p = window.parent;
    p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
    p.jQuery("#addAnchorFrm").modal('hide');
    p.location.reload();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        //check_exist_email: "{{ URL::route('check_exist_email') }}",
        check_exist_email: "{{ URL::route('check_exist_anchor_lead') }}",        
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {

            $(document).on('blur', '#email', function(){
              var email = $(this).val();
              if (!email.length) {
                  return false;
              }
              $.ajax({
                    url: messages.check_exist_email,
                    type: 'POST',
                    datatype: 'json',
                    async: false,
                    cache: false,
                    data: {
                        'email' : email,
                        anchor_id : $("#assigned_anchor").val(),
                        '_token' : messages.token
                    },
                  success: function(response){
                      console.log(response);
                     var nameclass = response.status ? 'success' : 'error';
                     $('#email-error').remove();
                      $('#email-error').removeClass('error success');
                     if(response.status == false){
                        $('#saveAnch').prop('disabled', true);
                         $('#email').after('<label id="email-error" class="'+ nameclass +'" for="email">'+response.message+'</label>');
                     }else{
                        $('#saveAnch').prop('disabled', false);
                        $('#email-error').remove();
                     }
                  }
              });
          });

            $('#anchor_user_type').on('change', function() {
                if ( this.value == 1)
                {
                    $(".supplier_code").show();
                }
                else
                {
                    $(".supplier_code").hide();
                }
            });
            
            $(document).on('input', '.number_format', function (event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40)
                    return;

                // format number
                $(this).val(function (index, value) {
                    return value.replace(/\D/g, "");
                });
            });
          
            $.validator.addMethod("alphabetsonly", function(value, element) {
                return this.optional(element) || /^[a-zA-Z]*$/.test(value);
            });

            $.validator.addMethod("alphaSpace", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]*$/.test(value);
            });
            
            $.validator.addMethod("alphabetsnspacendot", function(value, element) {
                return this.optional(element) || /^[a-zA-Z. ]*$/.test(value);
            });
            jQuery.validator.addMethod("noSpace", function(value, element) { 
                return value.indexOf(" ") < 0 && value != ""; 
            });

//            $.validator.addMethod("panValidator", function(value, element) {
//                var values = value;
//                var pannoformat = new RegExp('^[A-Z]{5}[0-9]{4}[A-Z]{1}$');
//
//                if (/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/.test(values)) {
//                    if (pannoformat.test(values)) {
//                        return true;
//                    } else {
//                        return false;
//                    }
//                } else {
//                    return false;
//                }
//            });
            
            $.validator.addMethod("isexistemail", function(value, element) {
                var email = value;
                let status = false;
                $.ajax({
                    url: messages.check_exist_email,
                    type: 'POST',
                    datatype: 'json',
                    async: false,
                    cache: false,
                    data: {
                        'email' : email,
                        anchor_id : $("#assigned_anchor").val(),
                        '_token' : messages.token
                    },
                    success: function(response){
                       if(response['status'] === true){
                          status = true;
                      }
                    }
                });
                return this.optional(element) || (status === true);
            });
            
            $("#email").on('blur', function(){
                $(this).rules('remove', 'isexistemail');
            });
            
            //$('#saveAnch').on('click', function (event) {
            $('#anchorForm').on('submit', function (event) {
                $('input.f_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                alphaSpace: true,
                                messages: {'alphaSpace' : "Only letters allowed" }
                            })
                });
                 $('input.l_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                noSpace: true,
                                messages: {'noSpace' : "Space not allowed" }
                            })
                });
                $('input.comp_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                alphabetsnspacendot: true,
                                messages: {'alphabetsnspacendot' : "Only letters, space and dot allowed" }
                            })
                });
//                $('input.pan_no').each(function () {
//                    $(this).rules("add",
//                        {
//                            required: true,
//                            maxlength: 10,
//                            panValidator: true,
//                            messages: {'panValidator': 'Please enter correct PAN No.'}
//                        })
//                });
                //$('input.email').each(function () {
                    $("#email").rules("add",
                    {
                        required: true,
                        email: true,
                        isexistemail: true,
                        emailExt:true,
                        messages:{'isexistemail' : "This email is already exist."}
                    });
                //});
                $('input.phone').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                number: true,
                                minlength:10,
                                messages: {'minlength' : "Number should be 10 digits"}
                            })
                });
                $('select.anchor_user_type').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.city').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('input.comp_addr').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });                
                $('input.pin_code').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                number: true,
                            })
                });
                
                $('select.assigned_anchor').each(function (){
                    $(this).rules("add",
                            {
                                required: true,
                            })
                }); 

                $('#supplier_code').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });               
                
                // test if form is valid                
                if (!$('#anchorForm').valid()) {
                    return false;
                }
                
                return true;                
            })
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate();
        });

</script>

<script>

     // this function is to accept only email

     jQuery.validator.addMethod("emailExt", function(value, element, param) {
            return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
         },'please enter a valid email');


         function checkFname(e) {
            let f_name = document.getElementById('f_name').value;

            if(!isNaN(e)) {
               document.getElementById('f_name').value = "";
            } else if(f_name.length >= 50) {
               document.getElementById('f_name').value = "";
            };
        }

         function checkLname(e) {
            let l_name = document.getElementById('l_name').value;

            if(!isNaN(e)) {
               document.getElementById('l_name').value = "";
            } else if(l_name.length >= 50) {
               document.getElementById('l_name').value = "";
            };
        }
         function checkMobile(e) {
            if(isNaN(e)) {
               document.getElementById('phone').value = "";
            };
        }
</script>

@endsection