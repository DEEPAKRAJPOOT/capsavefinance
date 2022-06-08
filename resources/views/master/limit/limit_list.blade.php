@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Borrower Limit</h3>
            <small>Limit List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Borrower Limit</li>
                <li class="active">Limit List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body"> 
            <div class="row" style="margin-bottom: 25px;">
            @can('add_borrower_limit')
            @if($is_avail == false)
                <div class="col-md-12 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addLimitFrame" data-url ="{{ route('add_borrower_limit') }}" data-height="300px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i> Add Limit
                    </a>
                </div>
            @endif
            @endcan  
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="LimitList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Sr No.</th>
                                    <th>Single Borrower limit (in mn)</th>
                                    <th>Group Borrower Limit (in mn)</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
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

{!!Helpers::makeIframePopup('addLimitFrame','Add Borrower Limit', 'modal-md')!!}
{!!Helpers::makeIframePopup('editBorrowerLimitFrame','Edit Borrower Limit', 'modal-md')!!}

@endsection

@section('jscript')
<script>

var messages = {
    get_ajax_limit_list: "{{ URL::route('get_ajax_limit_list') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/limit.js') }}"></script>
@endsection