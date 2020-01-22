@extends('layouts.backend.admin-layout')

@section('content')


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
<style>
    select[name='leadMaster_length']{
        height: calc(1.8125rem + 2px);
        margin: 0 10px 0 10px;
        width: 100px;
    }
    input[type='search']{
        height: calc(1.8125rem + 2px);
        display: inline;
        position: absolute;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Application</h3>
            <small>Application List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Application</li>
                <li class="active">My Application</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-md-4">

                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by App Id, Name',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">

                    {!!
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Not Assigned'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
           </div>
           <div class="row">     
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="appList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ trans('backend.app_list_head.app_id') }}</th>
                                                <th>{{ trans('backend.app_list_head.biz_name') }}</th>
                                                <th>{{ trans('backend.app_list_head.name') }}</th>
                                                <th>{{ trans('backend.app_list_head.email') }}</th>
                                                <th>{{ trans('backend.app_list_head.mobile_no') }}</th>
                                                <th>{{ trans('backend.app_list_head.anchor') }}</th>
                                                <th>{{ trans('backend.app_list_head.user_type') }}</th>
                                                <th>{{ trans('backend.app_list_head.assignee') }}</th>
                                                <th>{{ trans('backend.app_list_head.assigned_by') }}</th>
                                                {{--<th>{{ trans('backend.app_list_head.shared_detail') }}</th>--}}
                                                <th>{{ trans('backend.app_list_head.status') }}</th>
                                                <th>{{ trans('backend.app_list_head.action') }}</th>
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

{!!Helpers::makeIframePopup('addCaseNote','Add Note', 'modal-md')!!}
{!!Helpers::makeIframePopup('appStatusFrame','Change Status', 'modal-md')!!}
{!!Helpers::makeIframePopup('assignCaseFrame','Move to Back Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('sendNextstage','Send Next Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('viewApprovers','View Approver List', 'modal-lg')!!}
{!!Helpers::makeIframePopup('viewSharedDetails','View Shared Details', 'modal-lg')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        get_applications: "{{ URL::route('ajax_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/application.js') }}" type="text/javascript"></script>
@endsection