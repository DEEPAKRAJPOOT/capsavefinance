@extends('layouts.guest')
@section('content')

<div class="form-content no-padding sign-up mt-5">
	<div class="row justify-content-center align-items-center m-0">
		<div class="col-md-6 form-design">

			<div id="reg-box">
				 <form class="registerForm form form-cls" autocomplete="on" enctype="multipart/form-data" method="POST" action="{{ route('user_register_open') }}" id="registerForm">

                {{ csrf_field() }}

					<div class="section-header">
						<h4 class="section-title"> Registration</h4>
					</div>
					<div class="row form-fields">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtCreditPeriod">First Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="fname" id="employee" value="" class="form-control" tabindex="1" placeholder="First Name" required="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtSupplierName">Middle Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="mname" id="name" value="" class="form-control" tabindex="3" placeholder="Middle Name" required="">
									</div>
								</div>
							</div>
                                                    <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtCreditPeriod">Last Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="lname" id="employee" value="" class="form-control" tabindex="1" placeholder="Last Name" required="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtSupplierName">Business Name
											<span class="mandatory">*</span>
										</label>
										<input type="text" name="bname" id="name" value="" class="form-control" tabindex="3" placeholder="Business Name" required="">
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
									<div class="form-group password-input">
										<label for="txtPassword">Password
											<span class="mandatory">*</span>
										</label>
										<input class="form-control" name="password" id="passwordRegistration" type="password" tabindex="5" placeholder="Password" oninput="removeSpace(this);" required="">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="txtMobile">Mobile
											<span class="mandatory">*</span>
										</label>
										<div class="relative d-flex">
											<input class="form-control cont" name="phone-ext" id="phone-ext" type="text" value="+91" readonly="">
											<input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex btn-section sign-UP">
							<div class="col-md-4">
                                                            <input type="submit" value="Submit" class="btn btn-primary"> </div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="otp-section model-center-custom dark-bg form-design ">
		<div class="col-md-12 p-0">
			<form>
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
								<input type="text" name="otp" id="otp" value="" class="form-control" placeholder="Enter OTP" required="">
								<p class="small">OTP send successfully on your mobile and email.</p>
								<a href="#">Resend OTP</a>
							</div>
						</div>
					</div>
					<div class="row btn-section ">
						<div class="col-md-4"><input type="button" value="Submit" class="btn btn-primary" onclick="window.location.href = 'business-information.php'"> </div>
 					</div>
				</div>
			</form>
		</div>
	</div>
</div>
    @endsection



