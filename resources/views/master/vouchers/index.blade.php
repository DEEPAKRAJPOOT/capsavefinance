@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Vouchers</h3>
            <small>Vouchers List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Vouchers</li>
                <li class="active">Vouchers List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by level" id="by_name" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                <div class="col-md-7 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addVoucherFrame" data-url ="{{route('add_voucher')}}" data-height="250px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i>Add Voucher
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="voucherList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Voucher Code</th>
                                    <th>Voucher Name</th>
                                    <th>Transaction Type</th>
                                    <th>Action</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="voucherList_processing" class="voucherList_processing card" style="display: none;">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{!!Helpers::makeIframePopup('addVoucherFrame','Add Voucher', 'modal-md')!!}
{!!Helpers::makeIframePopup('editVoucherFrame','Edit Voucher', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
var messages = {
    get_vouchers_list: "{{ URL::route('get_ajax_voucher_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/voucher.js') }}"></script>
@endsection