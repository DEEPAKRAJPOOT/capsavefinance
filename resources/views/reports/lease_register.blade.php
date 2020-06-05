@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin_reports_links',['active'=>'lease_register'])
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Reports</h3>
            <small>Lease Register Reports</small>
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
                    'id'=>'from_date'
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
                    'id'=>'to_date'
                    ])
                    !!} 
                </div>
                <div class="col-md-3" id="prefetch">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Client ID/Name',
                    'id'=>'search_keyword',
                    'autocomplete'=>'off'
                    ])
                    !!}
                </div>
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                &nbsp; &nbsp; <a href="javascript:void(0)" class="btn  btn-success btn-sm float-right" id="dwnldPDF">Pdf</a> &nbsp; &nbsp; <a href="javascript:void(0)" class="btn  btn-success btn-sm float-right" id="dwnldEXCEL">Excel</a>
                {!! Form::hidden('user_id', 
                    isset($user['user_id'])?$user['user_id']:null, 
                    [ 'id'=>'user_id' ]) 
                !!}

                {!! Form::hidden('customer_id',  
                    isset($user['customer_id'])?$user['customer_id']:null, 
                    [ 'id'=>'customer_id' ])
                !!}
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="lease_register_listing_reports" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
                                        <table id="lease_register_report"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="lease_register_report_info" style="width: 100%;">
                                            <thead>
                                            <tr role="row">
                                                    <th>State</th>
                                                    <th>GSTN</th>
                                                    <th>Customer Name</th>
                                                    <th>Customer Address</th>
                                                    <th>Customer GSTN</th>
                                                    <th>SAC Code</th>
                                                    <th>Contract No</th>
                                                    <th>Invoice No</th>
                                                    <th>Invoice Date</th>
                                                    <th>Base Amount</th>
                                                    <th>SGST Rate</th>
                                                    <th>SGST Amount</th>
                                                    <th>CGST Rate</th>
                                                    <th>CGST Amount</th>
                                                    <th>IGST Rate</th>
                                                    <th>IGST Amount</th>
                                                    <th>Total Amount</th>
                                                    <th>Total Rate</th>
                                                    <th>Total Tax</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div id="lease_register_report_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
<script src="{{ asset('backend\theme\assets\plugins\typeahead\handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend\theme\assets\plugins\bootstrap-tagsinput\typeahead.bundle.js') }}" type="text/javascript"></script>
<script>
    var messages = {
        get_all_lease_registers: "{{ URL::route('get_all_lease_registers') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        get_customer: "{{ route('get_customer') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/Reports/lease_register.js') }}" type="text/javascript"></script>
@endsection