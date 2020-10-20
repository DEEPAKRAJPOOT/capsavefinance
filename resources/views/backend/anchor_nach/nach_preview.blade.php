@extends('layouts.backend.admin-layout')
@section('content')

<div class="content-wrapper">
    @include('frontend.nach.common.section')
    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
             <div class="card">
                <div class="card">
                    <div class="row col-md-12 mt-2">
                        <a class="btn btn-success btn-sm ml-2" href="{{route('anchor_generate_nach', ['users_nach_id' => $nachDetail['users_nach_id']])}}">Download Nach</a>
                        <a data-toggle="modal" data-target="#uploadNachDocument" data-url ="{{route('anchor_upload_nach_document', ['user_id' => $nachDetail['user_id'], 'users_nach_id' => $nachDetail['users_nach_id']]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2">Upload Nach Document</a>
                    </div>
                    @include('frontend.nach.common.preview')
                </div>
             </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('uploadNachDocument','Upload Nach Document', 'modal-md')!!}
@endsection