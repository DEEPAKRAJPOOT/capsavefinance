@extends('layouts.app')

@section('content')

<section>
    <div class="container">
        <div class="container">
            <div class="alertMsgBox hide"  id="msgBlockSuccess">

            </div>   
        </div> 
        <div class="container">
            <div class="alertMsgBox hide"  id="msgBlockError"></div>   
        </div>
        <div class="row">

            <div id="header" class="col-md-3">
                @include('layouts.user-inner.left-corp-menu')
                
            </div>
           <div class="col-md-9 dashbord-white">
	 <div class="form-section">
	   <div class="row marB10">
		   <div class="col-md-12">
		     <h3 class="h3-headline">Ultimate Beneficiary Owners</h3>
		   </div>
		</div>   
		
	 <div class="row marB10 marT20">
	  <div class="table-responsive">
		<table class="table table-ultimate">
			<thead>
			  <tr>
				<th>Name</th>
				<th>Shareholding %</th>
				<th>Actions</th>
				<th>Status</th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<td>Joy</td>
				<td>26%</td>
				<td><a href="#" class="kyc-color">KYC</a></td>
				<td><a href="#" class="kyc-pending">Pending</a></td>
			  </tr>
			  <tr>
				<td>Mary</td>
				<td>34%</td>
				<td><a href="#" class="kyc-color">KYC</a></td>
				<td><a href="#" class="kyc-pending">Pending</a></td>
			  </tr>
			  <tr>
				<td>Nida</td>
				<td>39%</td>
				<td><a href="#" class="kyc-color">KYC</a></td>
				<td><a href="#" class="kyc-pending">Pending</a></td>
			  </tr>
			</tbody>
		  </table>
	    </div>
	   </div>
	
	
	<div class="row marT140">
         <div class="col-md-12 text-right">
		  <a href="#" class="btn btn-prev">Previous</a>	
          <a href="#" class="btn btn-save">Save</a>		 
		  <a href="financial-info.html" class="btn btn-save">Save &amp; Next</a>
		 </div>
   </div>
	  
	  
	  
	  </div>
	</div>

        </div>	
    </div>

</section>
@endsection

@section('pageTitle')
Shareholding Structure
@endsection

@section('jscript')
<script src="{{ asset('backend/theme/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/shareHolding.js')}}"></script>
<script src="{{ asset('frontend/outside/js/validation/shareholderForm.js')}}"></script>
<script>
var messages = {
    social_media_form_limit: "{{ config('common.SOCIAL_MEDIA_LINK') }}",
    document_form_limit: "{{ config('common.DOCUMENT_LIMIT') }}",
    shareholder_save_ajax: "{{URL::route('shareholder_save_ajax')}}",
};

</script>
@endsection

