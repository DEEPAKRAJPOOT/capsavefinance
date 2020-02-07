@extends('layouts.backend.admin_popup_layout')

@section('content')

<style>
.overview-table >thead> tr > th {
    background: #138864 !important;
    color: #fff;
    border-left: 1px solid #199e75;
    vertical-align: top;
}
</style>
<div class="content-wrapper">
<div class="inner-container">
    @include('backend.cam.camReport')
 </div>
</div>
@endsection
