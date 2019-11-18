@extends('layouts.backend.admin-layout')

@section('content')




<link href="{{ asset('frontend/inside/css/site.css') }}" rel="stylesheet">
        <div class="form-section">
                <div class="row marB10">
                        <div class="col-md-12">
                                <h3 class="h3-headline">Manage Users</h3>
                        </div>
                </div>
        {!!
            Form::open(
            array('name' => 'ProCountryMaster',
            'autocomplete' => 'off', 
            'id' => 'manageUser',  

            )
            ) 
        !!}
                <div class="filter">
                        <div class="d-md-flex mt-3 mb-3">
                                <div class="filter-bg">
                                        <form class="form">
                                                <ul class="filter-sec">
                                                        <li>
                                                                                                                                   {!!
            Form::text('by_email',
            null,
            [
            'class' => 'form-control',
            'placeholder' => 'Search by First name, Last name and Email',
            'id'=>'by_name'
            ])
            !!}
                                                        </li>
                                                        <li>
                                                                <select class="form-control" name="uname" required="">
                                                                        <option>Select Status</option>
                                                                        <option>Approved</option>
                                                                        <option>Pending </option>
                                                                        <option>Disapproved </option>
<!--                                                                        <option>Locked </option>-->
                                                                </select>
                                                        </li>
                                                        <li>

                                                                <button type="button" value="search" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
                                                        </li>
                                                </ul>
                                        </form>
                                </div>
                                <div class="ml-md-auto">
                                        <button type="button" class="btn btn-default btn-sm">Clear Filters</button>
                                </div>
                        </div>
                </div>
            <div id="table_data">
                @include('backend.pagination_data') 
            </div>
        </div>


         
@endsection
@section('pageTitle')
User list
@endsection
@section('jscript')
<script>
$(document).ready(function(){

 $(document).on('click', '.pagination a', function(event){
  event.preventDefault(); 
  var page = $(this).attr('href').split('page=')[1];
  fetch_data(page);
 });

 function fetch_data(page)
 {
  $.ajax({
   url:"/dashboard/user_paginate?page="+page,
   success:function(data)
   {
    $('#table_data').html(data);
   }
  });
 }
 
});
</script>
<script src="{{ asset('frontend/inside/js/popper.min.js') }}"></script>
<script src="{{ asset('frontend/inside/js/bootstrap.min.js') }}"></script>
@endsection
