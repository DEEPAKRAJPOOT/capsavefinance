@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <div class="card mt-4">
            <div class="card-body">
                <div class="data">
                    <h2 class="sub-title bg mb-4">Limit By Capsave</h2>
                    <div class="pl-4 pr-4 pb-4 pt-2">
                    <form method="POST" action="{{route('save_limit_assessment')}}" onsubmit="return checkValidation()">
                        @csrf
                        <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                        <input type="hidden" name="biz_id" value="{{request()->get('biz_id')}}">
                        {{-- <input type="hidden" name="app_limit_id" value="{{$limitData->app_limit_id}}"> --}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label>Total Credit Assessed</label>
                                    <div class="relative">
                                    <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control number_format" name="tot_limit_amt" value="{{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt): '' }}" maxlength="15" placeholder="Total Exposure" {{isset($limitData->tot_limit_amt)? 'disabled': ''}}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label>Available Credit Assessed</label>
                                    <div class="relative">
                                    <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control number_format" name="available_exposure" value="{{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt - $totOfferedLimit): '' }}" maxlength="15" placeholder="Available Exposure (offered)" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!--<div class="col-md-3">
                                <div class="form-group">
                                </div>
                            </div>-->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="mb-21">Select Product Type</label>
                                    <select class="form-control" name="product_id" id="product_id">
                                        <option value="">Select Product</option>
                                        @foreach($product_types as $key => $product_type)
                                        <option value="{{$key}}" {{(old('product_id') == $key)?'selected': ''}}>{{$product_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @php
                            $balance = isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt - $prgmLimitTotal): '';
                            @endphp

                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label class="mb-0">Proposed Product Limit</label><span class="limit float-right"></span>
                                    <div class="text-success">
                                    @if($balance != '')
                                        <small>Remaining Limit Balance: <i class="fa fa-inr" aria-hidden="true"></i> {{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt - $prgmLimitTotal): '' }}</small>
                                    @endif
                                    </div>
				                    <div class="relative">	
                                    <a href="javascript:void(0);" class="remaining"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control number_format" name="limit_amt" id="limit_amt" value="{{old('limit_amt')}}" maxlength="15" placeholder="Enter Proposed Product Limit">
				                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
				    @if (request()->get('view_only'))
                    @can('save_limit_assessment')
                                    <button class="btn btn-success btn-sm mt-44" type="submit" name="program_submit">Submit</button>
                                    @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        </form>

                        <!-- To show suply chain data -->
                        <!-- @php
                            $offer_no = 1;
                        @endphp -->
                        @foreach($supplyPrgmLimitData as $key=>$prgmLimit)
                        @if($loop->first)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive ps ps--theme_default mt-2">
                                    <table id="supplier-listing" class="table table-striped cell-border  overview-table mb-0" cellspacing="0" width="100%">
                                        <thead>
                                            <tr role="row">
                                            <th width="6%">Sr. No.</th>
                                            <th width="15%">Product Type</th>
                                            <th width="15%">Product Limit</th>
                                            <th width="20%">Consumed Product Limit</th>
                                            <th width="20%">Remaining Product Limit</th>
                                            <th width="24%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#scollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%" class="pdl-15">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="6%">{{($key+1)}}</td>
                                                       <td width="15%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="15%">&#8377; {{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="20%">&#8377; {{number_format($prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="20%">&#8377; {{number_format($prgmLimit->limit_amt - $prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="24%">
                                                       @can('show_limit')
                                                       <button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}"  title="Edit Limit"><i class="fa fa-edit"></i></button>
                                                       @endcan
                                                       @can('show_limit_offer')
                                                       <button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" title="Add Offer"><i class="fa fa-plus"></i></button>
                                                       @endcan
                                                       @if($offerStatus == 2)
                                                       @can('share_to_colender')
                                                       <a data-toggle="modal" data-target="#shareColenderFrame" data-url ="{{route('share_to_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 13px;" title="Share with Co-Lender"><i class="fa fa-share"></i></a>
                                                       @endcan
                                                       @can('view_shared_colender')
                                                       <a data-toggle="modal" data-target="#viewSharedColenderFrame" data-url ="{{route('view_shared_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 13px;" title="View Shared Co-Lender"><i class="fa fa-eye"></i></a>
                                                       @endcan
                                                       @endif
                                                       </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="scollapse{{$key+1}}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                            <thead>
                                            <tr class="sub-heading">
                                                <td width="20%" >Overdue Interest Rate (%)</td>
                                                <td width="10%" >Interest Rate (%)</td>
                                                <td width="10%" >Program Limit</td>
                                                <td width="10%" >Tenor (In Days)</td>
                                                <td width="10%" >Payment Frequency</td>
                                                <td width="10%" >Margin (%)</td>
                                                <td width="18%" >Grace Period (In Days)</td>
                                                <td width="12%" >Adhoc Interest Rate (%)</td>
                                                <td width="5%" >Action</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($prgmLimit->offer->count() != 0)
                                            @foreach($prgmLimit->offer as $k=>$prgmOffer)					    
					    @if ( ($userRole && $userRole->id == 11 && \Auth::user()->anchor_id == $prgmOffer->anchor_id) || (!$userRole || ($userRole && $userRole->id != 11)) )
                                            </tr>
                                                <td>{{($prgmOffer->overdue_interest_rate ?? 0) + ($prgmOffer->interest_rate ?? 0)}}%</td>
                                                <td>{{$prgmOffer->interest_rate}}%</td>
                                                <td>&#8377; {{number_format($prgmOffer->prgm_limit_amt)}}</td>
                                                <td>{{$prgmOffer->tenor}}</td>
                                                <td>{{ config('common.payment_frequency.'.$prgmOffer->payment_frequency)}}</td>
                                                <td>{{$prgmOffer->margin}}%</td>
                                                <td>{{$prgmOffer->grace_period}}</td>
                                                <td>{{$prgmOffer->adhoc_interest_rate}}%</td>
                                                <td>
                                                    @if($prgmOffer->status == 2)
                                                    <label class="badge badge-danger">Rejected</label>
                                                    @else
                                                    @can('show_limit_offer')
                                                    <a class="btn btn-action-btn btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id, 'prgm_offer_id'=>$prgmOffer->prgm_offer_id])}}" title="Edit Offer"><i class="fa fa-edit"></i></a>
                                                    @endcan
                                                    @endif
                                                </td>
                                            </tr>
					    @endif
                                            @endforeach
                                            @else
                                                <tr style="text-align: center;">
                                                    <td>No offer found</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @if($loop->last)
                            </div>
                        </div>
                        @endif
                        @endforeach

                        <!-- To show term loan data -->
                        @foreach($termPrgmLimitData as $key=>$prgmLimit)
                        @if($loop->first)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive ps ps--theme_default mt-2">
                                    <table id="supplier-listing" class="table table-striped cell-border overview-table mb-0" cellspacing="0" width="100%">
                                        <thead>
                                            <tr role="row">
                                            <th width="5%">Sr. No.</th>
                                            <th width="16%">Product Type</th>
                                            <th width="18%">Product Limit</th>
                                            <th width="18%">Consumed Product Limit</th>
                                            <th width="18%">Remaining Product Limit</th>
                                            <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#tcollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%" class="pdl-15">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="5%">{{($key+1)}}</td>
                                                       <td width="16%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt - $prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="25%">
                                                       @can('show_limit')
                                                       <button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" title="Edit Limit"><i class="fa fa-edit"></i></button>
                                                       @endcan
                                                       @can('show_limit_offer')
                                                       <button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" title="Add Offer"><i class="fa fa-plus"></i></button>
                                                       @endcan
                                                       </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="tcollapse{{$key+1}}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                            <thead>
                                            <tr class="sub-heading">
                                                <td width="10%" >Facility Type</td>
                                                <td width="20%" >Equipment Type</td>
                                                <td width="10%" >Limit of the Equipment</td>
                                                <td width="10%" >Tenor (Months)</td>
                                                <td width="20%" >PTP Frequency</td>
                                                <td width="10%" >XIRR (%)</td>
                                                <td width="10%" >Processing Fee (%)</td>
                                                <td width="5%" >Action</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($prgmLimit->offer->count() != 0)
                                            @foreach($prgmLimit->offer as $k=>$prgmOffer)
                                            </tr>
                                                <td>{{($prgmOffer->facility_type_id !='')? config('common.facility_type')[$prgmOffer->facility_type_id]: 'NA'}}</td>
                                                <td>{{\Helpers::getEquipmentTypeById($prgmOffer->equipment_type_id)->equipment_name}}</td>
                                                <td>&#8377; {{number_format($prgmOffer->prgm_limit_amt)}}</td>
                                                <td>{{$prgmOffer->tenor}}</td>
                                                <td>
                                                    @php 
                                                        $i = 1;
                                                        $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
                                                        if(!empty($prgmOffer->offerPTPQ)){
                                                            $total = count($prgmOffer->offerPTPQ);
                                                    @endphp   
                                                    @foreach($prgmOffer->offerPTPQ as $key => $arr) 
                                                        @if($i > 1 && $i < $total)
                                                              ,
                                                        @elseif ($i > 1 && $i == $total)
                                                            and
                                                        @endif
                                                            &#8377; {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$prgmOffer->rental_frequency]}}
                                                        @php 
                                                        $i++;
                                                        @endphp     
                                                    @endforeach
                                                    @php 
                                                        }
                                                    @endphp
                                                </td>
                                                <td><b>Ruby Sheet</b>: {{$prgmOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$prgmOffer->cash_flow_xirr}}%</td>
                                                <td>{{$prgmOffer->processing_fee}}%</td>
                                                <td>
                                                    @if($prgmOffer->status == 2)
                                                    <label class="badge badge-success">Rejected</label>
                                                    @else
                                                    @can('show_limit_offer')
                                                    <a class="btn btn-action-btn btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id, 'prgm_offer_id'=>$prgmOffer->prgm_offer_id])}}" title="Edit Offer"><i class="fa fa-edit"></i></a>
                                                    @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                                <tr style="text-align: center;">
                                                    <td>No offer found</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @if($loop->last)
                            </div>
                        </div>
                        @endif
                        @endforeach

                        <!-- To show leasing data -->
                        @foreach($leasingPrgmLimitData as $key=>$prgmLimit)
                        @if($loop->first)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive ps ps--theme_default mt-2">
                                    <table id="supplier-listing" class="table table-striped cell-border overview-table mb-0" cellspacing="0" width="100%">
                                        <thead>
                                            <tr role="row">
                                            <th width="5%">Sr. No.</th>
                                            <th width="16%">Product Type</th>
                                            <th width="18%">Product Limit</th>
                                            <th width="18%">Cosumed Product Limit</th>
                                            <th width="18%">Remaining Product Limit</th>
                                            <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#lcollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%" class="pdl-15">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="5%">{{($key+1)}}</td>
                                                       <td width="16%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt - $prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="25%">
                                                       @can('show_limit')
                                                       <button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" title="Edit Limit"><i class="fa fa-edit"></i></button>
                                                       @endcan
                                                       @can('show_limit_offer')
                                                       <button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" title="Add Offer"><i class="fa fa-plus"></i></button>
                                                       @endcan
                                                       </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="lcollapse{{$key+1}}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                            <thead>
                                            <tr class="sub-heading">
                                                <td width="10%" >Facility Type</td>
                                                <td width="20%" >Equipment Type</td>
                                                <td width="10%" >Limit of the Equipment</td>
                                                <td width="10%" >Tenor (Months)</td>
                                                <td width="20%" >PTP Frequency</td>
                                                <td width="10%" >XIRR/Discounting (%)</td>
                                                <td width="10%" >Processing Fee (%)</td>
                                                <td width="5%" >Action</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($prgmLimit->offer->count() != 0)
                                            @foreach($prgmLimit->offer as $k=>$prgmOffer)
                                            </tr>
                                                <td>{{($prgmOffer->facility_type_id !='')? config('common.facility_type')[$prgmOffer->facility_type_id]: 'NA'}}</td>
                                                <td>{{\Helpers::getEquipmentTypeById($prgmOffer->equipment_type_id)->equipment_name}}</td>
                                                <td>&#8377; {{number_format($prgmOffer->prgm_limit_amt)}}</td>
                                                <td>{{$prgmOffer->tenor}}</td>
                                                <td>
                                                    @if($prgmOffer->facility_type_id == 3)
                                                    NILL
                                                    @else
                                                    @php 
                                                        $i = 1;
                                                        $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
                                                        if(!empty($prgmOffer->offerPTPQ)){
                                                            $total = count($prgmOffer->offerPTPQ);
                                                    @endphp   
                                                    @foreach($prgmOffer->offerPTPQ as $key => $arr) 
                                                        @if($i > 1 && $i < $total)
                                                              ,
                                                        @elseif ($i > 1 && $i == $total)
                                                            and
                                                        @endif
                                                            &#8377; {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$prgmOffer->rental_frequency]}}
                                                        @php 
                                                        $i++;
                                                        @endphp     
                                                    @endforeach
                                                    @php 
                                                        }
                                                    @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                @if($prgmOffer->facility_type_id == 3)
                                                <b>Rental Discounting</b>: {{$prgmOffer->discounting}}%
                                                @else
                                                <b>Ruby Sheet</b>: {{$prgmOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$prgmOffer->cash_flow_xirr}}%
                                                @endif
                                                </td>
                                                <td>{{$prgmOffer->processing_fee}}%</td>
                                                <td>
                                                    @if($prgmOffer->status == 2)
                                                    <label class="badge badge-success">Rejected</label>
                                                    @else
                                                    @can('show_limit_offer')
                                                    <a class="btn btn-action-btn btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id, 'prgm_offer_id'=>$prgmOffer->prgm_offer_id])}}" title="Edit Offer"><i class="fa fa-edit"></i></a>
                                                    @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                                <tr style="text-align: center;">
                                                    <td>No offer found</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @if($loop->last)
                            </div>
                        </div>
                        @endif
                        @endforeach
                        <div>
                            <a data-toggle="modal" data-target="#limitOfferFrame" data-url ="" data-height="600px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openOfferModal" style="display: none;"><i class="fa fa-plus"></i>Add Offer</a>
                            <a data-toggle="modal" data-target="#editLimitFrame" data-url ="" data-height="250px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openLimitModal" style="display: none;"><i class="fa fa-plus"></i>Edit Limit</a>
                            @if((request()->get('view_only') || $currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
                                <form method="POST" action="{{route('approve_offer')}}">
                                @csrf
                                <input type="hidden" name="app_id" value="{{request()->get('app_id')}}"><input type="hidden" name="user_id" value="{{$limitData->user_id}}">
                                @can('approve_offer')
                                <input name="btn_save_offer" class="btn btn-success btn-sm float-right mt-3 ml-3" type="submit" value="Approve Limit">
                                @endcan
                                </form>
                            @endif
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

{!!Helpers::makeIframePopup('shareColenderFrame','Share with Co-Lender', 'modal-md')!!}
{!!Helpers::makeIframePopup('viewSharedColenderFrame','View shared Co-Lender', 'modal-lg')!!}
{!!Helpers::makeIframePopup('limitOfferFrame','Add/Update Offer', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editLimitFrame','Update Limit', 'modal-md')!!}

@endsection
@section('jscript')
<script>

$(document).ready(function(){
    $('.add-offer').on('click', function(){
        let data_url = $(this).data('url');
        $('#openOfferModal').attr('data-url', data_url);
        $('#openOfferModal').trigger('click');
    });

    $('.edit-limit').on('click', function(){
        let data_url = $(this).data('url');
        $('#openLimitModal').attr('data-url', data_url);
        $('#openLimitModal').trigger('click');
    });
});

$(document).ready(function(){
    $('.card-header').eq(0).removeClass('collapsed');
    $('.card-body').eq(1).addClass('show');
})

function checkValidation(){
    unsetError('select[name=product_id]');
    unsetError('input[name=limit_amt]');
    unsetError('input[name=tot_limit_amt]');

    let flag = true;
    let product_id = $('select[name=product_id]').val();
    let limit_amt = $('input[name=limit_amt]').val().trim();
    let tot_limit_amt = $('input[name=tot_limit_amt]').val().trim();
    let prgmLimitTotal = "{{$prgmLimitTotal}}";
    if(tot_limit_amt.length == 0 || parseInt(tot_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=tot_limit_amt]', 'Please fill total Credit Assessed');
        flag = false;
    }

    if(product_id == ''){
        setError('select[name=product_id]', 'Please select Product Type');
        flag = false;
    }

    if(limit_amt.length == 0 || parseInt(limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=limit_amt]', 'Please fill Product Limit');
        flag = false;
    }else if(parseInt(limit_amt.replace(/,/g, '')) > parseInt(tot_limit_amt.replace(/,/g, ''))){
        setError('input[name=limit_amt]', 'Product Limit amount can not exceed from Total Credit Assessed');
        flag = false;
    }else if(parseInt(limit_amt.replace(/,/g, '')) > (parseInt(tot_limit_amt.replace(/,/g, '')) - prgmLimitTotal)){
        setError('input[name=limit_amt]', 'Product Limit amount can not exceed from Total Credit Assessed');
        flag = false;
    }else{
        // TAKE REST
    }

    if(flag){
        return true;
    }else{
        return false;
    }
}

function fillAnchors(programs){
    let html = '<option value="" data-limit="0">Select Anchor</option>';
    $.each(programs, function(i,program){
        if(program.anchors != null)
            html += '<option value="'+program.anchors.anchor_id+'" data-limit="'+program.anchors.prgm_data.anchor_limit+'">'+program.anchors.comp_name+'</option>';
    });
    $('#anchor_id').html(html);
    
}

function fillPrograms(programs){
    let html = '<option value="" data-sub_limit="0" data-min_limit="0" data-max_limit="0">Select Program</option>';
    $.each(programs, function(i,program){
        if(program.prgm_name != null)
            html += '<option value="'+program.prgm_id+'" data-sub_limit="'+program.anchor_sub_limit+'" data-min_limit="'+program.min_loan_size+'" data-max_limit="'+program.max_loan_size+'">'+program.prgm_name+'</option>';
    });
    $('#program_id').html(html);
}

</script>

@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES'))  
  <script>
      try {
          var p = window.parent;       
          p.jQuery('#shareColenderForm').modal('hide');
          window.parent.location.reload();
      } catch (e) {
          if (typeof console !== 'undefined') {
              console.log(e);
          }
      }
  </script>
@endif
@endsection
