@extends('layouts.app')

@section('content')



<section>
  <div class="container">
   <div class="row">

   	<div id="header" class="col-md-3">
<!--	   <div class="list-section">
	     <div class="kyc">
		   <h2>KYC</h2>
		   <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
		   <ul class="menu-left">
		     <li><a  href="#">Company Details</a></li>
			 <li><a class="active" href="#">Address Details</a></li>
			 <li><a href="#">Shareholding Structure</a></li>
			 <li><a href="#">Financial Information</a></li>
			  <li><a href="#">Documents & Declaration</a></li>
		   </ul>
		 
		</div>
	   </div>-->
            @include('layouts.user-inner.left-corp-menu')
	</div>
	<div class="col-md-9 dashbord-white">
	 <div class="form-section">
	   <div class="row marB10">
		   <div class="col-md-12">
		     <h3 class="h3-headline">Address Details</h3>
		   </div>
		</div>   

		
	  <form  id="addressdetails" action="{{'company-address'}}"  class="needs-validation form" novalidate method="post">
		@csrf
		<div class="row marT20">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Permanent/Registered Address:</label>
			</div>
		  </div>
		</div>
		 	
		<div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Country</label>
			  {!!
                    Form::select('country_id',
                    [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                    (isset($userArr->country_id) && !empty($userArr->country_id)) ? $userArr->country_id : (old('country_id') ? old('country_id') : ''),
                    array('id' => 'country_id','name' => 'country',
                    'class'=>'form-control select2Cls'))
                    !!}
			 <i style="color:red">{{$errors->first('country')}}</i>
			</div>
			

		  </div>
		  
		  
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">City</label>
			  <input type="text" name="city" id="city" class="form-control formempty" value="{{isset($address->city_id)?$address->city_id:'',old('city')}}" placeholder="Enter city">
 
			  <i style="color:red">{{$errors->first('city')}}</i>
			</div>
			
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Region</label>
			  <input type="text" class="form-control formempty" name="region" id="region" placeholder="Enter Region" value="{{isset($address->region)?$address->region:'',old('region')}}">
			  <i style="color:red">{{$errors->first('region')}}</i>
			</div>
			
		  </div>
		</div>					
		 
		<div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Building</label>
			  <input type="text" class="form-control" name="building" id="building" placeholder="Enter building" placeholder="Enter floor"
			   value="{{isset($address->building)?$address->building:'',old('building')}}">
			  <i style="color:red">{{$errors->first('building')}}</i>
			</div>
			
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Street</label>
			  <input type="text" class="form-control" name="street" id="street" placeholder="Enter Street" value="{{isset($address->street)?$address->street:'',old('street')}}">
			  <i style="color:red">{{$errors->first('street')}}</i>
			</div>
			
		  </div>
		</div>	
		 
		<div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Postal Code</label>
			  <input type="text" class="form-control" name="postalcode"id="postalcode"  placeholder="Enter postal code" value="{{isset($address->postal_code)?$address->postal_code:'',old('postalcode')}}">
			  <i style="color:red">{{$errors->first('postalcode')}}</i>
			</div>
			
		  </div>
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">P.O Box</label>
			  <input type="text" class="form-control" name="pobox" id="pobox" placeholder="Enter P.O. Box no." value="{{isset($address->po_box)?$address->po_box:'',old('pobox')}}">
			  <i style="color:red">{{$errors->first('pobox')}}</i>
			</div>
			
		  </div>
		</div>			 
		 
		<div class="row">
	      <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Email</label>
			  <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="{{isset($address->email)?$address->email:'',old('email')}}">
			  <i style="color:red">{{$errors->first('email')}}</i>
			</div>
			
		  </div>
		</div>		 
		 
		 
   <div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Telephone No.</label>
			  <input type="text" class="form-control" name="telephone" id="telephone" placeholder="Enter Telephone No" value="{{isset($address->telephone)?$address->telephone:'',old('telephone')}}">
			  <i style="color:red">{{$errors->first('telephone')}}</i>
			</div>
			
		  </div>
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Mobile No.</label>
			  <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter mobile no." value="{{isset($address->mobile)?$address->mobile:'',old('mobile')}}">
			  <i style="color:red">{{$errors->first('mobile')}}</i>
			</div>
			
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Fax No.</label>
			  <input type="text" class="form-control" name="faxno" id="faxno" placeholder="Enter fax no." value="{{isset($address->fax)?$address->fax:'',old('faxno')}}">
			<i style="color:red">{{$errors->first('faxno')}}</i>
			</div>
		  </div>
	</div>		 
		 
		 
		 
		 
		<div class="row marT20 marB5">
		  <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Address for Correspondence</label>
			</div>
		  </div>
		</div>		
				
					 <div class="row">
						<div class="col-md-12">		
						 <div class="form-group">
							  <div class="form-check-inline">
								  <label class="form-check-label" for="check2">
									<input type="checkbox" class="form-check-input" id="check2" name="vehicle2" value="0">Same as Registered Address
								  </label>
								</div>
						  </div>
						</div> 
					 </div>	 
						 
						<div class="row">
					      <div class="col-md-4">
							<div class="form-group">
							  <label for="pwd">Country</label>

							  {!!
                            Form::select('country_id',
                            [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                            (isset($userArr->country_id) && !empty($userArr->country_id)) ? $userArr->country_id : (old('country_id') ? old('country_id') : ''),
                            array('id' => 'corr_countryid','name' => 'corr_country',
                            'class'=>'form-control formempty'))
                            !!}

							<i style="color:red">{{$errors->first('country')}}</i>
							</div>
						  </div>
					     <div class="col-md-4">
							<div class="form-group">
								<label for="pwd">City</label>
								<input type="text"  class="form-control" name="corr_city" id="corr_city" value="{{isset($address->corre_city)?$address->corre_city:'',old('corr_city')}}" placeholder="Enter city" />

								<i style="color:red">{{$errors->first('corr_city')}}</i>
							</div>
						</div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Region</label>
			  <input type="text" class="form-control formempty" name="corr_region" id="corr_region" placeholder="Enter Region" value="{{isset($address->corre_region)?$address->corre_region:'',old('corr_region')}}">
			  <i style="color:red">{{$errors->first('corr_region')}}</i>
			</div>
		  </div>
		</div>					
		 
		<div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Building</label>
			  <input type="text" class="form-control formempty" name="corr_building" id="corr_building"  placeholder="Enter building" value="{{isset($address->corre_building)?$address->corre_building:'',old('corr_building')}}">
			  <i style="color:red">{{$errors->first('corr_building')}}</i>
			</div>
		  </div>
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Floor</label>
			  <input type="text" class="form-control formempty"  name="corr_floor" id="corr_floor"   placeholder="Enter floor"value="{{isset($address->corre_floor)?$address->corre_floor:'',old('corr_floor')}}">
			  <i style="color:red">{{$errors->first('corr_floor')}}</i>
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Street</label>
			  <input type="text" class="form-control formempty" name="corr_street" id="corr_street" placeholder="Enter Street" value="{{isset($address->corre_street)?$address->corre_street:'',old('corr_street')}}">
			  <i style="color:red">{{$errors->first('corr_street')}}</i>
			</div>
		  </div>
		</div>	
		 
		<div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Postal Code</label>
			  <input type="text" class="form-control formempty" name="corr_postal" id="corr_postal" placeholder="Enter postal code" value="{{isset($address->corre_postal_code)?$address->corre_postal_code:'',old('corr_postal')}}">
			  <i style="color:red">{{$errors->first('corr_postal')}}</i>
			</div>
		  </div>
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">P.O Box</label>
			  <input type="text"  name="corr_pobox" id="corr_pobox" class="form-control formempty"  placeholder="Enter P.O. Box no."value="{{isset($address->corre_po_box)?$address->corre_po_box:'',old('corr_pobox')}}">
			  <i style="color:red">{{$errors->first('corr_pobox')}}</i>
			</div>
		  </div>
		</div>			 
		 
		<div class="row">
	      <div class="col-md-12">
			<div class="form-group">
			  <label for="pwd">Email</label>
			  <input type="email" class="form-control formempty" name="corr_email" id="corr_email"  placeholder="Enter email" value="{{isset($address->corre_email)?$address->corre_email:'',old('corr_email')}}">
			  <i style="color:red">{{$errors->first('corr_email')}}</i>
			</div>
		  </div>
		</div>		 
		 		 
   <div class="row">
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Telephone No.</label>
			  <input type="text" name="corr_tele" id="corr_tele" class="form-control formempty"  placeholder="Enter email" value="{{isset($address->corre_telephone)?$address->corre_telephone:'',old('corr_tele')}}">
			  <i style="color:red">{{$errors->first('corr_tele')}}</i>
			</div>
		  </div>
	      <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Mobile No.</label>
			  <input type="text" name="corr_mobile" id="corr_mobile" class="form-control formempty"  placeholder="Enter mobile no." value="{{isset($address->corre_mobile)?$address->corre_mobile:'',old('corr_mobile')}}">
			  <i style="color:red">{{$errors->first('corr_mobile')}}</i>
			</div>
		  </div>
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="pwd">Fax No.</label>
			  <input type="text" name="corr_fax" id="corr_fax" class="form-control formempty"  placeholder="Enter fax no." value="{{isset($address->corre_fax)?$address->corre_fax:'',old('corr_fax')}}">
			  <i style="color:red">{{$errors->first('corr_fax')}}</i>
			</div>
		  </div>
	</div>
		 
	<div class="row marT40">
         <div class="col-md-12 text-right">
		  <a href="{{route('company_profile-show')}}" class="btn btn-prev">Previous</a>	
          <a href="#" class="btn btn-save">Save</a>		 
		  
		  <button  type="submit" class="btn btn-save"> Save & Next</button>
		 </div>
	</div>
	
	 </form>
	  </div>
	</div>
	
   </div>	
  </div>
</section>



<!--end-->

<script>
$(document).ready(function(){

	
$('#check2').click(function(){
	
	//$('#idCheckbox').prop('checked', true);
   //$('#idCheckbox').prop('checked', false);
	let checkboxval=$('#check2').val();
	if($(checkboxval==false))
	{	
		
		
		$('#corr_region').val($('#region').val());
		$('#corr_building').val($('#building').val());
		//$('#corr_floor').val($('#floor').val());
		$('#corr_street').val($('#street').val());
		$('#corr_pobox').val($('#pobox').val());
		$('#corr_postal').val($('#postalcode').val());
		$('#corr_email').val($('#email').val());
		$('#corr_tele').val($('#telephone').val());
		$('#corr_mobile').val($('#mobile').val());
		$('#corr_fax').val($('#faxno').val());

		let country=$('#country_id').val();
		//let city=$('#city').val();
	    $('#corr_countryid').val(country,"selected");
	    $('#corr_city').val($('#city').val());
	    
	    $('#check2').val('1');
	    $('#check2').prop('checked', true);

   } 
   if(checkboxval==true)
   {
   	   
    	$('#check2').val('0');
	    $('.formempty').val("");
	     $('#check2').prop('checked', false);
	}
	
	
})	

  
});



</script>


@include('frontend.company.companyscript')
@endsection


