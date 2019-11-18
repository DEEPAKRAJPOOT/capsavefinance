@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
           <form id="business_information_form" method="POST" action="business-information-save" onsubmit="return checkValidation();">
		@csrf
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Full Name" required="">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Business Name" required="">
                              </div>
                           </div>
                        </div>
                                                <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="hidden" name="send_otp" id="send-otp" value="">
                                 <input type="email" name="email" id="email" value="" class="form-control" tabindex="4" placeholder="Email" required="">
                              </div>
                           </div>

                           <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="txtMobile">Mobile
                                    <span class="mandatory">*</span>
                                    </label>

                                    <input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                                 <input name="password" id="passwordRegistration" type="hidden" oninput="removeSpace(this);" value="nr40od5m">
                              </div>
                        </div>
            <button type="submit" class="btn btn-primary float-right">Submit</button>  
           </form>
         </div>
     



@endsection

@section('jscript')
<script>

    var messages = {
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
@endsection