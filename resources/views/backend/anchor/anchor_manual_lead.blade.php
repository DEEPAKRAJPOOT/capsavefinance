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
                                 <p><small style="font-size: 60%;">You can include first and middlle name (e.g Varun Dudani)</small></p>
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
                         </div>
                
                       <div class="row">
                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="phone">Mobile
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control numbercls phone" name="phone" id="phone" value="{{ old('phone') }}" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="" onkeyup="return checkMobile(this.value)">
                                    {!! $errors->first('phone', '<span class="error">:message</span>') !!}
                                 </div>
                              </div>
                        
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
                     </div>  
                   @php 
                   $role_id=Helpers::getUserRole(Auth::user()->user_id);
                   @endphp
                @if ($role_id[0]->pivot->role_id!= '11')
                <div  class="row">                    
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
<!--                     <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtEmail">State
                                 <span class="mandatory">*</span>
                                 </label>
                                  <select class="form-control state" name="state" id="state">
                                      <option value="">please select</option>
                                      <option value="1">state1</option>
                                      <option value="2">state2</option>
                                      <option value="3">state3</option>
                                  </select>
                              </div>
                           </div>

                           <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">City
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control city" name="city" id="city" tabindex="6" type="text" maxlength="10" placeholder="City" required="">
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
                           <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Pin Code
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control numbercls pin_code" name="pin_code" id="pin_code" tabindex="6" type="text" maxlength="6" placeholder="Pin Code" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Address
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control comp_addr" name="comp_addr" id="comp_addr" tabindex="6" type="text"  placeholder="Address" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>-->
                
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
        check_exist_email: "{{ URL::route('check_exist_email') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {
            $.validator.addMethod(
                    "regex",
                    function(value, element, regexp) {
                        var re = new RegExp(regexp);
                        return this.optional(element) || re.test(value);
                    },
                    "Please check your input."
            );
    
            $(document).on('keyup', '#email', function(){
              var email = $(this).val();
              if (!email.length) {
                  return false;
              }
              $.ajax({
                  url: messages.check_exist_email,
                  type: 'POST',
                  data: {
                      'email' : email,
                      '_token' : messages.token,
                  },
                  success: function(response){
                     var nameclass = response.status ? 'success' : 'error';
                      $('#email-error').removeClass('error success');
                     if($('#email-error').length){
                        $('#email-error').text(response.message).addClass(nameclass);
                     }else{
                         $('#email').after('<label id="email-error" class="'+ nameclass +'" for="email">'+response.message+'</label>');
                     }
                  }
              });
          });
            
            $('#saveAnch').on('click', function (event) {
                $('input.f_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                regex: "^[a-zA-Z ]+$",
                                messages: {
                                    regex: "Please enter only alpha characters with/without space."
                                }
                                
                            })
                });
                 $('input.l_name').each(function () {
                    $(this).rules("add",
                            {
                                required: true,
                                regex: "^[a-zA-Z]+$",
                                messages: {
                                    regex: "Please enter only alpha characters."
                                }                                
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
                
                // test if form is valid                
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