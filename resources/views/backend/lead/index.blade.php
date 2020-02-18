@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Leads</h3>
            <small>Lead List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Leads</li>
                <li class="active">My Leads</li>
            </ol>
        </div>
    </section>


<div class="card">
        <div class="card-body">       
            <div class="row">
                <div class="col-md-4">
                    {!!
                    Form::text('by_email',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by First name, Last name and Email',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">

                    {!!
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Pending'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                <div class="col-md-2">
                    <button id="searchB" type="button" class="btn  btn-success btn-sm">Search</button>
                </div>
                @can('create_backend_lead')
                <div class="col-md-2 text-right">
                    <div class="float-right" style="margin-bottom: 10px;margin-right: 12px;">
                        <a  data-toggle="modal" data-target="#createLeadForm" data-url ="{{route('create_backend_lead')}}" data-height="420px" data-width="100%" data-placement="top" >
                            <button class="btn  btn-success btn-sm" type="button">
                                <span class="btn-label">
                                    <i class="fa fa-plus"></i>
                                </span>
                                Create Non-Anchor Lead
                            </button>
                        </a>
                    </div>                
                </div>    
                @endcan  
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <table id="leadMaster" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr.No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Anchor</th>
                                                <th>User Type</th>
                                                <th>Assignee Detail</th>
                                                <th>Status</th>
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
</div>
{!!Helpers::makeIframePopup('editLead','Edit Lead Detail', 'modal-md')!!}
{!!Helpers::makeIframePopup('createLeadForm','Create Lead', 'modal-lg')!!}
@endsection
@section('additional_css')
<style>
#leadMaster_wrapper  #leadMaster_info{margin: -40px 0px 0px 164px;}
</style>
@endsection
@section('jscript')
<script>

    var messages = {
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>


@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES'))
    
<script>
    try {
        var p = window.parent;       
        p.jQuery('#editLead').modal('hide');
        window.parent.location.reload();
    } catch (e) {
        if (typeof console !== 'undefined') {
            console.log(e);
        }
    }
</script>

@endif
@endsection




