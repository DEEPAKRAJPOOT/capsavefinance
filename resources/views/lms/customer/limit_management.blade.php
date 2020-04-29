@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])



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
@endsection

@section('additional_css')

@section('jscript')
