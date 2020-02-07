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
          @if(($currStageCode == 'approver') && ($approveStatus && $approveStatus->status == 0))
          <div class="float-right">
            <form method="POST" action="{{route('approve_offer')}}">
            @csrf
            <input type="hidden" name="app_id" value="{{request()->get('app_id')}}">
            <input name="btn_save_offer" class="btn btn-success btn-sm float-right mt-3 ml-3" type="submit" value="Approve Limit">
            </form>
          </div>
          @elseif(($approveStatus && $approveStatus->status == 1))
            <p class="float-right ml-3 mb-0"><b style="color: green; font-size: 17px;">Limit Approved</b></p>
          @endif
          
            <a href="{{route('generate_cam_report', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}">
                 <button type="button" class="btn btn-primary float-right btn-sm ml-3" > Generate CAM Report</button>
              </a>
            <a target="_blank" href="{{route('download_cam_report', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}">
               <button type="button" class="btn btn-primary float-right btn-sm ml-3" > Download Report</button>
            </a>
            {{-- <button onclick="downloadCam()" class="btn btn-primary float-right btn-sm  " > Download Report</button> --}}
        </div>
    </div>
    @include('backend.cam.camReport')
 </div>
</div>
@endsection
