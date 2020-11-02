@extends('layouts.app')
@section('content')

<div class="content-wrapper">
    @include('frontend.nach.common.section')
    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
             <div class="card">
                <div class="card">
                    <div class="row col-md-12 mt-2">
                        <a class="btn btn-success btn-sm ml-2" href="{{route('front_generate_nach', ['users_nach_id' => $nachDetail['users_nach_id']])}}">Download NACH</a>
                        <a data-toggle="modal" data-target="#uploadNachDocument" data-url ="{{route('front_upload_nach_document', ['user_id' => $nachDetail['user_id'], 'users_nach_id' => $nachDetail['users_nach_id']]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2">Upload NACH Document</a>
                    </div>
                    @include('frontend.nach.common.preview')
                </div>
             </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('uploadNachDocument','Upload NACH Document ', 'modal-md')!!}
@endsection