<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/icon" href="#" />
    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/frontend/outside/lending-asset/css/site.css') }}">


    <title>{{trans('master.companyDetails.title')}}</title>
</head>
<!-- dashboard part -->
<body class="login-page">
<section>
 <div class="content-wrap height-100">
    <div class="login-section sign-box-bk">
	  <div class="logo-box text-center marB20">
	    <a href="index.html"><img src="{{ asset('frontend/outside/images/00_dexter.svg') }}" class="img-responsive"></a>
		<h2 class="head-line2 marT25">{{trans('master.companyDetails.heading')}}</h2>
	  </div>

	  <div class="sign-up-box">

		<form action="/action_page.php" class="needs-validation form form-cls" novalidate>
		  <div class="row">
		   <div class="col-md-12">
			<div class="form-group">
			  <label for="uname">{{trans('master.companyDetails.nationality')}}</label>
			  <select  class="form-control"  name="uname" required>
			    <option>{{trans('master.companyDetails.select_nationality')}}</option>
				<option>Indian</option>
				<option>Italian </option>
			  </select>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
			</div>
		  </div>
		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.fname')}}</label>
			  <input type="text" class="form-control"  placeholder="{{trans('master.companyDetails.fname')}}" name="pswd" required>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
		  </div>
         </div>

		 <div class="row">
		   <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.mname')}}</label>
			  <input type="text" class="form-control" placeholder="{{trans('master.companyDetails.enter_mname')}}" name="pswd" required>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.lname')}}</label>
			  <input type="text" class="form-control" placeholder="{{trans('master.companyDetails.enter_lname')}}" name="pswd" required>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
		  </div>
		  </div>

		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.dob')}}</label>
			  <div class="input-group">
			  <input type="text" class="form-control"  placeholder="{{trans('master.companyDetails.select_dob')}}" name="pswd">
			      <div class="input-group-append">
					<i class="fa fa-calendar-check-o"></i>
				</div>
	          </div>


			</div>
		  </div>
         </div>

		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.official_email_add')}}</label>
			  <input type="text" class="form-control"  placeholder="{{trans('master.companyDetails.enter_email')}}" name="pswd" required>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
		  </div>
         </div>
		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">{{trans('master.companyDetails.official_mob_no')}}</label>
			  <input type="text" class="form-control"  placeholder="{{trans('master.companyDetails.enter_mob_no')}}" name="pswd" required>
			  <div class="valid-feedback">Valid.</div>
			  <div class="invalid-feedback">Please fill out this field.</div>
			</div>
		  </div>
         </div>

		 <div class="row">
		 <div class="col-md-12">
		    <p class="text-center have-account marB15">{{trans('master.companyDetails.ques')}}<a href="#">{{trans('master.companyDetails.sign_in')}}</a></p>
		 </div>
         <div class="col-md-12">
		  <a href="thanks.html" class="btn btn-sign verify-btn">{{trans('master.companyDetails.sign_up')}}</a>
		 </div>
		</div>
		  </form>


	  </div>


	</div>
 </div>
</section>

     
     <script type="text/javascript" src="{{ asset('frontend/outside/js/jquery-3.2.1.min.js') }}"></script>
     <script type="text/javascript" src="{{ asset('frontend/outside/js/bootstrap.min.js') }}"></script>


</body>
</html>


