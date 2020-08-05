@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage TDS</h3>
            <small>TDS List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage TDS</li>
                <li class="active">TDS List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-12 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addTDSFrame" data-url ="{{ route('add_tds') }}" data-height="250px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i> Add TDS
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="TdsList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th style="width: 10%">Sr No.</th>
                                    <th style="width: 50%">TDS Percentage</th>
                                    <th style="width: 20%">Created at</th>
                                    <th style="width: 20%">Status</th>
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

{!!Helpers::makeIframePopup('addTDSFrame','Add TDS Percentage', 'modal-md')!!}
{!!Helpers::makeIframePopup('editTDSFrame','Edit TDS Percentage', 'modal-md')!!}

@endsection

@section('jscript')
<script>

var messages = {
    get_tds_list: "{{ URL::route('get_ajax_tds_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/tds.js') }}"></script>
@endsection