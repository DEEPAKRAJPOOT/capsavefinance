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
                                 <label for="txtCreditPeriod">First Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="f_name" id="f_name" value="" class="form-control f_name" tabindex="1" placeholder="First Name" >
                              </div>
                           </div>
                            <div class="col-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Last Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="l_name" id="l_name" value="" class="form-control l_name" tabindex="1" placeholder="Last Name" >
                              </div>
                           </div>
                            </div>
                           <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="comp_name" id="comp_name" value="" class="form-control comp_name" tabindex="3" placeholder="Business Name" >
                              </div>
                           </div>
                        
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="" class="form-control email" tabindex="4" placeholder="Email" >
                                 <span class="email_val"></span>
                              </div>
                           </div>
                         </div>
                
                       <div class="row">
                           <div class="col-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control numbercls phone" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">User Type
                                 <span class="mandatory">*</span>
                                 </label>
                                  <select class="form-control anchor_user_type" name="anchor_user_type" id="anchor_user_type">
                                      <option value="">Please Select</option>
                                      <option value="1">Supplier</option>
                                      <option value="2">Buyer</option>
                                  </select>
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
                                 <label for="txtEmail">Anchor
                                 <span class="mandatory">*</span>
                                 </label>        
                                     <select class="form-control assigned_anchor" name="assigned_anchor" id="assigned_anchor">
                            <option value="">Please Select</option>
                             @foreach($anchDropUserList as $key => $value)
                             <option value="{{$value->anchor_id}}"> {{$value->comp_name}} </option>
                             @endforeach
                         </select>
                                  
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

@section('jscript')

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
            
//            $.validator.addMethod('check_email', function(value,element,param){
//                var email = $('#email').val();
//                $.ajax({
//                    url: messages.check_exist_email,
//                    type: 'POST',
//                    data: {
//                        'email_check' : 1,
//                        'email' : email,
//                        '_token' : messages.token
//                    },
//                    success: function(response){
//                        if (response === 'true' ) {
//                            email_state = false;
//                            $('#email').parent().removeClass("error");
//                            $('#email').parent().addClass("form_error");
//                            $('#email').siblings("span").text('Sorry... Email already exists');
//                            
//                        }else if (response === 'false') {
//                            email_state = true;
//                            $('#email').parent().removeClass("form_error");
//                            $('#email').parent().addClass("form_success");
//                            $('#email').siblings("span").text('');
//                            return email_state;
//                        }
//                    }
//                });
//            },'');

            $(document).on('keyup', '#email', function(){
              var email = $(this).val();
              if (!email.length) {
                  return false;
              }
              $.ajax({
                  url: messages.check_exist_email,
                  type: 'POST',
                  data: {
                      'email_check' : 1,
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
                                required: true
                            })
                });
                 $('input.l_name').each(function () {
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
//                        email: true,
//                        check_email: '#email',
                    });
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
@endsection