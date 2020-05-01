@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])

<div class="content-wrapper">
 <div class="row"> 
     <div class=" col-lg-12 m-auto">
      <div class="card">
         <div class="card-body limit-management"> 
            <div class="limit-title"> 
           <div class="row">
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Total Credit Assessed </label>
                 <div class="label-bottom">{{ number_format($userlimit->tot_limit_amt) }}</div>
               </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Available Credit Assessed	 </label>
                 <div class="label-bottom">{{ number_format($userlimit->tot_limit_amt-$avaliablecustomerLimit) }}   </div>
               </div>
             </div>
             </div>
         @foreach($offerlimit as $limit)                         
          <div class="limit-odd">  
           <div class="row">
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Product Type </label>
                 <div class="label-bottom">Supply Chain</div>
               </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Proposed product limit </label>
                 <div class="label-bottom">600,000</div>
               </div>
             </div>
          
            @foreach($limit->offer as $val) 
            @php 
        
            $inv_limit =  (new \App\Helpers\Helper)->invoiceAnchorLimitApprove($val);
          
            @endphp  
           <div class="row">
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Anchor </label>
                 <div class="label-bottom">{{ $val->anchor->comp_name}}</div>
               </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Anchor sub program </label>
                 <div class="label-bottom">{{ $val->program->prgm_name}}</div>
               </div>
               
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                 <label>Program Limit </label>
                 <div class="label-bottom">{{number_format($val->prgm_limit_amt)}}</div>
               </div>
             <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                 <label>Utilize Limit	 </label>
                 <div class="label-bottom">{{number_format($inv_limit)}}</div>
               </div>
               <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                 <label>Available Limit </label>
                 <div class="label-bottom">{{number_format($val->prgm_limit_amt-$inv_limit)}}</div>
               </div>
               </div>
             @endforeach 
        </div>
      
	@endforeach
         </div>
      </div>
  </div>
</div>

 </div>
     
  
@endsection

@section('additional_css')

@section('jscript')
