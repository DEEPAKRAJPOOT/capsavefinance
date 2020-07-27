@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Agency</h3>
            <small>Agency List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Agency</li>
                <li class="active">Agency List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                            @can('add_agency_reg')
                            <a data-toggle="modal" data-target="#addAgencyFrame" data-url ="{{route('add_agency_reg')}}" data-height="400px" data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Agency
                                </button>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>     
            </div>

            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by Agency Name, Email Id" id="by_name" name="search_keyword" type="text">
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="agencyList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Agency ID</th>
                                    <th>Agency Name</th>
                                    <th>Address</th>
                                    <th>Email ID</th>
                                    <th>Mobile</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addAgencyFrame','Add Agency', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editAgencyFrame','Edit Agency Detail', 'modal-lg')!!}
@endsection

@section('jscript')

@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES'))  
  <script>
      try {
          var p = window.parent;       
          p.jQuery('#editAgencyFrame').modal('hide');
          p.jQuery('#addAgencyFrame').modal('hide');
          window.parent.location.reload();
      } catch (e) {
          if (typeof console !== 'undefined') {
              console.log(e);
          }
      }
  </script>
@endif

<script>
var messages = {
    get_agency_list: "{{ URL::route('get_ajax_agency_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/agency.js') }}"></script>
@endsection