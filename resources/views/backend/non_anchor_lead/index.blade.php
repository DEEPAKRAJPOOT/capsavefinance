@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Non Anchor Leads</h3>
            <small>Non Anchor Leads List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Non Anchor Leads</li>
                <li class="active">Non Anchor Leads List</li>
            </ol>
        </div>
    </section>

    <div class="card">
        <div class="card-body">       
            <div class="row">
                <div class="col-sm-4">
                    {!!
                    Form::text('by_email',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by name, email, pan and business name',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>

                <div class="col-sm-2">
                    <button id="nonAnchleadListSearch" type="button" class="btn  btn-success btn-sm">Search</button>
                </div>
                @can('create_backend_lead')
                <div class="col-sm-6 float-right">
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
            </div>  
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="nonAnchleadList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Sr.No.</th>
                                    <th>Name</th>
                                    <th>Business Name</th>
                                    <th>PAN No.</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>User Type</th>
                                    <th>Created At</th>
                                    <th>Status</th>
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
{!!Helpers::makeIframePopup('createLeadForm','Create Lead', 'modal-lg')!!}
@endsection
@section('jscript')
<script>
    var messages = {
        get_non_anchor_leads: "{{ URL::route('get_non_anchor_leads') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
@endsection




