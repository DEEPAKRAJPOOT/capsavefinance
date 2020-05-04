@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])

  
<div class="content-wrapper">
 <div class="row"> 
     <div class=" col-lg-12 m-auto">
      <div class="card">
        @foreach($userAppLimit as $uLimit) 
        @php 
            $obj =  new \App\Helpers\Helper;
            $credit_limit =  $obj->ProgramProductLimit($uLimit->app_limit_id);
          @endphp          
         <div class="card-body limit-management"> 
            <div class="limit-title"> 
                <div class="row" style="margin-top:10px;">
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Total Credit Assessed </label>
                 <div class="label-bottom">{{ number_format($uLimit->tot_limit_amt) }}
                 @if($uLimit->status==1) 
                  <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                 @else
                  <button type="button" class="badge badge-warning btn-sm float-right">Deactive </button>
                 @endif
                 </div>
               </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Available Credit Assessed	 </label>
                 <div class="label-bottom">{{number_format($uLimit->tot_limit_amt-$credit_limit)}} </div>
               </div>
             </div>
             </div>
         @foreach($uLimit->programLimit as $limit)                         
          <div class="limit-odd">  
           <div class="row" style="margin-top:20px;">
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Product Type </label>
                 <div class="label-bottom">{{$limit->product->product_name}}</div>
                </div>
             <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                 <label>Proposed product limit </label>
                 <div class="label-bottom">{{number_format($limit->limit_amt)}}
                  @if($limit->status==1) 
                  <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                 @else
                  <button type="button" class="badge badge-warning btn-sm float-right">Deactive </button>
                 @endif
                 </div>
               </div>
             </div>
          
            @foreach($limit->offer as $val) 
            @php 
        
            $inv_limit =  $obj->invoiceAnchorLimitApprove($val);
          
            @endphp  
           <div class="row" style="margin-top:20px;">
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
       
          @endforeach 
      </div>
  </div>
</div>

 </div>
    
@endsection

@section('additional_css')

@section('jscript')
