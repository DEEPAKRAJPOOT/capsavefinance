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
                $mytime = \Carbon\Carbon::now();
                $limitCurDt =  $mytime->format('Y-m-d');
                $isLimitExpired = false;
                if ($uLimit->status==1 && !empty($uLimit->end_date)) {
                $limitEndDt   =  $uLimit->actual_end_date ? $uLimit->actual_end_date : $uLimit->end_date;
                $isLimitExpired = strtotime($limitCurDt) > strtotime($limitEndDt);
                }
                @endphp          
                <div class="card-body limit-management"> 
                    
                    <div class="limit-title"> 
                        <div class="row" style="margin-top:10px;">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Total Credit Assessed </label>
                                <div class="label-bottom">{{ number_format($uLimit->tot_limit_amt) }}
                                  @if($uLimit->app->app_type==2) 
                                    @if($uLimit->status==1 && $uLimit->actual_end_date==Null) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @elseif($uLimit->status==1 && $uLimit->actual_end_date!=Null) 
                                   <button type="button" class="badge {{ $isLimitExpired ? 'badge-danger' : 'badge-success' }} btn-sm float-right">{{ $isLimitExpired ? 'Limit Expired' : 'Active' }}</button>
                                   @elseif($uLimit->status==2) 
                                   <button type="button" class="badge badge-danger btn-sm float-right">Closed</button>
                                    @else
                                   <button type="button" class="badge badge-warning btn-sm float-right">Pending </button>
                                    @endif
                                  @else
                                     @if($uLimit->status==0) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($uLimit->status==1) 
                                    <button type="button" class="badge {{ $isLimitExpired ? 'badge-danger' : 'badge-success' }} btn-sm float-right">{{ $isLimitExpired ? 'Limit Expired' : 'Active' }} </button>
                                    @else
                                    <button type="button" class="badge badge-danger btn-sm float-right">Closed </button>
                                    @endif
                                @endif 
                                    
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Credit Assessed	 </label>
                                <div class="label-bottom">{{number_format($uLimit->tot_limit_amt-$credit_limit)}} </div>
                            </div>
                            @if($uLimit->start_date!=null)
                            @php 
                                $sDate  = $obj->convertDateTimeFormat($uLimit->start_date, $fromDateFormat='Y-m-d', $toDateFormat='d-m-Y');
                                $eDate  = $obj->convertDateTimeFormat($uLimit->end_date, $fromDateFormat='Y-m-d', $toDateFormat='d-m-Y');
                             @endphp
                            
                             <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Start Date	 </label>
                                <div class="label-bottom">{{$sDate}} </div>
                            </div>
                             <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>End Date	 </label>
                                <div class="label-bottom">{{$eDate}} </div>
                            </div>
                               <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                
                                @if($getAccountClosure > 0 && $uLimit->app->status==2)
                                <form  method="post" action="{{Route('account_closure')}}" enctype= multipart/form-data>
                                @csrf 
                                <input type="hidden" name="user_id" value="{{$userId}}">
                                @can('account_closure')
                                    <input type="submit" id="submit" name="submit" value="Account Closure" class="btn-sm btn btn-success">
                                @endcan
                                </form>
                                @elseif($uLimit->app->status==2)
                                   @can('account_closure')
                                        <button type="button" class="btn-sm badge badge-warning btn-sm float-right">Account closed </button>
                                   @endcan
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @foreach($uLimit->programLimit as $limit)                      
                    <div class="limit-odd">  
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Product Type </label>
                                <div class="label-bottom">{{$limit->product->product_name}}</div>
                                @if($uLimit->app->app_type==2)     
                                    @if($limit->status==1 && $limit->actual_end_date==Null) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Active </button>
                                    @elseif($limit->status==1 && $limit->actual_end_date!=Null) 
                                    <button type="button" class="badge {{ $isLimitExpired ? 'badge-danger' : 'badge-success' }} btn-sm float-right">{{ $isLimitExpired ? 'Limit Expired' : 'Active' }} </button>
                                    @elseif($uLimit->status==2) 
                                   <button type="button" class="badge badge-danger btn-sm float-right">Closed</button>
                                    @else
                                    <button type="button" class="badge badge-warning btn-sm float-right">Pending </button>
                                    @endif
                                  @else
                                     @if($limit->status==0) 
                                    <button type="button" class="badge badge-success btn-sm float-right">Inprocess </button>
                                    @elseif($limit->status==1) 
                                    <button type="button" class="badge {{ $isLimitExpired ? 'badge-danger' : 'badge-success' }} btn-sm float-right">{{ $isLimitExpired ? 'Limit Expired' : 'Active' }} </button>
                                    @else
                                    <button type="button" class="badge badge-danger btn-sm float-right">Closed </button>
                                    @endif
                                @endif 
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <label>Proposed product limit </label>
                                <div class="label-bottom">{{number_format($limit->limit_amt)}}
                                     
                                </div>
                            </div>
                        </div>

                        @foreach($limit->offer as $val) 
                        @php
                        $val['user_id']  = $uLimit->app->user_id;
                        $inv_limit =  $obj->anchorSupplierPrgmUtilizedLimitByInvoice($val);
                        $getAdhoc   = $obj->getAdhoc($val);
                        @endphp  
                        @if ($val->status !=2 )
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Anchor </label>
                                <div class="label-bottom">{{ $val->anchor->comp_name ?? ''}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Anchor sub program </label>
                                <div class="label-bottom">{{ $val->program->prgm_name ?? ''}}</div>
                            </div>

                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Program Limit </label>
                                <div class="label-bottom">{{number_format($val->prgm_limit_amt, 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Utilize Limit	 </label>
                                <div class="label-bottom">{{number_format($inv_limit, 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Limit </label>
                                <div class="label-bottom">{{number_format(($val->prgm_limit_amt-$inv_limit), 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                              
                                @if($limit->status==1 && $getAccountClosure > 0)  
                       
                                @can('add_adhoc_limit')
                                @if($val->program->is_adhoc_facility == 1 && !$isLimitExpired && !Helpers::checkActiveAdhocLimit($getAdhoc))
                                <a data-toggle="modal" style='color:white' data-target="#addAdhocLimit" data-url ="{{ route('add_adhoc_limit', ['user_id' => request()->get('user_id'),'prgm_offer_id' => $val->prgm_offer_id ]) }}" data-height="350px" data-width="100%" data-placement="top" class="btn-sm btn btn-success btn-sm ml-2">Add Adhoc Limit</a>
                                @endif
                                @endcan
                               @endif
                            </div>
                        </div>
                        
                        @foreach($getAdhoc as $adc) 
                        @php 
                        $mytime = \Carbon\Carbon::now();
                        $adhocLimitCurDt =  $mytime->format('Y-m-d');                                  
                        $adhocLimitEndDt   =  $adc->end_date;
                        $isAdhocLimitExpired = strtotime($adhocLimitCurDt) > strtotime($adhocLimitEndDt);
                        @endphp 
                        
                        <div class="row" style="margin-top:20px;"> 
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Limit </label>
                                <div class="label-bottom">{{number_format($adc->limit_amt) }}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Adhoc Interest Rate </label>
                                <div class="label-bottom">{{ $adc->prgm_offer->adhoc_interest_rate }} %</div>
                            </div>
                            
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Start Date </label>
                                <div class="label-bottom">{{ date('d-m-Y',strtotime($adc->start_date)) }}</div>
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-6 col-xs-12">
                                <label>End Date</label>
                                <div class="label-bottom">{{ date('d-m-Y',strtotime($adc->end_date)) }}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                @if($adc->status==0) 
                                <button type="button" class="badge badge-warning btn-sm">Pending </button>
                                @elseif($adc->status==1) 
                                <button type="button" class="badge {{ $isLimitExpired || $isAdhocLimitExpired ? 'badge-danger' : 'badge-success' }} btn-sm">{{ $isLimitExpired || $isAdhocLimitExpired ? 'Limit Expired' : 'Active' }} </button>
                                @else
                                <button type="button" class="badge badge-danger btn-sm">Closed </button>
                                @endif

                                @can('approve_adhoc_limit')
                                    @if(isset($adc->status) && $adc->status == 0 && !$isLimitExpired)
                                    <a data-toggle="modal" data-target="#approveAdhocLimit" data-url ="{{ route('approve_adhoc_limit', ['user_id' => request()->get('user_id'), 'app_offer_adhoc_limit_id' => $adc->app_offer_adhoc_limit_id ]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-1">Approve</a>
                                    @endif
                                @endcan

                                @can('view_adhoc_document')
                                </div>
                                @if (isset($adc->adhocDocument) && !empty($adc->adhocDocument->toArray()))
                                <div class="col-lg-1 col-md-6 col-sm-6 col-xs-12" >
                                <a data-toggle="modal" data-target="#viewAdhcDocument" data-url ="{{ route('view_adhoc_document', ['user_id' => request()->get('user_id'), 'app_offer_adhoc_limit_id' => $adc->app_offer_adhoc_limit_id ]) }}" data-height="475px" data-width="100%" data-placement="top" class="btn-sm btn btn-success">View Documents <i class="fa fa-eye" aria-hidden="true"></i></a>
                                </div>
                                @endif
                                @endcan
                            </div>
                            </div>
                        @endforeach 

                        @endif
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
{!!Helpers::makeIframePopup('approveAdhocLimit','Approve Adhoc Limit', 'modal-xs')!!}
{!!Helpers::makeIframePopup('viewAdhcDocument','View Adhoc Document', 'modal-lg')!!}

@endsection

@section('additional_css')

@section('jscript')
<script>
    $(document).on('click','#submit', function(){
    
       if(confirm('Are you sure you want to close this account?'))
       {
           return true;
       }
       else
       {
          return false; 
          
       }
    })
    
</script>
@endsection
