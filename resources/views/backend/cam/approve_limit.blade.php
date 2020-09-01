@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="rejectOfferForm" name="rejectOfferForm" method="POST" action="{{route('approve_offer',['app_id'=>$app_id, 'biz_id'=>$biz_id])}}" target="_top">
        @csrf
        <div class="row">
            <div class="form-group col-12">
                Do you want to approve limit?
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm pull-right" name="submit" id="yes" value="Yes" />
            </div>
        </div>
    </form>
</div>
@endsection
@section('jscript')
@php 
$operation_status = session()->get('operation_status', false);
$messages = session()->get('message', false);
@endphp
@if($operation_status == config('common.YES'))
<script>
try {
    var p = window.parent;
    p.jQuery('#iframeMessage').html('{!! Helpers::createAlertHTML($messages, 'success') !!}');
    p.jQuery('#add_bank_account').modal('hide');
    // p.reloadDataTable();
    p.location.reload();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif
@endsection