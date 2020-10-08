@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'bank'])

<div class="content-wrapper">
    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
             <div class="card">
                <div class="card">
                    @include('lms.nach.nach_preview_common')
                    <div>
                        <a class="btn btn-success btn-sm" href="{{route('generate_nach', ['users_nach_id' => $nachDetail['users_nach_id']])}}">Download Nach</a>
                        <a data-toggle="modal" data-target="#uploadNachDocument" data-url ="{{route('upload_nach_document', ['user_id' => $nachDetail['user_id'], 'users_nach_id' => $nachDetail['users_nach_id']]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ">Upload Nach Document</a>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('uploadNachDocument','Upload Nach Document', 'modal-md')!!}
@endsection