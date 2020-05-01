@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Batches</h3>
            <small>&nbsp;</small>
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
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="batches" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                
                                <tr role="row">
                                    <th>Batch No</th>
                                    <th>Records in Batch</th>
                                    <th>Date</th>
                                    <th>Action</th>
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
        get_ajax_batches: "{{ URL::route('get_ajax_batches') }}",       
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        export_txns_url: "{{route('export_txns')}}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/tally_batch.js') }}"></script>
@endsection