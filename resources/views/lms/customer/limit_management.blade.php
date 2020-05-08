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
                                  @if($uLimit->app->app_type==2) 
                                    @if($uLimit->status==1 && $uLimit->actual_end_date==Null) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($uLimit->status==1 && $uLimit->actual_end_date!=Null) 
                                   <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @else
                                   <button type="button" class="badge badge-warning btn-sm float-right">Pending </button>
                                    @endif
                                  @else
                                     @if($uLimit->status==0) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($uLimit->status==1) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @else
                                    <button type="button" class="badge badge-warning btn-sm float-right">Closed </button>
                                    @endif
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
                                 @if($uLimit->app->app_type==2)     
                                    @if($limit->status==1 && $limit->actual_end_date==Null) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($limit->status==1 && $limit->actual_end_date!=Null) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @else
                                    <button type="button" class="badge badge-warning btn-sm float-right">Pending </button>
                                    @endif
                                  @else
                                     @if($limit->status==0) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($limit->status==1) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @else
                                    <button type="button" class="badge badge-warning btn-sm float-right">Closed </button>
                                    @endif
                                @endif 
                                    
                                </div>
                            </div>
                        </div>

                        @foreach($limit->offer as $val) 
                        @php 

                        $inv_limit =  $obj->invoiceAnchorLimitApprove($val);
                        $getAdhoc   = $obj->getAdhoc($val);

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
                        @if($limit->status==1)  
                        <div class="row">
                            <div class="col-md-4" id="buttonDiv">
                                @can('add_adhoc_limit')
                                @if($val->program->is_adhoc_facility == 1)
                                <a data-toggle="modal" data-target="#addAdhocLimit" data-url ="{{ route('add_adhoc_limit', ['user_id' => request()->get('user_id'),'prgm_offer_id' => $val->prgm_offer_id ]) }}" data-height="350px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" >Add Adhoc Limit</a>
                                @endif
                                @endcan
                                
                            </div>
                        </div>
                       @endif
                        @foreach($getAdhoc as $adc) 
                        <div class="row" style="margin-top:20px;"> 
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Limit </label>
                                <div class="label-bottom">{{number_format($adc->limit_amt) }}</div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Adhoc Interest Rate </label>
                                <div class="label-bottom">{{ $adc->prgm_offer->adhoc_interest_rate }} %</div>
                            </div>
                            
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Start Date </label>
                                <div class="label-bottom">{{ date('d-m-Y',strtotime($adc->start_date)) }}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>End Date</label>
                                <div class="label-bottom">{{ date('d-m-Y',strtotime($adc->end_date)) }}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                @if($adc->status==0) 
                                <button type="button" class="badge badge-warning btn-sm float-right">Pending </button>
                                @elseif($adc->status==1) 
                                <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                @else
                                <button type="button" class="badge badge-danger btn-sm float-right">Reject </button>
                                @endif

                                @can('approve_adhoc_limit')
                                    @if(isset($adc->status) && $adc->status == 0)
                                    <a data-toggle="modal" data-target="#approveAdhocLimit" data-url ="{{ route('approve_adhoc_limit', ['user_id' => request()->get('user_id'), 'app_offer_adhoc_limit_id' => $adc->app_offer_adhoc_limit_id ]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2">Approve</a>
                                    @endif
                                @endcan
                                
                            </div>
                           
                        </div>
                        @endforeach 


                        @endforeach 
                    </div>

                    @endforeach
                </div>

                @endforeach 
            </div>
        </div>
    </div>
</div>

{!!Helpers::makeIframePopup('addAdhocLimit','Add Adhoc Limit', 'modal-lg')!!}
{!!Helpers::makeIframePopup('approveAdhocLimit','Confrim Approve Adhoc Limit', 'modal-xs')!!}

@endsection

@section('additional_css')

@section('jscript')
