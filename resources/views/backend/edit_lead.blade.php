@extends('layouts.backend.admin_popup_layout')

@section('content')

<div class="modal-body text-left">
                    <form action="{{route('update_backend_lead')}}" method="POST">
                       @csrf

                          <input type="hidden" name="userId" value="{{$userInfo->user_id}}" /> 
			                 <div class="row">

                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtCreditPeriod">Full Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="f_name" id="employee" value="{{$userInfo->f_name}}" class="form-control" tabindex="1" placeholder="Full Name" required="">
                              </div>
                           </div>
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtSupplierName">Business Name
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="text" name="biz_name" id="name" value="{{$userInfo->biz_name}}" class="form-control" tabindex="3" placeholder="Business Name" required="">
                              </div>
                           </div>
                        </div>
						            <div class="row">
                           <div class="col-6">
                              <div class="form-group">
                                 <label for="txtEmail">Email
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input type="email" name="email" id="email" value="{{$userInfo->email}}" class="form-control" tabindex="4" placeholder="Email" required="" disabled="">
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
                                   
                                    <input class="form-control numbercls" name="mobile_no" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" value='{{$userInfo->mobile_no}}' required="" disabled="">
                                    <div class="failed">
                                       <div style="color:#FF0000">
                                          <small class="erro-sms" id="erro-sms">
                                          </small>
                                       </div>
                                    </div>
                                 </div>
                                 
                              </div>
                        </div>
                        <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
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