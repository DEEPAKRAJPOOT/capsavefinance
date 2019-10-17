@extends('layouts.admin')

@section('content')
<div class="prtm-content">
    <div class="prtm-page-bar">
        <ul class="breadcrumb">
            <li class="breadcrumb-item text-capitalize">
                   <h4>Rights Detail</h4>
            </li>
        </ul>
    </div>

    <div class="form-validation-style">
        <div class="prtm-block">
            <div class="prtm-block-content">
               <div class="container">

                <div class="row">
                  <div class="col-sm-4">
                    <h4>User Name</h4>
                    <p> {{$right[0]->first_name}} {{$right[0]->last_name}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Title</h4>
                   <p>{{$right[0]->title}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Type Title</h4>
                    <p>{{$right[0]->mrt}}</p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Number</h4>
                    <p> {{$right[0]->number}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Inventor</h4>
                   <p>{{$right[0]->inventor}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Assignee</h4>
                    <p>{{$right[0]->assignee}}</p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Cluster Title</h4>
                    <p> {{$right[0]->mct}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Rights For id</h4>
                   <p>{{$right[0]->mrft}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Url's</h4>
                    <p>{{$right[0]->url}}</p>
                  </div>
                </div>
                 <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Description</h4>
                    <p> {{$right[0]->description}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Keywords</h4>
                   <p>{{$right[0]->keywords}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Is Trading</h4>
                    <p>{{($right[0]->is_trading == 1)?'Yes':'No'}}
                    </p>
                  </div>
                </div>
                <br>

                <div class="row">
                  <div class="col-sm-4">
                    <h4>Trading Type</h4>
                    <p> {{($right[0]->trading_type == 1)?'Exclusive Purchage':'Non Exclusive Purchage'}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Phase</h4>
                   <p>{{($right[0]->phase == 1)?'Completed':'Draft'}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Is Featured</h4>
                    <p>{{($right[0]->is_featured==1)?'Featured':'Normal'}}</p>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-sm-4">
                    <h4>Date</h4>
                    <p> {{($right[0]->date)?$right[0]->date:''}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Expiry Date</h4>
                   <p>{{($right[0]->expiry_date)?$right[0]->expiry_date:''}}</p>
                  </div>
                  <div class="col-sm-4">
                    <h4>Thumbnail</h4>
                    <p>{{($right[0]->thumbnail)?$right[0]->thumbnail:''}}</p>
                  </div>
                </div>

              </div> 
            </div>
        </div>
    </div>
    @endsection
    @section('pageTitle')
        View Rights
    @endsection
    @section('jscript')
    <script>
        var messages = {
            title: "{{trans('master.cluster.title')}}",
            is_active: "{{trans('master.country.is_active')}}"
        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/master/source.js') }}" ></script>

    @endsection
