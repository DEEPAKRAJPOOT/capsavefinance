@extends('layouts.withought_login')
@section('content')
<section>
    <div class="banner-section">
        <div class="search-box">
            <!--Grid column-->
            @include('layouts.global_search')
            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    <a href="{{route('add_right')}}">
                        <img src="{{ asset('frontend/inside/images/add-right.png') }}" alt="add-right" class="img-fluid">
                        <span>Add Rights</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<section style="padding:2px;">
    <div class="container ">
         <div class="card-header offset-md-2 col-md-10 pt-0">
            <p ><span id="keySearch"></span><span id="sortSearch"></span> <span id="typeSearch"></span> <span id="groupSearch"></span></p>                  
                        </div>
        <div class="d-flex">
            @include('layouts.filter')
            <div class=" col-md-9">
                <div class="card box-shadow-div view-all-rights-sec mb-5">
                    <div class="card-body p-0   overflow-v hover-effect">
                       
                        <div class="col-md-12">
                            <div class="row filter_data"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('pageTitle')
All Rights
@endsection
@section('additional_css')

@endsection
@section('jscript') 

<script type="text/javascript" src="{{ asset('frontend/inside/js/custom.js') }}"></script>
@endsection
