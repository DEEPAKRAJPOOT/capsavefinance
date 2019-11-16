@extends('layouts.backend.admin-layout')

@section('content')

<div class="prtm-page-bar">
    <ul class="breadcrumb">
        <li class="breadcrumb-item text-capitalize">
            <h3>User Profile</h3> </li>
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;" class="active">User Profile</a></li>
    </ul>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="prtm-full-block text-center">
            <div class="pad-all-md">
                <div class="pos-relative">
                    <div class="contextual-link">
                        <div class="dropdown"> <a href="javascript:;" class="more-btn" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true">more<i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu dropdown-icon dropdown-menu-right">
                                <li>
                                    <a href="{{ route('update_profile') }}"> <i class="fa fa-pencil"></i> <span class="mrgn-l-sm">Edit </span> </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mrgn-b-lg"> 
                    @if(isset($userDetails->user_photo) &&  $userDetails->user_photo!="")
                        <img class="img-responsive img-circle display-ib" src="{{ asset('storage/'.$userDetails->user_photo) }}" width="140" height="140" alt="user profile image">
                    @else
                        <img class="img-responsive img-circle display-ib" src="{{ asset('backend/theme/assets/img/dami-user.png') }}" width="140" height="140" alt="user profile image">
                    @endif
                </div>
               <h5>{{ isset($userDetails->first_name) ? ucfirst($userDetails->first_name)." ".$userDetails->last_name : '' }}</h5>
                <p>{{ isset($userDetails->email) ? $userDetails->email : '' }}</p>
            </div>
        </div>
    </div>
</div>
         
@endsection
@section('pageTitle')
Dashboard
@endsection
