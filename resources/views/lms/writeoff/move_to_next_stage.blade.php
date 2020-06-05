@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="woApprDissapprForm" name="woApprDissapprForm" method="POST" action="{{route('wo_save_appr_dissappr',['wo_req_id'=>$wo_req_id,'customer_id'=>$user_id,'action_type'=>$action_type,'status_id'=>$status_id])}}" target="_top">
        @csrf
        <div class="row">
            <div class="form-group col-12">
                <label for="comment_txt">Comment
                <span class="mandatory">*</span>
                </label>
                <textarea type="text" id="comment_txt" name="comment_txt" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>       
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                <input type="submit" class="btn btn-success btn-sm pull-right" name="submit" id="yes" value="Submit" />
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
<script>
    $("#woApprDissapprForm").validate({
            rules: {
                comment_txt: {
                    required: true
                }
            },
            messages: {
                comment_txt:{
                    required:'Please enter comment.'
                }
            }
        });
</script>
@endsection