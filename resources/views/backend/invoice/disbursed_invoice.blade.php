@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')

@php 
$flag = (isset($flag)) ? $flag :     0;
$role = (isset($role)) ? $role :     11;
@endphp

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

                                        <div class="col-md-5">
                                            <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control form-control-sm"  name="search_biz"  placeholder="Search by business name, Invoice number ">
                                        </div> 
                                        <div class="col-md-1">
                                            <button  type="button" id="search_biz" class="btn  btn-success btn-sm float-right">Search</button>
                                        </div>  
                                        <div class="col-md-2" id="buttonDiv">
                                            @php if($role!=11) { @endphp
                                            <a data-url="{{ route('disburse_confirm', ['disburse_type' => 2 ]) }}" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2 disburseClickBtn" >Send To Bank</a>
                                            @php  } @endphp
                                            <a data-toggle="modal" data-target="#disburseInvoice" data-url ="" data-height="560px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisburseInvoice" style="display: none;" >Disburse Trigger</a>
                                        </div>
                                        <input type="hidden" value="" name="invoice_ids" id="invoice_ids"> 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 dataTables_wrapper mt-4">
                                        <div class="overflow">
                                            <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table id="invoiceListDisbursedQue" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                            <thead>
                                                                <tr role="row">
                                                                    <th><input type="checkbox" id="chkAll"></th> 
                                                                    <th>Invoice No</th> 
                                                                    <th>Anchor Detail</th>
                                                                    <th>Customer Detail</th>
                                                                    <th>Inv. Detail</th>
                                                                    <th>Inv.  Amount</th>
                                                                    <th> Updated By</th>
                                                                    <th> Action</th>

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
<div id="loadDiv1">
    <input type="hidden" id="loadUrl1" value=""> 
</div>
{!!Helpers::makeIframePopup('disburseInvoice','Disburse Invoices', 'modal-lg')!!}

@endsection
@section('jscript')
<script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment_que.js') }}"></script>
<script src="{{ asset('backend/js/invoice-disburse.js') }}"></script>
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
@php 
$operation_status = session()->get('operation_status', false);
@endphp
@if( $operation_status == config('common.YES')) 
<script>
try {
    var p = window.parent;
    p.jQuery('#disburseInvoice').modal('hide');
    window.parent.location.reload();
} catch (e) {
    if (typeof console !== 'undefined') {
        console.log(e);
    }
}
</script>
@endif

<script>

    var messages = {
        backend_get_invoice_list_disbursed_que: "{{ URL::route('backend_get_invoice_list_disbursed_que') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_program_supplier: "{{ URL::route('get_program_supplier') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
        invoice_document_save: "{{ URL::route('invoice_document_save') }}",
        update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
        token: "{{ csrf_token() }}",
        appp_id: "{{ $app_id }}",
    };
</script>   
@endsection
