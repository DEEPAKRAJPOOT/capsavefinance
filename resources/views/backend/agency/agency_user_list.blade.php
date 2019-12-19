@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Agency User</h3>
            <small>Agency User List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Agency</li>
                <li class="active">Agency User List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                            @can('add_anchor_user_reg')
                            <a data-toggle="modal" data-target="#addAgencyUserFrame" data-url="{{route('add_agency_user_reg')}}" data-height="375px" data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Agency User
                                </button>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>     
            </div>

            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by User Name, Email Id" id="by_name" name="search_keyword" type="text">
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="agencyUserList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Agency Name</th>
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
{!!Helpers::makeIframePopup('addAgencyUserFrame','Add Agency User', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editAgencyUserFrame','Edit Agency User Detail', 'modal-md')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_agency_user_list: "{{ URL::route('get_ajax_agency_user_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/agency.js') }}"></script>
@endsection