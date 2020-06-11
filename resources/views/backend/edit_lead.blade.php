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
        is_accept: "{{ Session::get('is_accept') }}",
        message: "{{ Session::pull('message') }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script>   
     $(document).ready(function(){
         
     if(messages.is_accept == 1){
        var parent =  window.parent;     
        parent.jQuery("#editLead").modal('hide');  
        //window.parent.jQuery('#my-loading').css('display','block');        
        var alertmsg = '<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' + messages.message + '</div>';
        parent.$("#iframeMessage").html(alertmsg);
        parent.oTables.draw();
       //window.parent.location.href = messages.paypal_gatway;
    }
        
})
</script>
@endsection