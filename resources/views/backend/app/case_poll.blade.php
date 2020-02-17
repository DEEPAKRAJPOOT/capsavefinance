@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Application Pool</h3>
            <small>Pool List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Application</li>
                <li class="active">Application Pool</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 dataTables_wrapper">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="btn-group btn-custom-group inline-action-btn" "="">
                                        <!--
							       <a href="#" data-toggle="modal" data-target="#exampleModal-cls" class="btn btn-pickup btn-sm">Pick up Case</a>
                                   <a href="#" data-toggle="modal" data-target="#myModal2" class="btn btn-pickup btn-sm">Assign Case</a>
                                        -->
							  </div>
                                    
                                    <table id="apppollMaster" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">

                                            <th style="width:6%">{{ trans('backend.app_list_head.app_id') }}</th>
                                            <th style="width:24%">{{ trans('backend.app_list_head.biz_name') }}</th>
                                            <th style="width:16%">{{ trans('backend.app_list_head.name') }}</th>  
                                             <th style="width:10%">{{ trans('backend.app_list_head.contact') }}</th>
                                             <!-- {{--<th style="width:8%">{{ trans('backend.app_list_head.email') }}</th>
                                             <th style="width:10%">{{ trans('backend.app_list_head.mobile_no') }}</th>--}} -->
                                             <th style="width:10%">{{ trans('backend.app_list_head.anchor') }}</th>
                                             <!-- {{-- <th style="width:8%">{{ trans('backend.app_list_head.user_type') }}</th>--}}    -->
                                              <th style="width:10%">{{ trans('backend.app_list_head.assignee') }}</th>
                                              <th style="width:12%">{{ trans('backend.app_list_head.assigned_by') }}</th>
                                              <!-- {{-- <th style="width:8%">{{ trans('backend.app_list_head.shared_detail') }}</th>--}} -->
                                              <th style="width:5%">{{ trans('backend.app_list_head.status') }}</th>
                                              <th style="width:7%">{{ trans('backend.app_list_head.action') }}</th>
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
</div>

{!!Helpers::makeIframePopup('pickLead','Pick Lead','modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        get_case_pool: "{{ URL::route('get_case_pool') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/app_poll.js') }}" type="text/javascript"></script>
@endsection