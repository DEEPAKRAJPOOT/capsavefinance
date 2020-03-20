@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
    <div class="col-md-12 ">
        <section class="content-header">
            <div class="header-icon">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
            </div>
            <div class="header-title">
                <h3 class="mt-2">View Disburse Invoice</h3>

                <ol class="breadcrumb">
                    <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active"><a href="">Bank Invoice</a></li>
                    <li class="active"><a href="">Disbursed Customers List</a></li>
                    <li class="active">View Disburse Invoice List</li>
                </ol>
            </div>
            <div class="clearfix"></div>
        </section>
        <div class="row grid-margin">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">      
                            <div class="card">
                                <div class="card-body">                                    
                                    <div class="row">
                                        <div class="col-12 dataTables_wrapper mt-4">
                                            <div class="overflow">
                                                <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                        <input type="hidden" name="batch_id" value="{{$batch_id}}" />
                                                        <input type="hidden" name="disbursed_user_id" value="{{$disbursed_user_id}}" />
                                                            <table id="viewDisburseInvoice" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                                <thead>
                                                                    <tr role="row">
                                                                        <th>App ID</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Disburse Date</th>
                                                                        <th>Invoice Due Date</th>
                                                                        <th>Amount Disbursed</th>
                                                                        <th>Disburse Type</th>
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
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscript')  
<script>
var messages = {
    get_ajax_view_disburse_invoice: "{{ URL::route('get_ajax_view_disburse_invoice') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/bank_invoice.js') }}"></script>
@endsection