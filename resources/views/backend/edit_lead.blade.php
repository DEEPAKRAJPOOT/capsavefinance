@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
                    <form id="anchorForm" name="anchorForm" action="{{route('update_backend_lead')}}" method="POST" >
                       @csrf
                           
                          <input type="hidden" name="userId" value="{{$userInfo->user_id?$userInfo->user_id:$userInfo->anchor_user_id}}" />
                          <input type="hidden" name="is_registerd" value="{{$userInfo->user_id?1:0}}" /> 
			                 <div class="row">
                          <div class="col-6">
                              <div class="form-group">
                                 <label for="f_name">First Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="f_name" id="f_name" value="{{$userInfo->f_name}}" class="form-control f_name" tabindex="1" placeholder="First Name" onkeyup="return checkFname(this.value)" >
                                 {!! $errors->first('f_name', '<span class="error">:message</span>') !!}                                 
                                 <p><small style="font-size: 80%;">You can include first and middlle name (e.g Varun Dudani)</small></p>
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="l_name">Last Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="l_name" id="l_name" value="{{$userInfo->l_name}}" class="form-control l_name" tabindex="1" placeholder="Last Name" onkeyup="return checkLname(this.value)" >
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
                                 <input type="text" name="biz_name" id="name" value="{{$userInfo->biz_name}}" class="form-control" tabindex="3" placeholder="Business Name" required="">
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
                                       <option value="1" {{ ($userInfo->user_type == "1" ? "selected":"") }}>Supplier</option>
                                       <option value="2" {{ ($userInfo->user_type == "2" ? "selected":"") }}>Buyer</option>
                                    </select>
                                    {!! $errors->first('anchor_user_type', '<span class="error">:message</span>') !!}
                                </div>
                            </div>
                            @endif
                           </div>
						            <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="{{$userInfo->email}}" class="form-control" tabindex="4" placeholder="Email" required>
                              </div>
                           </div>
                             <!-- <div class="col-md-6">
                                  <div class="form-group password-input">
                                     <label for="txtPassword">Password
                                     <span class="mandatory">*</span>
                                     </label>
                         <input class="form-control" name="password" id="passwordRegistration" type="password" tabindex="5" placeholder="Password" oninput="removeSpace(this);">
                                  </div>
                               </div>  -->
                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>
                                    <input type="hidden" name="anchor_user_id" id="anchor_user_id" value="{{$userInfo->anchor_user_id}}" >
                                    <input class="form-control numbercls" name="mobile_no" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" value='{{$userInfo->mobile_no}}' required>
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                                 
                              </div>
                              </div>
                        
                        <button type="submit" class="btn  btn-success btn-sm float-right" id="saveAnch" >Submit</button>  
                    </form>
         </div>
     



@endsection

@section('jscript')
<script>

    var messages = {
        check_exist_email: "{{ URL::route('check_exist_anchor_lead') }}",
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        is_accept: "{{ Session::get('is_accept') }}",
        message: "{{ Session::pull('message') }}",
    };
    
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>   
     $(document).ready(function(){

        $(document).on('blur', '#email', function(){
              
              var email = $(this).val();
              if (!email.length) {
                  return false;
              }
              if(emailExtention(email) == null){

                $('#saveAnch').prop('disabled', true);
                $('#email').after('<label id="email-error" class="error" for="email">Please enter valid email</label>');

              }else{
                  
                    $.ajax({
                        url: messages.check_exist_email,
                        type: 'POST',
                        datatype: 'json',
                        async: false,
                        cache: false,
                        data: {
                            'email' : email,
                            anchor_user_id : $("#anchor_user_id").val(),
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

              }
              
          });
    function emailExtention(value) {
        return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
        }
     if(messages.is_accept == 1){
        
        var parent =  window.parent;     
        parent.jQuery("#editLead").modal('hide');  
        //window.parent.jQuery('#my-loading').css('display','block');        
        var alertmsg = '<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' + messages.message + '</div>';
        parent.$("#iframeMessage").html(alertmsg);
        parent.oTables.draw();
        var isRegistered = "{{$userInfo->is_registered}}";
        if(isRegistered === '0')
          setInterval(function () {window.parent.location.href = "{{ URL::route('get_anchor_lead_list') }}"}, 1000);   
        else
          setInterval(function () {window.parent.location.href = "{{ URL::route('lead_list') }}"}, 1000);
          
        
    }
        
})
</script>
<script type="text/javascript">
        $(document).ready(function () {

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
             // this function is to accept only email
             


        jQuery.validator.addMethod("emailExt", function(value, element, param) {
            return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
         },'please enter a valid email');

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
                        anchor_user_id : $("#anchor_user_id").val(),
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
                                alphaSpace: true,
                                messages: {'alphaSpace' : "Only letters allowed" }
                            })
                });
                $('input.biz_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                alphabetsnspacendot: true,
                                messages: {'alphabetsnspacendot' : "Only letters, space and dot allowed" }
                            })
                });
                $('input.email').each(function () {
                    $("#email").rules("add",
                    {
                        required: true,
                        email: true,
                        isexistemail: true,
                        emailExt :true,
                        messages:{'isexistemail' : "This email is already exist."}
                    });
                });
                $('input.mobile_no').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                number: true,
                                minlength:10,
                                messages: {'minlength' : "Number should be 10 digits"}
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