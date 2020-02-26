@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Financial Trans Config</h3>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Select Type</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control form-control-sm">
                            <option>Select Type</option>
                            <option>Loan Sanction</option>
                            <option>Disbursal</option>
                            <option>Repayment</option>
                            <option>Charges</option>
                            <option>Penalty</option>
                            <option>Fees</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="mb-0">Variables</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <select class="multi-select-demo form-control form-control-sm" multiple="multiple">
                            <option>IA</option>
                            <option>ROI</option>
                            <option>TP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="accountList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Code</th>
                                    <th>Name</th>
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
<script src="{{ asset('backend/assets/js/bootstrap-multiselect.js') }}"></script>
<script>
$('.multi-select-demo').multiselect();
var messages = {
    get_ajax_account_list: "{{ URL::route('get_ajax_account_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/finance.js') }}"></script>
@endsection