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
                                    <label>Total Exposure</label>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:27px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control form-control-sm number_format" name="tot_limit_amt" value="{{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt): '' }}" maxlength="15" placeholder="Total Exposure">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label>Available Exposure</label>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:27px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control form-control-sm number_format" name="available_exposure" value="{{ isset($limitData->tot_limit_amt)? number_format($limitData->tot_limit_amt - $totOfferedLimit): '' }}" maxlength="15" placeholder="Available Exposure" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Product Type</label>
                                    <select class="form-control" name="product_id" id="product_id">
                                        <option value="">Select Product</option>
                                        <!-- <option value="1">Supply Chain</option> -->
                                        <option value="2">Term Loan</option>
                                        <option value="3">Leasing</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Anchor</label><span class="limit float-right"></span>
                                    <select class="form-control" name="anchor_id" id="anchor_id">
                                        <option value="">Select Anchor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Select Program</label><span class="limit float-right"></span>
                                    <select class="form-control" name="prgm_id" id="program_id">
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group INR">
                                    <label>Enter Limit</label><span class="limit float-right"></span>
                                    <a href="javascript:void(0);" class="verify-owner-no" style="top:30px;"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                    <input type="text" class="form-control number_format" name="limit_amt" id="limit_amt" maxlength="15" placeholder="Enter Limit">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-success btn-sm float-right" type="submit" name="program_submit">Submit</button>
                            </div>
                        </div>
                        </form>
                        <!-- To show suply chain data -->
                        @foreach($supplyPrgmLimitData as $key=>$prgmLimit)
                        @if($loop->first)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive ps ps--theme_default mt-2">
                                    <table id="supplier-listing" class="table table-striped cell-border  overview-table mb-0" cellspacing="0" width="100%">
                                        <thead>
                                            <tr role="row">
                                            <th width="17%">Sr. No.</th>
                                            <th width="17%">Product Type</th>
                                            <th width="17%">Anchor</th>
                                            <th width="17%">Program</th>
                                            <th width="16%">Limit</th>
                                            <th width="16%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#scollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="17%">{{($key+1)}}</td>
                                                       <td width="17%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="17%">{{$prgmLimit->anchor->comp_name}}</td>
                                                       <td width="17%">{{$prgmLimit->program->prgm_name}}</td>
                                                       <td width="16%">{{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="16%"><button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Edit Limit</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="scollapse{{$key+1}}" class="card-body bdr pt-2 pb-2 collapse">
                                            <ul class="row p-0 m-0">
                                            @if($prgmLimit->offer->count() != 0)
                                                @foreach($prgmLimit->offer as $k=>$prgmOffer)
                                                    <li class="col-md-2">Loan Offer <br> <i class="fa fa-inr"></i> <b>{{number_format($prgmOffer->offer->prgm_limit_amt)}}</b></li>
                                                    <li class="col-md-2">Interest(%)  <br> <b>{{$prgmOffer->offer->interest_rate}}</b></li>
                                                    <li class="col-md-2">Tenor(Days) <br> <b>{{$prgmOffer->offer->tenor}}</b></li>
                                                    <li class="col-md-2">Margin(%) <br> <b>{{$prgmOffer->offer->margin}}</b></li>
                                                    <li class="col-md-2">Processing Fee  <br><i class="fa fa-inr"></i><b>{{number_format($prgmOffer->offer->processing_fee)}}</b></li>
                                                    <li class="col-md-2"><button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Update</button></li>
                                                @endforeach
                                            @else
                                                <li class="col-md-10" style="text-align: center;">No offer found</li>
                                                <li class="col-md-2"><button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Add</button></li>
                                            @endif
                                            </ul>
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
                                            <th width="18%">Limit</th>
                                            <th width="18%">Cosumed Limit</th>
                                            <th width="18%">Balance Limit</th>
                                            <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#tcollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="5%">{{($key+1)}}</td>
                                                       <td width="16%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt - $prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="25%"><button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Edit Limit</button>
                                                       <button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Add Offer</button>
                                                       <a data-toggle="modal" data-target="#shareColenderFrame" data-url ="{{route('share_to_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 14px;">Share with Co-Lender</a>
                                                       <a data-toggle="modal" data-target="#viewSharedColenderFrame" data-url ="{{route('view_shared_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 14px;" title="View Shared Co-Lender"><i class="fa fa-eye"></i></a>
                                                       </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="tcollapse{{$key+1}}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                            <thead>
                                            <tr>
                                                <td width="10%" style="background: #e9ecef;"><b>Facility Type</b></td>
                                                <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Equipment Type</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Limit of the Equipment</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Tenor (Months)</b></td>
                                                <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>PTP Frequency</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>XIRR (%)</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Processing Fee (%)</b></td>
                                                <td width="5%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Action</b></td>
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
                                                <td><a class="btn btn-action-btn btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id, 'prgm_offer_id'=>$prgmOffer->prgm_offer_id])}}" title="Edit Offer"><i class="fa fa-edit"></i></a></td>
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
                                            <th width="18%">Limit</th>
                                            <th width="18%">Cosumed Limit</th>
                                            <th width="18%">Balance Limit</th>
                                            <th width="25%">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                @endif
                                <div class="accordion">
                                    <div class="card card-color mb-0">
                                        <div class="card-header pl-0 pr-0 collapsed" data-toggle="collapse" href="#lcollapse{{$key+1}}" aria-expanded="false">
                                            <table cellspacing="0" cellpadding="0" width="100%">
                                                <tbody>
                                                    <tr role="row" class="odd">
                                                       <td width="5%">{{($key+1)}}</td>
                                                       <td width="16%">{{$prgmLimit->product->product_name}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt - $prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->getTotalByPrgmLimitId())}}</td>
                                                       <td width="18%">&#8377; {{number_format($prgmLimit->limit_amt)}}</td>
                                                       <td width="25%"><button class="btn btn-success btn-sm edit-limit" data-url="{{route('show_limit', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Edit Limit</button>
                                                       <button class="btn btn-success btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}">Add Offer</button>
                                                       <a data-toggle="modal" data-target="#shareColenderFrame" data-url ="{{route('share_to_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 14px;">Share with Co-Lender</a>
                                                       <a data-toggle="modal" data-target="#viewSharedColenderFrame" data-url ="{{route('view_shared_colender', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-success btn-sm" style="font-size: 14px;" title="View Shared Co-Lender"><i class="fa fa-eye"></i></a>
                                                       </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="lcollapse{{$key+1}}" class="card-body bdr collapse" style="padding: 0; border: 1px solid #e9ecef;">
                                            <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                            <thead>
                                            <tr>
                                                <td width="10%" style="background: #e9ecef;"><b>Facility Type</b></td>
                                                <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Equipment Type</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Limit of the Equipment</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Tenor (Months)</b></td>
                                                <td width="20%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>PTP Frequency</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>XIRR (%)</b></td>
                                                <td width="10%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Processing Fee (%)</b></td>
                                                <td width="5%" style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Action</b></td>
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
                                                <td><a class="btn btn-action-btn btn-sm add-offer" data-url="{{route('show_limit_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'), 'app_prgm_limit_id'=>$prgmLimit->app_prgm_limit_id, 'prgm_offer_id'=>$prgmOffer->prgm_offer_id])}}" title="Edit Offer"><i class="fa fa-edit"></i></a></td>
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
                            <a data-toggle="modal" data-target="#editLimitFrame" data-url ="" data-height="400px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openLimitModal" style="display: none;"><i class="fa fa-plus"></i>Edit Limit</a>
                            @if((request()->get('view_only') || $currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
                                <form method="POST" action="{{route('approve_offer')}}">
                                @csrf
                                <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
                                <input name="btn_save_offer" class="btn btn-success btn-sm float-right mt-3 ml-3" type="submit" value="Approve Limit">
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
{!!Helpers::makeIframePopup('limitOfferFrame','Add Offer', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editLimitFrame','Edit Limit', 'modal-md')!!}

@endsection
@section('jscript')
<script>
var messages = {
    "get_anchors_by_product" : "{{route('ajax_get_anchors_by_product')}}",  
    "get_programs_by_anchor" : "{{route('ajax_get_programs_by_anchor')}}",  
    "get_program_balance_limit" : "{{route('ajax_get_program_balance_limit')}}",
    "token" : "{{ csrf_token() }}"  
};

var offers = {
    "program_limit":0,
    "program_min_limit":0,
    "program_max_limit":0,
    "program_balance_limit":0,
    "current_offer_limit":0,
    "total_offered_limit":{{$totOfferedLimit}},
}; 


$(document).ready(function(){
    $('#product_id').on('change',function(){
        let product_id = $('#product_id').val();
        $('span.limit').html('');
        if(product_id == ''){
            $('#program_id').attr('disabled', false);
            $('#anchor_id').attr('disabled', false);
            $('#program_id').html('<option value="">Select Program</option>');
            $('#anchor_id').html('<option value="">Select Anchor</option>');
            return;
        }else if(product_id != 1){
            $('#program_id').attr('disabled', true);
            $('#anchor_id').attr('disabled', true);
            $('#anchor_id').html('<option value="">Select Anchor</option>');
            $('#program_id').html('<option value="">Select Program</option>');
            return;
        }else{
            $('#program_id').attr('disabled', false);
            $('#anchor_id').attr('disabled', false);
        }
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_anchors_by_product,
            'type':"POST",
            'data':{"_token" : messages.token, "product_id" : product_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                fillAnchors(res);
                $('#program_id').html('<option value="">Select Program</option>');
                $('.isloader').hide();
            }
        })
    });

    $('#anchor_id').on('change',function(){
        let anchor_id = $('#anchor_id').val();
        let anchor_limit = $('#anchor_id option:selected').data('limit');
        setLimit('select[name=anchor_id]', '');
        setLimit('select[name=prgm_id]', '');
        setLimit('#limit_amt', '');

        if(anchor_id == ''){
            $('#program_id').html('<option value="">Select Program</option>');
            return;
        }else{
            setLimit('select[name=anchor_id]', 'Limit: <i class="fa fa-inr" aria-hidden="true"></i> '+anchor_limit);
        }
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_programs_by_anchor,
            'type':"POST",
            'data':{"_token" : messages.token, "anchor_id" : anchor_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                fillPrograms(res);
                $('.isloader').hide();
            }
        })
    });

    $('#program_id').on('change',function(){
        offers.program_limit = parseInt($('#program_id option:selected').data('sub_limit'));
        offers.program_min_limit = parseInt($('#program_id option:selected').data('min_limit'));
        offers.program_max_limit = parseInt($('#program_id option:selected').data('max_limit'));
        let program_id = $('#program_id').val();
        setLimit('select[name=prgm_id]', '');
        if(program_id == ''){
            unsetError('select[name=prgm_id]');
            return;
        }else{
            unsetError('select[name=prgm_id]');
            setLimit('select[name=prgm_id]', 'Limit: <i class="fa fa-inr" aria-hidden="true"></i> '+offers.program_limit);
        }
        let token = "{{ csrf_token() }}";
        $('.isloader').show();
        $.ajax({
            'url':messages.get_program_balance_limit,
            'type':"POST",
            'data':{"_token" : messages.token, "program_id" : program_id},
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success:function(res){
                res = JSON.parse(res);
                let consumed_limit = parseInt(res);
                offers.program_balance_limit = offers.program_limit - consumed_limit;
                setLimit('#limit_amt', 'Balance: <i class="fa fa-inr" aria-hidden="true"></i> '+offers.program_balance_limit);
                $('.isloader').hide();
            }
        })
    });


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

function checkValidation(){
    unsetError('select[name=product_id]');
    unsetError('select[name=anchor_id]');
    unsetError('select[name=prgm_id]');
    unsetError('input[name=limit_amt]');
    unsetError('input[name=tot_limit_amt]');

    let flag = true;
    let product_id = $('select[name=product_id]').val();
    let anchor_id = $('select[name=anchor_id]').val();
    let program_id = $('select[name=prgm_id]').val();
    let limit_amt = $('input[name=limit_amt]').val().trim();
    let tot_limit_amt = $('input[name=tot_limit_amt]').val().trim();
    if(tot_limit_amt.length == 0 || parseInt(tot_limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=tot_limit_amt]', 'Please fill total exposure limit');
        flag = false;
    }

    if(product_id == ''){
        setError('select[name=product_id]', 'Please select product type');
        flag = false;
    }

    if(anchor_id == '' && product_id == 1){
        setError('select[name=anchor_id]', 'Please select anchor');
        flag = false;
    }

    if(program_id == '' && product_id == 1){
        setError('select[name=prgm_id]', 'Please select program');
        flag = false;
    }

    if(limit_amt.length == 0 || parseInt(limit_amt.replace(/,/g, '')) == 0){
        setError('input[name=limit_amt]', 'Please fill limit amount');
        flag = false;
    }else if((parseInt(limit_amt.replace(/,/g, '')) > parseInt(tot_limit_amt.replace(/,/g, ''))) || (parseInt(limit_amt.replace(/,/g, '')) > (parseInt(tot_limit_amt.replace(/,/g, '')) - offers.total_offered_limit))){
        setError('input[name=limit_amt]', 'Limit amount can not exceed from Total Exposure');
        flag = false;
    }else{
        if(product_id == 1 && (parseInt(limit_amt.replace(/,/g, '')) > offers.program_balance_limit)){
            setError('input[name=limit_amt]', 'Limit amount can not exceed from program limit');
            flag = false;
        }else if(product_id == 1 && ((parseInt(limit_amt.replace(/,/g, '')) < offers.program_min_limit) || (parseInt(limit_amt.replace(/,/g, '')) > offers.program_max_limit))){
            setError('input[name=limit_amt]', 'Limit amount should be ('+offers.program_min_limit+' - '+offers.program_max_limit+') range');
            flag = false;
        }else{
            // TAKE REST
        }
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
