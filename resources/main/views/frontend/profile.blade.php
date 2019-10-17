@extends('layouts.app')

@section('content')


<section>
  <div class="container">
   <div class="row">
    <div id="header" class="col-md-3">
	   <div class="list-section">
	     <div class="kyc">
		   <h2>KYC</h2>
		   <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
		   <ul class="menu-left">
		     <li><a class="active" href="#">Personal Information</a></li>
			 <li><a href="#">Professional Information</a></li>
			 <li><a href="#">Financial Information</a></li>
			 <li><a href="#">Documents</a></li>
		   </ul>


		 </div>
	   </div>
	</div>
	<div class="col-md-9 dashbord-white">
	 <div class="form-section">
	   <div class="row marB10">
		   <div class="col-md-12">
		     <h3 class="h3-headline">Personal Information</h3>
		   </div>
		</div>
             <!--<form action="/action_page.php" class="needs-validation form" novalidate></form>-->

              <form class="loginForm" autocomplete="off" method="POST" action="{{ route('saveprofile') }}" id="frmLogin">
                {{ csrf_field() }}

		<div class="row">
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">First Name</label>
			  <input type="text" class="form-control"  placeholder="Enter First Name" name="pswd" required value="{{$profile->first_name}}">
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Middle Name</label>
			  <input type="text" class="form-control"  placeholder="Enter middle name" name="pswd" required>

			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Last Name</label>
			  <input type="text" class="form-control"  placeholder="Enter Last Name" name="pswd" required>
			</div>
		  </div>
		</div>

		<div class="row">
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Gender</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Nationality</option>
				<option>Indian</option>
				<option>Italian </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Date of Birth</label>
			  <div class="input-group">
			  <input type="text" class="form-control" placeholder="Select Date of Birth" name="pswd">
			      <div class="input-group-append">
					<i class="fa fa-calendar-check-o"></i>
				</div>
	          </div>

			</div>
		  </div>

		</div>

		<div class="row">
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Country of Birth</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Country of Birth</option>
				<option>India</option>
				<option>Italian </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">State of birth</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select State of birth</option>
				<option>Uttar Pradesh</option>
				<option>Madhya Pradesh </option>
			  </select>
			</div>
		  </div>

		   <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">City of birth</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select City of birth</option>
				<option>Noida</option>
				<option>Ghaziabad </option>
			  </select>
			</div>
		  </div>

		</div>


		<div class="row">
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Father's Name</label>
			  <input type="text" class="form-control"  placeholder="Enter Father's Name" name="pswd" required>
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Mother's first name</label>
			  <input type="text" class="form-control"  placeholder="Enter Mother's first name" name="pswd" required>

			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Mother's maiden name</label>
			  <input type="text" class="form-control"  placeholder="Enter Mother's maiden name" name="pswd" required>
			</div>
		  </div>

		</div>


		<div class="row">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Registration No</label>
			  <input type="text" class="form-control"  placeholder="Enter Registration No" name="pswd" required>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Registration  Place</label>
			  <input type="text" class="form-control"  placeholder="Enter Registration  Place" name="pswd" required>
			</div>
		  </div>
		</div>
	  <div class="row">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Nationality</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Nationality</option>
				<option>India</option>
				<option>Italian </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Secondary Nationality</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Secondary Nationality</option>
				<option>Uttar Pradesh</option>
				<option>Madhya Pradesh </option>
			  </select>
			</div>
		  </div>
		</div>

		<div class="row">
		  <div class="col-md-3">
			<div class="form-group">
			  <label for="pwd">Document Type</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Document Type</option>
				<option>India</option>
				<option>Italian </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-3">
			<div class="form-group">
			  <label for="pwd">Document Number</label>
			  <input type="text" class="form-control"  placeholder="Document Number" name="pswd" required>
			</div>
		  </div>
		  <div class="col-md-3">
			<div class="form-group">
			  <label for="pwd">Issuance Date</label>
			  <input type="text" class="form-control"  placeholder="Issuance Date" name="pswd" required>
			</div>
		  </div>
		  <div class="col-md-3">
			<div class="form-group">
			  <label for="pwd">Expiry Date</label>
			  <input type="text" class="form-control"  placeholder="Expiry Date" name="pswd" required>
			<a href="#" class="pull-right marT10 text-color">+Add</a>
			</div>
		  </div>

		</div>
		<div class="row">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Social Media</label>
			  <select class="form-control" name="uname" required="">
			    <option>Social Media Links</option>
				<option>India</option>
				<option>Italian </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Social Media Link</label>
			  <input type="text" class="form-control"  placeholder="Enter Social Media Link">
			  <a href="#" class="pull-right marT10 text-color">+Add</a>
			</div>
		  </div>
		</div>

		<div class="row">
		  <div class="col-md-12">
		   <hr></hr>
		  </div>
		</div>


		<div class="row">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Residence Status</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Residence Status</option>
				<option>Yes</option>
				<option>No </option>
			  </select>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Family Status</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Family Status</option>
				<option>Yes</option>
				<option>Yes</option>
			  </select>
			</div>
		  </div>
		</div>


		<div class="row">
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Legal Guardian’s Name (if applicable)</label>
			  <input type="text" class="form-control"  placeholder="Enter Guardian's name" name="pswd">
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Legal maturity date</label>
			  <input type="text" class="form-control"  placeholder="Legal Maturity Date" name="pswd">

			</div>
		  </div>
		</div>


	  <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Educational Level</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select Educational Level</option>
				<option>Yes</option>
				<option>No </option>
			  </select>
			</div>
		  </div>
		</div>

		<div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Do you have any residency card</label>
			  <select class="form-control" name="uname" required="">
			    <option>Select dropdown item</option>
				<option>Yes</option>
				<option>No </option>
			  </select>
			</div>
		  </div>
		</div>

       <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Do you hold or have ever held a</label>
			  <div class="clearfix marB15"></div>
			  <div class="form-check-inline">
				  <label class="form-check-label" for="check1">
					<input type="checkbox" class="form-check-input" id="check1" name="vehicle1" value="something" checked>Senior position in the public sector
				  </label>
				</div>
				<div class="form-check-inline">
				  <label class="form-check-label" for="check2">
					<input type="checkbox" class="form-check-input" id="check2" name="vehicle2" value="something">Political position
				  </label>
				</div>

			</div>
		  </div>
		</div>


		<div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">If yes, please specify position (s)</label>
			  <textarea class="form-control" rows="3"></textarea>
			</div>
		  </div>
		</div>

       <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Do you hold or have ever held a</label>
			  <div class="clearfix marB15"></div>
			  <div class="form-check-inline">
				  <label class="form-check-label" for="check1">
					<input type="checkbox" class="form-check-input" id="check1" name="vehicle1" value="something" checked>Senior position in the public sector
				  </label>
				</div>
				<div class="form-check-inline">
				  <label class="form-check-label" for="check2">
					<input type="checkbox" class="form-check-input" id="check2" name="vehicle2" value="something">Political position
				  </label>
				</div>

			</div>
		  </div>
		</div>


		<div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">If yes, please specify position (s)</label>
			  <textarea class="form-control" rows="3"></textarea>
			</div>
		  </div>
		</div>





		 <div class="row">
         <div class="col-md-12 text-right">
		  <a href="#" class="btn btn-prev">Previous</a>
          <a href="#" class="btn btn-save">Save</a>
		  <a href="family-information.html" class="btn btn-save">Save & Next</a>
		 </div>
		</div>
	 </form>
	  </div>
	</div>

   </div>
  </div>
</section>

<!--models-->
  <div class="modal model-popup" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <h4 class="headline-h4 marB15">Dear Applicant;</h4>
		  <p>Welcome to the Compliance platform of Dexter Capital Financial Consultancy LLC. </p>

		 <p> According to the United Arab Emirates rules and regulations and the International applicable laws, you are kindly requested to proceed with the due diligence application allowing you to validate your profile and access many financial platforms.</p>
		<p> Dexter Capital Financial Consultancy LLC being regulated by Securities and Commodities Authority in the UAE, is committed to maintain all your information confidential and highly protected by the most sophisticated security tools and is in full compliance with the requirements of the European Union related to the General Data Protection Regulation (GDPR). <a href="https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en"> https://ec.europa.eu/info/law/law-topic/data-protection/data-protection-eu_en</a></p>
        </div>

      </div>
    </div>
  </div>

<!--end-->

<!-- / END SECTION -->
@endsection
@section('pageTitle')
Profile
@endsection


@section('jscript')
<script type="text/javascript">
    $(window).on('load',function(){
        $('#myModal').modal('show');
    });
</script>
@endsection