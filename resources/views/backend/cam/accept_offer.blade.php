@extends('layouts.backend.admin_popup_layout')
@section('content')

<div class="modal-body text-left">
    <form id="acceptOfferForm" name="acceptOfferForm" method="POST" action="{{route('accept_offer',['app_id'=>$app_id, 'biz_id'=>$biz_id])}}" target="_top">
        @csrf
        <div class="row">
            <div class="form-group col-12">
                @if(request()->get('btn_type') == 'reject')
                <label for="comment_txt">Comment<span class="mandatory">*</span></label>
                <textarea type="text" id="comment_txt" name="comment_txt" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                @elseif(request()->get('btn_type') == 'accept')
                Do you want to accept these offers?
                @endif      
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12 mb-0">
                @if(request()->get('btn_type') == 'reject')
                <input type="submit" class="btn btn-secondary btn-sm pull-right" name="btn_reject_offer" id="yes" value="Submit" />
                @elseif(request()->get('btn_type') == 'accept')
                <input type="submit" class="btn btn-success btn-sm pull-right" name="btn_accept_offer" id="yes" value="Yes" />
                @endif
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
        p.jQuery('#acceptOfferFrame').modal('hide');
        p.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
    </script>
@endif

@if(request()->get('btn_type') == 'reject')
    <script>
        $("#acceptOfferForm").validate({
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
@endif

@endsection