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
                <div class="col-sm-12" style="display: flex;">
                    <div class="col-sm-6">
                        <!-- <select class="form-control" id="voucher_type">
                            <option value="">Voucher Type</option>
                            <option value="1">JOURNAL</option>
                            <option value="2">BANK</option>
                        </select> -->
                    </div>
                    <div class="col-sm-6" style="text-align: right">
                        <a href="{{route('export_txns')}}" class="btn btn-success col-sm-2" id="export_txns">Export</a>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="transactions" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                
                                <tr role="row">
                                    <th>Voucher Type</th>
                                    <th>Voucher Code</th>
                                    <th>Date</th>
                                    <th>Ledger Name</th>
                                    <th>Amount</th>
                                    <th>Entry Type</th>
                                    <th>Batch No</th>
                                    <th>Mode of Pay</th>
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
    export_txns_url: "{{route('export_txns')}}",
    };
    // $(document).on('click', '#export_txns', function(e) {
    //    let voucher_type =  $('#voucher_type').val();
    //    $('.error').remove();
    //    if (!voucher_type && voucher_type != 1 && voucher_type != 2) {
    //         $('#voucher_type').after('<span style="color:red" class="error">Please select valid voucher type</span>');
    //         return false;
    //    }
    //    export_txns_url  = messages.export_txns_url + '?type=' + voucher_type;
    //    window.location = export_txns_url;
    // })
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection