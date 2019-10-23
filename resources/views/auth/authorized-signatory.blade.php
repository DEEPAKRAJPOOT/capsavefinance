@extends('layouts.guest')
@section('content')

 <div class="step-form pt-5">

	<div class="container">
		<ul id="progressbar">
			<li class="active">
				<div class="count-heading">Business Information </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/business-information.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li class="count-active">
				<div class="count-heading"> Authorized Signatory KYC </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/kyc.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading">Business Documents </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/business-document.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Associate Buyers </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/buyers.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
			<li>
				<div class="count-heading"> Associate Logistics </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('backend/signup-assets/images/logistics.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('backend/signup-assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
		</ul>

	</div>



	<div class="container">

		<div class="mt-4">
			<div class="form-heading p-3">
				<h2>Partner's Information

					<small> ( Please fill the Director's Information )

					</small>
				</h2>
			</div>
			<div class="col-md-12 form-design ">

				<div id="reg-box">
					<form>
						<div class=" form-fields">

							<div class="form-sections">

								<div class="row">
									<div class="col-md-8">
										<div class="col-md-12">
											<h3>Partner</h3>
										</div>

										<div class="col-md-12">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Partner Name
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter Partner Name" required="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">PAN Number
															<span class="mandatory">*</span>
														</label>
														<a href="javascript:void(0);" class="verify-owner-no">Verify</a>
														<input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter PAN Number" required="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtEmail">Aadhar Number
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Aadhar Number" required="">
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtCreditPeriod">Contact Email
															<span class="mandatory">*</span>
														</label>
														<input type="email" name="employee" id="employee" value="" class="form-control" placeholder="Enter Email" required="">
													</div>
												</div>
												<div class="col-md-4">

													<div class="form-group password-input">
														<label for="txtPassword">Mobile Number
															<span class="mandatory">*</span>
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Mobile Number" required="">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="txtEmail">Home Ph.
														</label>
														<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Home Ph.">
													</div>
												</div>
											</div>
											<div class="row">

												<div class="col-md-4">
													<div class="row">

														<div class="col-md-12">
															<div class="form-group password-input">
																<label for="txtPassword">DOB
																	<span class="mandatory">*</span>
																</label>
																<input type="date" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Pin Code" required="">
															</div>
														</div>
														<div class="col-md-12">
															<div class="form-group password-input">
																<label for="txtPassword">Gender
																	<span class="mandatory">*</span>
																</label>
																<select class="form-control ">
																	<option> Select Gender</option>
																	<option> Male </option>
																	<option>Female </option>


																</select>
															</div>
														</div>
													</div>

												</div>
												<div class="col-md-8">

													<div class="form-group password-input">
														<label for="txtPassword">Address
															<span class="mandatory">*</span>
														</label>
														<textarea class="form-control textarea" placeholder="Enter Address"></textarea>

										</div>	</div>
											</div>
										</div>

										

									</div>




									<div class="col-md-4">
										<div class="col-md-12 ">
											<h3 class="full-width">Documents

											</h3>
											<p><small>Maximum file upload size : 5MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX
												</small></p>
										</div>

										<div class="col-md-12">
											<div id="uploadsection3" class="fil-uploaddiv" style="display: block;">
												<div class="row ">
													<div class="col-md-12">
														<div class="justify-content-center d-flex">
															<label class="mb-0"><span class="file-icon"><img src="{{url('backend/signup-assets/images/contractdocs.svg')}}"> </span> PAN Card *	</label>
															<div class="ml-auto">
																<div class="file-browse">
																	<button class="btn custom-btn btn-sm"> <i class="fa fa-upload"></i> Upload</button>
																	<input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
																</div>
															</div>

														</div>
														<div id="filePath_1" class="filePath"></div>
														<hr>
													</div>
													<div class="col-md-12">
														<div class="justify-content-center d-flex">
															<label class="mb-0"><span class="file-icon"><img src="{{url('backend/signup-assets/images/contractdocs.svg')}}"> </span> Address Proof *	 </label>
															<div class="ml-auto">
																<div class="file-browse">
																	<button class="btn custom-btn btn-sm"> <i class="fa fa-upload"></i> Upload</button>
																	<input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
																</div>
															</div>

														</div>
														<div id="filePath_1" class="filePath"></div>
<hr>
													</div>
													<div class="col-md-12">
														<div class="justify-content-center d-flex">
															<label class="mb-0"><span class="file-icon"><img src="{{url('backend/signup-assets/images/contractdocs.svg')}}"> </span> Partner's Photo *		 </label>
															<div class="ml-auto">
																<div class="file-browse">
																	<button class="btn custom-btn btn-sm"> <i class="fa fa-upload"></i> Upload</button>
																	<input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
																</div>
															</div>

														</div>
														<div id="filePath_1" class="filePath"></div>

													</div>
												</div>
											</div>
										</div>

									</div>


								</div>
								<div class="d-flex btn-section ">
									<div class="col-md-4 ml-auto text-right">
										<input type="button" value="Back" class="btn btn-warning" onclick="window.location.href='business-information.php'">
										<input type="button" value="Save and Continue" class="btn btn-primary">
									</div>
								</div>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
<script>
function FileDetails(clicked_id) {
	// GET THE FILE INPUT.
	var fi = document.getElementById('file_' + clicked_id);
	// VALIDATE OR CHECK IF ANY FILE IS SELECTED.
	if (fi.files.length > 0) {

		// THE TOTAL FILE COUNT.
		var x = 'filePath_' + clicked_id;
		//var x = document.getElementById(id);alert(id);
		document.getElementById(x).innerHTML = '';

		// RUN A LOOP TO CHECK EACH SELECTED FILE.
		for (var i = 0; i <= fi.files.length - 1; i++) {

			var fname = fi.files.item(i).name; // THE NAME OF THE FILE.
			var fsize = fi.files.item(i).size; // THE SIZE OF THE FILE.
			// SHOW THE EXTRACTED DETAILS OF THE FILE.
			document.getElementById(x).innerHTML =
				'<div class="file-name" id="fileId"> ' +
				fname + '' + '<button type="button"  class="close-file" onclick="myDelete()" > x' + '</button>' + '</div>';
		}
	} else {
		alert('Please select a file.');
	}
}

function myDelete() {
	document.getElementById("fileId").remove();

}
	</script>
@endsection