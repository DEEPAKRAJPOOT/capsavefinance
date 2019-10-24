@extends('layouts.guest')
@section('content')
<center>
        @if (count($errors) > 0)
        <div class="alertMsgBox">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
            <div class="content-wrap height-auto">
                <div class="login-section">
                   

               <div class="thanks-conent marT50">
                        <h2 class="head-line2 marT20">{{trans('master.otpForm.thanks_email_verify')}}</h2>
                        <p class="p-conent">{{trans('master.otpForm.enter_otp_below')}}</p>
                    </div>  
              </div>
            </div></center>
 <div class="panel-body ">
     
        <div class=" model-center-custom ">
            
		<div class="col-md-12 p-0">
			  <form class="registerForm" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('verify_otp') }}" id="registerForm">
                            {{ csrf_field() }}
				<div class="section-header">
					<h4 class="section-title"> Enter One Time (OTP) </h4>
					<button class="close">x</button>
				</div>
                            
				<div class="  form-fields">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="txtCreditPeriod">Enter OTP
									<span class="mandatory">*</span>
								</label>
                                                            
                                                                  
                                        <input type="text" class="form-control"  placeholder="Enter OTP"  name="otp" id="otp" required>
                                    
								<p class="small">OTP send successfully on your mobile and email.</p>
								 <input type="hidden" name="token" id="token" value="{{$tokenarr['token'] }}" />
                                                                <a href="{{ route('resend_otp',['token' => $tokenarr['token']]) }}">Resend OTP</a>
							</div>
						</div>
					</div>
					<div class="row btn-section ">
						<div class="col-md-4">
                                                    
                                             <input type="submit" value="Submit" name="next" class="btn btn-primary" value="{{trans('master.otpForm.verify_otp')}}"> </div>
 					</div>
				</div>
			</form>
		</div>
	</div>
                    <!--startotp-->
</div>       

           
<script>
    var messages = {
        req_otp: "{{ trans('error_messages.req_otp') }}",
        invalid_otp: "{{ trans('error_messages.invalid_otp') }}",

    };
</script>
<script  type="text/javascript" src="{{ asset('frontend/outside/js/validation/otp.js') }}"></script>
    @endsection
@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>
 @endsection

    

