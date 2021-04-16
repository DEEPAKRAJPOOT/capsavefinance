@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Bank</h3>
            <small>Bank List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Bank </li>
                <li class="active">Bank List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by bank Name" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="submit" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-2 text-right">
                    @can('add_new_bank')
                    <a data-toggle="modal" data-height="400px" data-width="100%" data-target="#addBankMaster" 
                        id="addBank" data-url="{{ route('add_new_bank') }}" >
                        <button class="btn  btn-success btn-sm float-left mb-2" type="button">
                            <i class="fa fa-plus"></i> Add New Bank
                        </button>
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive" id="fi_list">
                        <table id="BankList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th width="10%">S. No.</th>
                                    <th width="25%">Bank Name</th>
                                    <th width="25%">Perfious Id</th>
                                    <th width="20%">Status</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div id="BankList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('addBankMaster','Add Bank', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editBankMaster','Edit Bank', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
var messages = {
        get_bank_list: "{{ URL::route('get_ajax_bank_list') }}",       
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/bank.js') }}"></script>
@endsection
