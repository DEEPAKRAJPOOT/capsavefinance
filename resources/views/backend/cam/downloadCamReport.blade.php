@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">

        <div class="card mt-3">
            <div class="card-body pt-3 pb-3">
                
                <button onclick="downloadCam(49)" class="btn btn-primary float-right btn-sm "> Download</button>
                <div class="row">
                	{!! $arrCamData !!}
                </div>

            </div>
        </div>

        
    </div>
</div>
@endsection
