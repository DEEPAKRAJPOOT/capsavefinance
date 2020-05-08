@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="modal-body text-left">
    <span>Are you sure you want to approve the Adhoc limit? </span>
    <div class="row">
        <div class="col-6">
            <form method="POST" action="{{ Route('save_approve_adhoc_limit') }}" enctype="multipart/form-data" target="_top">
                @csrf
                <input type="hidden" name="user_id" value="{{ request()->get('user_id') }}">
                <input type="hidden" name="app_offer_adhoc_limit_id" value="{{ request()->get('app_offer_adhoc_limit_id') }}">
                <input type="hidden" name="status" value="2">
                <button type="submit" class="btn btn-danger float-left btn-sm" >Reject</button>  
            </form>
        </div> 
        <div class="col-6">
            <form method="POST" action="{{ Route('save_approve_adhoc_limit') }}" enctype="multipart/form-data" target="_top">
                @csrf
                <input type="hidden" name="user_id" value="{{ request()->get('user_id') }}">
                <input type="hidden" name="app_offer_adhoc_limit_id" value="{{ request()->get('app_offer_adhoc_limit_id') }}">
                <input type="hidden" name="status" value="1">

                <button type="submit" class="btn btn-success float-right btn-sm" >Approve</button>  
            </form>
        </div> 
    </div>
</div>
 
@endsection
