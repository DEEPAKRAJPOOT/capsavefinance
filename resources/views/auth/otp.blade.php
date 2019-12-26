@extends('layouts.guest')
@section('content')

<div class="form-content no-padding sign-up mt-5">
	<div class="row justify-content-center align-items-center m-0">
		<div class="col-md-6 form-design">

			<div id="reg-box">
                            

            <div class="content-wrap height-auto">
                <div class="login-section">  
	               <div class="thanks-conent text-center marT50">
	                      <p class="p-conent">{{trans('master.otpForm.enter_mobile_otp_below')}}</p>
	                </div>  
              	</div>
            </div>    
                            
				 <form class="registerForm" autocomplete="off" enctype="multipart/form-data" method="POST" action="{{ route('verify_otp') }}" id="registerForm">
                            {{ csrf_field() }}
				<div class="section-header">
					<h4 class="section-title"> Enter One Time (OTP) </h4>
				</div>
                            
				<div class="  form-fields">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="txtCreditPeriod">Enter OTP
									<span class="mandatory">*</span>
								</label>
                                                            
                                                                  
                                        <input type="text" class="form-control"  placeholder="Enter OTP"  name="otp" id="otp" required>
                                    
								<p class="text-danger">@if (count($errors) > 0)

@foreach ($errors->all() as $error)
{{ $error }}
@endforeach

@endif</p>
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
	</div>
	
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



