@extends('layouts.app')


@section('content')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  
  <script src="{{ asset('frontend/datepicker/jquery-ui.js') }}"></script>  

<script type="text/javascript">
        $( function() {
       $(".datepicker" ).datepicker({
       maxDate: new Date()
    });


  } );
</script>

<section>
  <div class="container">
   <div class="row">

   	<div id="header" class="col-md-3">
	   @include('layouts.user-inner.left-corp-menu')
	</div>	

	
	<div class="col-md-9 dashbord-white">
	 <div class="form-section">
	   <div class="row marB10">
		   <div class="col-md-12">
		     <h3 class="h3-headline">Company Details</h3>
		   </div>
		</div>
		  
	  <form id="companydetails"  action="{{route('company_profile')}}" class="needs-validation form" novalidate method="post">
		@csrf
		<div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Name of the Customer: (as per Certificate of Incorporation/ Registration)</label>
			  	
			 <input type="text"class="form-control"  placeholder="Enter name of the Customer" name="customername" 
			  value="{{isset($companyprofile)?$companyprofile->customer_name:$userSignupdata->corp_name,old('customername')}}"> 
				

		      <i style="color:red">{{$errors->first('customername')}}</i>
			</div>
		  </div>
		</div>
		 	

		 	<div class="row">
	      <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Registration Number</label>


			  <input type="text" class="form-control"  placeholder="Registration Number" name="regisno" 
			  value="{{isset($companyprofile)?$companyprofile->registration_no: $userSignupdata->corp_license_number,old('regisno')}}">
			  <i style="color:red">{{$errors->first('regisno')}}</i>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group">
			  <label for="pwd">Registration Date</label>
			  <div class="input-group"> 
			  <input type="text" class="form-control datepicker"  placeholder="Select Registration Date" name="regisdate"
			   value="{{isset($companyprofile)?$companyprofile->registration_date: $userSignupdata->corp_date_of_formation,old('regisdate')}}" autocomplete="off">
			      <div class="input-group-append">
					<!-- <i class="fa fa-calendar-check-o"></i> -->
				</div>
				<i style="color:red">{{$errors->first('regisdate')}}</i>
	          </div>

			</div>
		  </div>
		
		</div>					
		 
		<div class="row">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Status:</label>
			  <select class="form-control" name="status"> 
			  	
			  	<option value="">Select Status</option>
			  	{{$status=Helpers::getCorpStatus()}}
			  	@foreach($status as $s)
			  	@if($companyprofile)
			  	
				<option value="{{$s->sid}}" {{($companyprofile->status==$s->sid)? 'selected':'Select Status'}}>{{$s->status}}</option>
				

				@else

					<option value="{{$s->sid}}">{{$s->status}}</option>

				@endif
				@endforeach
			  </select> 
			  <i style="color:red">{{$errors->first('status')}}</i>
			</div>
		  </div>
		</div>	

		<div class="row">
			  
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Nature of Business</label>
			    <textarea  class="form-control" rows="3" name="naturebusiness"> 
			  		{{isset($companyprofile)?$companyprofile->business_nature : ''}} 
			  	</textarea>
			  
			</div>
			<i style="color:red">{{$errors->first('naturebusiness')}}</i>
		  </div>
		</div>

	
		 <div class="row">
         <div class="col-md-12 text-right">
		  <a href="#" class="btn btn-prev">Previous</a>	
          <a href="#" class="btn btn-save">Save</a>		 
		  <button type="submit" class="btn btn-save">Save & Next</button>
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


    @include('frontend.company.companyscript')
@endsection
