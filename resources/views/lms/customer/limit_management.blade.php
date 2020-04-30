@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])

<!--

<div class="content-wrapper">
    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
            <div class="card">


                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table  cellspacing="0" width="52%" role="grid" aria-describedby="invoive-listing_info" style="width: 52%;">

                                    <thead>
                                        <tr role="row">
                                            <th>Total Credit Assessed</th>
                                            <th>:</th><th>{{ number_format($userlimit->tot_limit_amt) }}</th>
                                            <th>Available Credit Assessed</th>
                                            <th>:</th> <th>{{ number_format($userlimit->tot_limit_amt-$avaliablecustomerLimit) }}   </th>


                                        </tr>
                                    </thead>
                                </table>


                                @foreach($offerlimit as $limit)   
                                 <table cellspacing="0" width="40%" role="grid" aria-describedby="invoive-listing_info" style="width: 40%; margin-top:20px;">


                                    <tr>
                                        <th>Product Type </th> <th>: </th><td>{{$limit->product->product_name}}</td>  
                                    </tr>
                                    <tr>
                                        <th>Proposed product limit </th> <th>: </th><td>{{number_format($limit->limit_amt)}}<td></td> 
                                    </tr>



                                    @php $sum=0;   $inv=0;  @endphp    
                                    @foreach($limit->offer as $val) 
                                    @php 
                                    $inv =  (new \App\Helpers\Helper)->invoiceAnchorLimitApprove($val);
                                    $sum+=$val->prgm_limit_amt; 
                                    @endphp  
                                    <tbody>
                                        <tr style="height:20px;">  <th></th></tr>
                                        <tr> 
                                            <th> Anchor</th><th>:</th><td>{{ $val->anchor->comp_name}}</td></tr>
                                    <th> Anchor sub program</th><th>:   </th><td>{{ $val->program->prgm_name}}</td></tr>

                                    </tr>
                                    </tbody>
                                    @endforeach      


                                </table>

                                <table cellspacing="5"  border="0"  width="80%" class= no-footer overview-table style="margin-top:30px;"> 
                                    <thead>
                                        <tr role="row">
                                            <th>Program Limit</th>
                                            <th>:</th> <th>{{number_format($sum)}}</th>
                                            <th>Utilize  Limit</th>
                                            <th>:</th> <th>{{number_format($inv)}} </th>

                                            <th>Available  Limit</th>
                                            <th>:</th> <th>{{number_format($sum-$inv)}} </th>
                                        </tr>
                                    </thead>


                                </table>

                                @endforeach
                            </div>





                        </div>
                    </div>
                </div>
            </div>
        </div></div>
</div>
</div>   

-->



<!--div class="row">
<div class=" col-sm-12">

<section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-2">Limit Management</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Limit Management</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>


    
   </div>
</div-->
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


@endsection

@section('additional_css')

@section('jscript')
