@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Transactions</h3>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div style="float: right"><a href="{{route('export_txns')}}" class="btn btn-success">Export</a></div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="transactions" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Date</th>
                                    <th>Label</th>                                    
                                    <th>Account Name</th>
                                    <th>Journal Name</th>
                                    <th>Invoice Id</th>
                                    <th>Debit Amount</th>
                                    <th>Credit Amount</th>
                                    <th>Reference No</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    get_ajax_transactions: "{{ URL::route('get_ajax_transactions') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection