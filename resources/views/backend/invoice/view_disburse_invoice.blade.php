@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="modal-body text-left">
    <div class="row">   
        <div class="col-sm-12">
            <div class="table-responsive">
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