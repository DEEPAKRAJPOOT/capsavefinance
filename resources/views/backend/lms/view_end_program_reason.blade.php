@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
    <div class="row">
        <div class="col-6">
            <label>Reason: </label>
        </div>
        <div class="col-6">
            {{ $reason }}
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <label>Comment:</label>            
        </div>    
        <div class="col-12">            
            {{ $comment }}        
        </div>
    </div>
</div>


@endsection

@section('jscript')
@endsection