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
                                      <option value="">please select</option>
                                      <option value="1">Supplier</option>
                                      <option value="2">Buyer</option>
                                  </select>
                              </div>
                           </div>
                     </div>
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
                
                <button type="submit" class="btn btn-primary float-right" id="saveAnch">Submit</button>  
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
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script type="text/javascript">
        $(document).ready(function () {
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
                                 email: true,
//                                remote: {
//                                url: messages.check_exist_user,
//                                type: 'post',
//                                data: {
//                                'username': $('#email').val()
//                                }
//                            }
                            
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
                // test if form is valid                
            })
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate();
        });

</script>
@endsection