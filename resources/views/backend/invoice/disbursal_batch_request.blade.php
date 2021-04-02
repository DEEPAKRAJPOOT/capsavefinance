@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@if($flag == 1)
@include('layouts.backend.partials.admin_customer_links',['active' => 'invoice'])
@endif
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
        </div>
        <div class="header-title">
            <h3 class="mt-2">Manage Invoice</h3>

            <ol class="breadcrumb">
                <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                <li class="active">Manage Invoice</li>
            </ol>
        </div>
        <div class="clearfix"></div>
    </section>
    <div class="row grid-margin">

        <div class="col-md-12 ">
            <div class="card">
                <div class="card-body">
                    @include('layouts.backend.invoice_status_links')


                    <div class="card">
                        <div class="card-body">       
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" id="batch_id" name="batch_id">
                                        <option value="" selected="">All</option>
                                        @foreach($batchData as $batch)
                                        <option value="{{ $batch->batch_id }}">{{ $batch->batch_id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                            </div>

                            <div class="col-12 dataTables_wrapper mt-4">
                                <div class="overflow">
                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                                    <table id="disbursalBatchRequest"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                        <thead>
                                                            <tr role="row">
                                                                <th width="20%">Batch ID</th>
                                                                <th width="5%">Total Customer </th>
                                                                <th width="20%">Total Disburse Amt.</th>
                                                               <th width="15%"> Created At</th> 
                                                                <th width="10%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="disbursalBatchRequest_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('viewOnlineDisbursalRollback','Online Disbursal Rollback', 'modal-lg')!!}
@endsection
@section('jscript')
<style>
    .itemBackground 
    { 
        border: 2px solid blanchedalmond;  
        background-color:#138864;
    }
    .itemBackgroundColor 
    { 
        color:white;
    }
</style>    
<script>

    var messages = {
        backend_ajax_get_disbursal_batch_request: "{{ URL::route('backend_ajax_get_disbursal_batch_request') }}",
        token: "{{ csrf_token() }}",
    };
    $('#selected_date').datetimepicker({
        useCurrent: true,
        format: 'yyyy-mm-dd',
        // startDate: new Date(),
        autoclose: true,
        minView: 2,
        defaultDate: new Date(),
    })
</script>
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursal_batch_request.js') }}"></script>

@endsection
