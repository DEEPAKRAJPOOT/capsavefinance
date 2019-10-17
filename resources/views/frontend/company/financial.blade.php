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
		     <li><a href="#">Company Details</a></li>
			 <li><a href="#">Address Details</a></li>
			 <li><a href="#">Shareholding Structure</a></li>
			 <li><a class="active" href="#">Financial Information</a></li>
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
		     <h3 class="h3-headline">Financial Information</h3>
		   </div>
		</div>   
	
	  <form id="financialform" method="post" action="{{route('financial')}}" class="needs-validation form" novalidate>
	 	@csrf
		<div class="row">
		  <div class="col-md-6">
			<div class="form-group inputborder-left">
			  <label for="pwd" class="error_msg">Yearly turnover in USD</label>
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text">$</span>
				</div>
				<input type="text" class="form-control number" name="yearly_usd" placeholder="Enter Value" value="{{isset($financial)?$financial->yearly_usd:'',old('yearly_usd')}}">
				<i style="color:red">{{$errors->first('yearly_usd')}}</i>
			  </div>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group inputborder-left">
			  <label for="pwd">Yearly profits in USD</label>
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text">$</span>
				</div>
				<input type="text" class="form-control number" name="yearly_profit_usd" placeholder="Enter Value"value="{{isset($financial)?$financial->yearly_profit_usd:'',old('yearly_profit_usd')}}">
				<i style="color:red">{{$errors->first('yearly_profit_usd')}}</i>
			  </div>
			</div>
		  </div>
		</div>					
		
	   <div class="row">
		  <div class="col-md-6">
			<div class="form-group inputborder-left">
			  <label for="pwd">Total debts in USD</label>
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text">$</span>
				</div>
				<input type="text" class="form-control number" name="total_debts_usd" placeholder="Enter Value" value="{{isset($financial)?$financial->total_debts_usd:'',old('yearly_debts_usd')}}">
				<i style="color:red">{{$errors->first('total_debts_usd')}}</i>
			  </div>
			</div>
		  </div>
		  <div class="col-md-6">
			<div class="form-group inputborder-left">
			  <label for="pwd">Total receivables in USD</label>
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text">$</span>
				</div>
				<input type="text" class="form-control number"  name="total_recei_usd"placeholder="Enter Value" value="{{isset($financial)?$financial->total_receivables_usd:'',old('yearly_recei_usd')}}">
				<i style="color:red">{{$errors->first('total_recei_usd')}}</i>
			  </div>
			</div>
		  </div>
		</div>		
		
		 <div class="row">
		  <div class="col-md-12">
			<div class="form-group inputborder-left">
			  <label for="pwd">Total cash in banks in USD</label>
			  <div class="input-group mb-3">
				<div class="input-group-prepend">
				  <span class="input-group-text">$</span>
				</div>
				<input type="text" class="form-control number" name="total_cash_usd" placeholder="Enter Value" value="{{isset($financial)?$financial->total_cash:'',old('yearly_cash_usd')}}">
				<i style="color:red">{{$errors->first('total_cash_usd')}}</i>
			  </div>
			</div>
		  </div>
		</div>	
		
		
		 
	<div class="row marT80 marB30">
         <div class="col-md-12 text-right">
		  <a href="{{route('shareholding_structure')}}" class="btn btn-prev">Previous</a>	
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


@include('frontend.company.companyscript')
@endsection