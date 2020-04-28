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

                    <div class="tab-content">

                        <div id="menu1" class=" active tab-pane "><br>
  <span id="moveCase" class="text-success"></span>

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control form-control-sm"  name="search_biz"  placeholder="Search by business name, Invoice number ">
                                        </div> 
                                        <div class="col-md-1">
                                            <button  type="button" id="search_biz" class="btn  btn-success btn-sm float-right">Search</button>
                                        </div>  
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-12 dataTables_wrapper mt-4">
                                            <div class="overflow">
                                                <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <table id="invoiceListDisbursed" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                                <thead>
                                                                    <tr role="row">
                                                                        <th> Inv. No. </th>
                                                                        <th> Batch ID </th> 
                                                                        <th> Anchor Detail </th>
                                                                        <th> Customer Detail </th>
                                                                        <th> Inv Detail </th>
                                                                        <th> Inv Amount </th>
                                                                        <th> Updated By</th>
                                                                        <th> Action </th>
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

    {!!Helpers::makeIframePopup('modalInvoiceDisbursed','Repayment Details', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('viewDisbursalCustomerInvoice','View Disbursal Customer Invoice', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('viewInterestAccrual','Interest Accrual', 'modal-lg')!!}
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
            backend_get_invoice_list_disbursed: "{{ URL::route('backend_get_invoice_list_disbursed') }}",
            upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
            get_program_supplier: "{{ URL::route('get_program_supplier') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            get_biz_anchor: "{{ URL::route('get_biz_anchor') }}",
            front_program_list: "{{ URL::route('front_program_list') }}",
            front_supplier_list: "{{ URL::route('front_supplier_list') }}",
            update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
            lms_get_disbursal_list: "{{ URL::route('lms_get_disbursal_list') }}",
            invoice_document_save: "{{ URL::route('invoice_document_save') }}",
            update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
            token: "{{ csrf_token() }}",
        };

    </script>
    <script src="{{ asset('backend/js/manage_invoice.js') }}"></script>
    <script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment.js') }}"></script>

    @endsection
