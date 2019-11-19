@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
           <form id="anchorForm" name="anchorForm" method="POST" action="{{route('add_anchor_reg')}}" onsubmit="return checkValidation();">
		@csrf
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="employee" id="employee" value="" class="form-control employee" tabindex="1" placeholder="Full Name" >
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="comp_name" id="comp_name" value="" class="form-control comp_name" tabindex="3" placeholder="Business Name" >
                              </div>
                           </div>
                        </div>
                           <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="" class="form-control email" tabindex="4" placeholder="Email" >
                              </div>
                           </div>

                           <div class="col-md-6">
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
                        </div>
                
                
                <div class="row">
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
                        </div>
                
                <button type="submit" class="btn btn-primary float-right" id="saveAnch">Submit</button>  
           </form>
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
                $('input.employee').each(function () {
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
                                required: true
                            })
                });
                $('input.phone').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                $('select.state').each(function () {
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
                $('input.pin_code').each(function () {
                    $(this).rules("add",
                            {
                                required: true
                            })
                });
                // test if form is valid 
                if ($('form#anchorForm').validate().form()) {
                    var form = $("#anchorForm");
                    $.ajax({
                        type: "POST",
                        url: '{{Route('add_anchor_reg')}}',
                        data: form.serialize(), // serializes the form's elements.
                        cache: false,
                        success: function (res)
                        {
                            if (res.status == 1)
                            {
                               
                                       $('#addAnchorFrm').dialog('close');
                                     window.location.href = "/anchor";
                            }
                        },
                        error: function (error)
                        {
                            console.log(error);
                        }

                    });
                } else {
                    console.log("does not validate");
                }
            })
            //$("#btnAddMore").on('click', addInput);
            $('form#anchorForm').validate();
        });

</script>
@endsection