@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin_reports_links',['active'=>'charge_breakup'])
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Reports</h3>
            <small>Charge Report</small>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="row mt-4">
                <div class="col-md-3">
                    {!!
                    Form::text('from_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'From Date',
                    'id'=>'from_date',
                    'autocomplete'=>'off'
                    ])
                    !!}
                </div>
                <div class="col-md-3">
                    {!!
                    Form::text('to_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'To Date',
                    'id'=>'to_date',
                    'autocomplete'=>'off'
                    ])
                    !!}
                </div>
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button> &nbsp; &nbsp; 
                {{-- <a href="javascript:void(0)" class="btn  btn-success btn-sm float-right" id="dwnldPDF">Pdf</a> &nbsp; &nbsp;  --}}
                <a href="javascript:void(0)" class="btn  btn-success btn-sm float-right" id="dwnldEXCEL">Excel</a>
               
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="charge_breakup_listing_reports" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default"
                                        data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                        <table id="charge_breakup_report"
                                            class="table table-striped cell-border dataTable no-footer overview-table"
                                            cellspacing="0" width="100%" role="grid"
                                            aria-describedby="charge_breakup_report_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">
                                                    <th>Loan #</th>
                                                    <th>Client Name</th>
                                                    <th>Charge Date</th>
                                                    <th>Charge Name</th>
                                                    <th>Charge (%)</th>
                                                    <th>Charge Amount (₹)</th>
                                                    <th>GST Amount (₹)</th>
                                                    <th>Total Amount (₹)</th>
                                                    <th>Tally Batch #</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div id="charge_breakup_report_processing" class="dataTables_processing card"
                                        style="display: none;">Processing...</div>
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
        get_all_charge_breakups: "{{ URL::route('get_all_charge_breakups') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        get_customer: "{{ route('get_customer') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/Reports/charge_breakup.js') }}" type="text/javascript"></script>
@endsection