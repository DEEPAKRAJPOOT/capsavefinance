@extends('layouts.app')
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
<section class="mt-4">
    <div class="container ">
        <div class="offset-md-2 col-md-10 card-header  pb-3 pt-0">
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


@section('iframe')
 <div class="modal" id="rightpurchase">
        <div class="modal-dialog modal-lg ">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Buy Right</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" style="padding: 0.5rem;">
              <iframe id ="frameset"  frameborder="0"></iframe>
            </div>
          </div>
        </div>
    </div>

@endsection



@section('pageTitle')
All Rights
@endsection
@section('additional_css')

@endsection
@section('jscript') 
@endsection
