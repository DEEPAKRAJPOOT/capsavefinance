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
            <small>Renewal Application List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Application</li>
                <li class="active">Renewal Application List</li>
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
                <!--
                <div class="col-md-4">

                    {!!
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Not Assigned'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                -->
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
           </div>
           <div class="row">     
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="renewalAppList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th style="width:6%">{{ trans('backend.app_list_head.app_id') }}</th>
                                                <th style="width:20%">{{ trans('backend.app_list_head.biz_name') }}</th>
                                                <th style="width:12%">{{ trans('backend.app_list_head.name') }}</th>
                                                <th style="width:9%">{{ trans('backend.app_list_head.contact') }}</th>
                                                {{-- <th>{{ trans('backend.app_list_head.email') }}</th>
                                                <th>{{ trans('backend.app_list_head.mobile_no') }}</th> --}}
                                                <th style="width:12%">{{ trans('backend.app_list_head.anchor') }}</th>
                                                {{-- <th>{{ trans('backend.app_list_head.user_type') }}</th> --}}
                                                <th style="width:12%">{{ trans('backend.app_list_head.assignee') }}</th>
                                                <th style="width:12%">{{ trans('backend.app_list_head.assigned_by') }}</th>
                                                {{--<th>{{ trans('backend.app_list_head.shared_detail') }}</th>--}}
                                                <th style="width:5%">{{ trans('backend.app_list_head.status') }}</th>
                                                <th style="width:12%">{{ trans('backend.app_list_head.action') }}</th>
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

{!!Helpers::makeIframePopup('addAppCopy','Add Copy', 'modal-md')!!}
{!!Helpers::makeIframePopup('addCaseNote','Add Note', 'modal-md')!!}
{!!Helpers::makeIframePopup('appStatusFrame','Change Status', 'modal-md')!!}
{!!Helpers::makeIframePopup('assignCaseFrame','Move to Back Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('sendNextstage','Send Next Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('viewApprovers','View Approver List', 'modal-lg')!!}
{!!Helpers::makeIframePopup('viewSharedDetails','View Shared Details', 'modal-lg')!!}
{!!Helpers::makeIframePopup('confirmCopyApp','Copy/Renew Application', 'modal-lg')!!}

@endsection
@section('additional_css')
<style>
    #appList_wrapper  #appList_info{margin: -36px 0px 0px 164px;}
    .pl-3, .px-3, .table th, .table td{
        padding-left:8px!important;
        padding-right:4px!important;
        }
    </style>
    @endsection
@section('jscript')
<script>

var messages = {
    get_renewal_applications: "{{ URL::route('ajax_renewal_app_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",

};
    
$(document).ready(function () {
    //User Listing code
    oTable = $('#renewalAppList').DataTable({
        "dom": '<"top">rt<"bottom"flpi><"clear">',
        autoWidth:false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        searching: false,
        bSort: false,
            // "scrollY": 400,
            // "scrollX": true,
            // scrollCollapse: true,            
        ajax: {
            "url": messages.get_renewal_applications, // json datasource
            "method": 'POST',
            data: function (d) {
                d.search_keyword = $('input[name=search_keyword]').val();
                d.is_assign = $('select[name=is_assign]').val();
                d._token = messages.token;
            },
            "error": function () {  // error handling

                $("#appList").append('<tbody class="appList-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                $("#appList_processing").css("display", "none");
            }
        },
       columns: [
            {data: 'app_id'},
            {data: 'biz_entity_name'},
            {data: 'name'},
            {data: 'contact'},
            // {data: 'email'},
           // {data: 'mobile_no'},
            {data: 'assoc_anchor'},
           // {data: 'user_type'},
            {data: 'assignee'},
            {data: 'assigned_by'},
           // {data: 'shared_detail'},
            {data: 'status'},
            {data: 'action'}
        ],
        aoColumnDefs: [{'bSortable': false, 'aTargets': [0,1,3,4,5,6,7]}]

    });

    //Search
    $('#searchRenewaAppsBtn').on('click', function (e) {
        oTable.draw();
    });
 });        
</script>
@endsection