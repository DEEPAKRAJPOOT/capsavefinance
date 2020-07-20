@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Anchor Lead</h3>
            <small>Anchor List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Anchor</li>
                <li class="active">Manage Anchor Lead</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
           
            <div class="row">
                
                <div class="col-sm-4">
                   <!-- 
                    {!!
                    Form::select('pan',
                    [''=>'Select Pan'],
                    null,
                    array('id' => 'pan',
                    'class'=>'form-control'))
                    !!}
                    -->
                   
                    {!!
                    Form::text('by_email',                    
                    null,
                    array('id' => 'by_email',
                    'class'=>'form-control', 'placeholder'=>'Search By name or email or pan or anchor'))
                    !!}
                  
                </div>
                
                <div class="col-sm-2">
                    <button id="anchleadListSearchB" type="button" class="btn  btn-success btn-sm">Search</button>
                </div>                
                <div class="col-sm-6">
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;">
                        @can('add_anchor_lead')   
                        <a  data-toggle="modal" data-target="#uploadAnchLead" data-url ="{{route('add_anchor_lead')}}" data-height="260px"  data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button"> <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Upload Anchor Lead
                            </a>
                        @endcan
                        </button>
                        </div>
                    </div>
                    <div class="head-sec">
                        <div class="pull-right" style="margin-bottom: 10px;margin-right: 12px;">
                        @can('add_manual_anchor_lead')    
                        <a  data-toggle="modal" data-target="#addAnchorFrm" data-url ="{{route('add_manual_anchor_lead')}}" data-height="400px" data-width="100%" data-placement="top" >
                                <button class="btn  btn-success btn-sm" type="button">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add Anchor Lead
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
                                    <table id="anchleadList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>Sr.No.</th>
                                                <th>Name</th>
                                                <th>Business Name</th>
                                                <th>PAN No.</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Anchor</th>
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
    </div>
</div>
{!!Helpers::makeIframePopup('addAnchorFrm','Add Anchor Lead', 'modal-md')!!}
{!!Helpers::makeIframePopup('editAnchorFrm','Update Anchor', 'modal-md')!!}
{!!Helpers::makeIframePopup('uploadAnchLead','Upload User List', 'modal-md')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        get_anch_lead_list: "{{ URL::route('get_anch_lead_list') }}",       
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
@endsection