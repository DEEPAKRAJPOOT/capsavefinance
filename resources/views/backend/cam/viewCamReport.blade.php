@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
<style>
.overview-table >thead> tr > th {
    background: #138864 !important;
    color: #fff;
    border-left: 1px solid #199e75;
    vertical-align: top;
}
</style>
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
<div class="inner-container">
    <div class="card mt-3">
      <div class="card-body pt-3 pb-3">
        <p class="pull-left"><b>CAM Report For {{isset($arrBizData->biz_entity_name) ? $arrBizData->biz_entity_name : ''}}</b></p>
        @if(($currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
          <div class="float-right">
            @can('approve_limit_form')
           <!-- <a data-toggle="modal"  data-height="150px" 
            data-width="100%" data-target="#approveLimitFrame"
            data-url="{{route('approve_limit_form', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}"  
            data-placement="top" class="float-right mt-0 ml-3"><button type="button" class="btn btn-success btn-sm">Approve Limit</button></a>-->
            @endcan
          </div>
          @elseif(($approveStatus && $approveStatus->status == 1))
            <p class="float-right ml-3 mb-0"><b style="color: green; font-size: 17px;">Limit Approved</b></p>
          @endif
          @if(($currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
          <div class="float-right">
            @can('reject_offer_form')
            <a data-toggle="modal"  data-height="250px" 
            data-width="100%" data-target="#rejectOfferFrame"
            data-url="{{route('reject_offer_form', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}"  
            data-placement="top" class="float-right mt-0 ml-3"><button type="button" class="btn btn-success btn-sm">Reject Limit</button></a>
            @endcan
          </div>
          @elseif(($approveStatus && $approveStatus->status == 2))
            <p class="float-right ml-3 mb-0"><b style="color: green; font-size: 17px;">Limit Rejected</b></p>
          @endif
          @can('generate_cam_report')
          <a target="_blank" href="{{route('generate_cam_report', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}">
            <button type="button" class="btn btn-primary float-right btn-sm ml-3" > Download Report</button>
          </a>
          @endcan
        </div>
    </div>
    <div class="card mt-3" id="camReport">
      <div class="card-body pt-3 pb-3">
        <div class="row">
          <div class="col-md-12">
            @include('backend.cam.camReport')
          </div>
        </div>
      </div>
   </div>
 </div>
</div>
{!!Helpers::makeIframePopup('rejectOfferFrame','Reject Limit', 'modal-md')!!}
{!!Helpers::makeIframePopup('approveLimitFrame','Approve Limit', 'modal-md')!!}
@endsection
