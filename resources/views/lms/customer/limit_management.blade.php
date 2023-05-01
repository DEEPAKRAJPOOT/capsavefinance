@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])

@php  $customberOfferLimit = 0;$j=0;$count = 0;@endphp
@php $obj =  new \App\Helpers\Helper;@endphp
@foreach($userActiveAppLimit as $uLimit) 
    @foreach($uLimit->supplyProgramLimit as $limit) 
        @foreach($limit->offer as $val)
                @php
                $val['user_id']  = $uLimit->app->user_id;
                $anchorDatas[$j]['anchor_id'] = $val->anchor->anchor_id;
                $anchorDatas[$j]['anchor_name'] = $val->anchor->comp_name;
                $j++;
                @endphp  
        @endforeach
    @endforeach
@endforeach
@php $tempArr = array_unique(array_column($anchorDatas, 'anchor_id'));
$anchorDatas = array_intersect_key($anchorDatas, $tempArr); @endphp
<div class="content-wrapper">
    <div class="row">
    @foreach($anchorDatas as $anchorData)
        <div class=" col-lg-12 m-auto">
            <div class="card">
            @php $inv_Total_limit = 0; $customberOfferLimit = 0;@endphp 
            @foreach($userAppLimit as $uLimit) 
                @foreach($uLimit->supplyProgramLimit as $limit) 
                    @foreach($limit->offer as $val) 
                    @php $val['user_id'] = $uLimit->app->user_id; 
                        if($anchorData['anchor_id'] == $val->anchor->anchor_id) { 
                            $inv_Total_limit += $obj->anchorSupplierUtilizedLimitByInvoiceByPrgm($val);
                        } 
                    @endphp 
                    @endforeach 
                @endforeach 
            @endforeach

            @foreach($userActiveAppLimit as $uLimit) 
            @foreach($uLimit->supplyProgramLimit as $limit) 
            @foreach($limit->offer as $val)
                @php
                $val['user_id']  = $uLimit->app->user_id;
                if($anchorData['anchor_id'] == $val->anchor->anchor_id) {
                $customberOfferLimit += $val->prgm_limit_amt;
                }
                $j++;
                @endphp  
            @endforeach
            @endforeach
            @endforeach

            <table id="customerList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                <thead>   
                    <tr role="row"> 
                        <th>{{$anchorData['anchor_name']}}</th>                                                   
                        <th>Total Customer Limit</th>       
                        <th>₹ {{number_format($customberOfferLimit, 2)}}</th>       
                        <th>Utilized Customer Limit</th>		
                        <th>₹ {{number_format($inv_Total_limit, 2)}}</th>
                        <th>Available Customer Limit</th>
                        <th>₹ {{number_format(($customberOfferLimit - $inv_Total_limit), 2)}}</th>
                    </tr>
            </thead> 
            </table>
          
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
                @if($uLimit['supplyProgramLimit'][0]['offer'][0]['anchor_id'] == $anchorData['anchor_id'])           
                <div class="card-body limit-management"> 
                    @if($uLimit->is_deleted ==0 && $uLimit->status != 0)
                    <div class="limit-title"> 
                        <div class="row" style="margin-top:10px;">
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
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
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Credit Assessed	 </label>
                                <div class="label-bottom">{{number_format($uLimit->tot_limit_amt-$credit_limit)}} </div>
                            </div>
                            @if($uLimit->start_date!=null)
                            @php 
                                $sDate  = $obj->convertDateTimeFormat($uLimit->start_date, $fromDateFormat='Y-m-d', $toDateFormat='d-m-Y');
                                $eDate  = $obj->convertDateTimeFormat($uLimit->end_date, $fromDateFormat='Y-m-d', $toDateFormat='d-m-Y');
                                $limitExpDate = $limitExpDateCheck = '';
                                if ($uLimit->limit_expiration_date != null){
                                    $limitExpDate  = $obj->convertDateTimeFormat($uLimit->limit_expiration_date, $fromDateFormat='Y-m-d', $toDateFormat='d-m-Y');
                                    $limitExpDateCheck  = date('Y-m-d', strtotime($limitExpDate));
                                }
                                $readInDays = config('lms.SHOW_EDIT_REVIEW_DATE_BUTTON_IN_DAYS').' days';
                                $endDate = $uLimit->end_date;
				                $editReviewButtonShowDate = date('Y-m-d', strtotime('-'.$readInDays,strtotime($endDate)));
                                $curDate = $limitCurDt;//'2022-06-23';
                             @endphp
                            
                             <div class="col-lg-1 col-md-6 col-sm-6 col-xs-12">
                                <label>Start Date	 </label>
                                <div class="label-bottom">{{$sDate}} </div>
                            </div>
                             <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>End Date / Review Date </label>
                                <div class="label-bottom">{{$eDate}}
                                    @can('edit_review_date')
                                        @if($getAccountClosure > 0 && $uLimit->app->status==2)
                                        @php
                                            $isShowReviewButton = false;
                                            if ($editReviewButtonShowDate == $curDate){
                                                $isShowReviewButton = true;
                                            }elseif (($curDate > $editReviewButtonShowDate) && ($curDate < $endDate)) {
                                                $isShowReviewButton = true;
                                                if ($endDate == $limitExpDateCheck && $limitExpDate != ''){
                                                    $isShowReviewButton = false;
                                                }
                                            }
                                        @endphp
                                        @if($isShowReviewButton) 
                                        <a data-toggle="modal" class="badge btn-success btn-sm ml-1" style="color: #fff;
                                        cursor: pointer;" data-target="#editReviewDate" data-url ="{{ route('edit_review_date', ['user_id' => request()->get('user_id'),'app_limit_id' => $uLimit->app_limit_id ]) }}" data-height="380px" data-width="100%" data-placement="top" title="Edit Review Date">
                                    <i class="fa fa-pencil-square-o"></i> Review Date</a>
                                        @endif
                                    @endif
                                @endcan
                                </div>
                            </div>
                              @if ($limitExpDate != '')
                              <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                    <label>Limit Expiration Date	 </label>
                                    <div class="label-bottom">{{$limitExpDate}} </div>
                                </div>
                              @endif
                               <div class="col-lg-1 col-md-6 col-sm-6 col-xs-12">
                                
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
                            @can('edit_review_date')
                            @if($getAccountClosure > 0 && $uLimit->app->status==2)
                              @if(count($getAppLimitReview) > 0)
                               <div class="col-lg-1 col-md-6 col-sm-6 col-xs-12 ml-5">
                                <a class="btn-sm badge badge-success btn-sm" data-toggle="collapse" href="#scollapse{{ $uLimit->app_limit_id }}" role="button" aria-expanded="false" aria-controls="scollapse{{ $uLimit->app_limit_id }}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                </div>
                               @endif
                              @endif
                             @endcan
                            @endif
                        </div>
                        @can('edit_review_date')
                            @if($getAccountClosure > 0 && $uLimit->app->status==2)
                                @if(count($getAppLimitReview) > 0)
                                <div id="scollapse{{ $uLimit->app_limit_id }}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                    <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                    <thead>
                                    <tr role="row">
                                        <th width="10%" >Review Date</td>
                                        @can('download_review_approval_file')
                                        <th width="10%" >Download File</td>   
                                        @endcan
                                        <th width="10%" >Comment</td>
                                        <th width="10%" >Status</td>
                                        <th width="10%" >Created By</td>
                                        <th width="10%" >Created At</td>
                                        <th width="10%" >Updated By</td>
                                        <th width="10%" >Updated At</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($getAppLimitReview as $vAppLimitReview)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($vAppLimitReview->review_date)->format('d-m-Y') }}</td>
                                            @can('download_review_approval_file')
                                            <td>
                                                @if($vAppLimitReview->file_id)
                                                <a href="{{ route('download_review_approval_file',['file_id'=>$vAppLimitReview->file_id]) }}" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                @else
                                                N/A
                                                @endif
                                            </td>   
                                            @endcan
                                            <td>{{ $vAppLimitReview->comment_txt??'N/A' }}</td>
                                            <td>
                                                @if ($vAppLimitReview->status == 1)
                                                <span class="badge badge-warning">Pending </span> 
                                                @else
                                                <span class="badge badge-success">Approved </span>  
                                                @endif
                                            </td>
                                            <td>{{ \Helpers::getUserName($vAppLimitReview->created_by) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($vAppLimitReview->created_at)->format('d-m-Y h:i:s') }}</td>
                                            @if ($vAppLimitReview->updated_by)
                                            <td>{{ \Helpers::getUserName($vAppLimitReview->updated_by) }}</td>
                                            @else
                                            <td>N/A</td>
                                            @endif
                                            @if ($vAppLimitReview->updated_at)
                                            <td>{{ \Carbon\Carbon::parse($vAppLimitReview->updated_at)->format('d-m-Y h:i:s') }}</td>
                                            @else
                                            <td>N/A</td>
                                            @endif
                                        </tr>  
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                                @endif
                            @endif
                        @endcan
                    </div>



                    @foreach($uLimit->supplyProgramLimit as $limit)                      
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
                        $inv_limit =  $obj->anchorSupplierUtilizedLimitByInvoiceByPrgm($val);
                        $getAdhoc   = $obj->getAdhoc($val);
                        @endphp  
                        @if ($val->status !=2 )
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Anchor </label>
                                <div class="label-bottom">{{ $val->anchor->comp_name ?? ''}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Anchor Sub Program </label>
                                <div class="label-bottom">{{ $val->program->prgm_name ?? ''}} - {{ ($val->program->prgm_id)}}</div>
                            </div>

                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Program Limit </label>
                                <div class="label-bottom">{{number_format($val->prgm_limit_amt, 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Utilize Program Limit</label>
                                <div class="label-bottom">{{number_format($inv_limit, 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                                <label>Available Program Limit</label>
                                <div class="label-bottom">{{number_format(($val->prgm_limit_amt-$inv_limit), 2)}}</div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
                              
                                @if($limit->status==1 && $getAccountClosure > 0)  
                       
                                @can('add_adhoc_limit')
                                @if(($val->program->is_adhoc_facility ?? NULL) == 1 && !$isLimitExpired && !Helpers::checkActiveAdhocLimit($getAdhoc))
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
                            @if (!empty($adc->remark))
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                 <label>Remark</label>
                                 <div class="label-bottom">{{ $adc->remark }} </div>
                                </div>
                            </div>
                                @endif
                            </div>
                        @endforeach 

                        @endif
                        @endforeach 
                    </div>

                    @endforeach
                </div>
                @endif
                @endif

                @endforeach 
            </div>
        </div>
    @endforeach
    </div>
</div>

{!!Helpers::makeIframePopup('addAdhocLimit','Add Adhoc Limit', 'modal-lg')!!}
{!!Helpers::makeIframePopup('approveAdhocLimit','Confrim Approve Adhoc Limit', 'modal-xs')!!}
{!!Helpers::makeIframePopup('viewAdhcDocument','View Adhoc Document', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editReviewDate','Edit Review Date', 'modal-xs')!!}

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

    $(document).ready(function(){
        $('.collapse').on('show.bs.collapse', function(){
            $(this).prev().find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
        }).on('hide.bs.collapse', function(){
            $(this).prev().find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
        });
    });
    
</script>
@endsection
