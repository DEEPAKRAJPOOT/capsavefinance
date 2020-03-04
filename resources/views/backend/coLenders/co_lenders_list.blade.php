@extends('layouts.backend.admin-layout') 

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Co Lenders</h3>
            <small>Co Lenders List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Co Lenders</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                        @can('add_co_lender')    
                        <a  data-toggle="modal" data-target="#addcolenders"
                            data-url ="{{route('add_co_lender')}}" 
                            data-height="550px" data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Co-lender
                                </button>
                            </a>
                        @endcan
                        </div>
                    </div>
                </div>     
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="co_lenderList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Sr.No.</th>
                                    <th>Name</th>
                                    <th>Business Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <!--<th>Anchor</th>-->
                                    <th>Created At</th>
                                    <th>Status</th>
                                     <th>Actions</th>
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
{!!Helpers::makeIframePopup('addcolenders','Add/Update Co-lender', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
    var messages = {
        get_co_lender_list: "{{ URL::route('get_co_lender_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        please_select: "{{ trans('backend.please_select') }}",

    };
    $(document).ready(function () {
        oTables = $('#co_lenderList').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        searching: false,
        bSort: true,
        ajax: {
            url: messages.get_co_lender_list,
            method: 'POST',
            data: function (d) {
                d._token = messages.token;
            },
            error: function () { // error handling

                $("#leadMaster").append('<tbody class="leadMaster-error"><tr><th colspan="3">' + messages.data_not_found + '</th></tr></tbody>');
                $("#leadMaster_processing").css("display", "none");
            }
        },
        columns: [
            {data: 'co_lender_id'},
            {data: 'f_name'},
            {data: 'biz_name'},
            {data: 'email'},
            {data: 'comp_phone'},
            {data: 'created_at'},
            {data: 'status'},
            {data: 'action'}
        ],
        aoColumnDefs: [{'bSortable': false,'aTargets': [6,7]}]
    });
        
    window.refresh = function(){
        oTables.draw();
    }
});  
</script>
@endsection