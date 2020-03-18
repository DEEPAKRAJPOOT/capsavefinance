@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Charges</h3>
            <small>Charges List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Charges</li>
                <li class="active">Charges List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search by Name, Amount or Description" id="by_name" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div>
                <div class="col-md-7 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addChargesFrame" data-url ="{{route('add_charges')}}" data-height="400px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i>Add Charges
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="chargesList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>Charge Name</th>
                                    <th>Charge Type</th>
                                    <th>Charge Calculation</th>
                                    <th>Charge Amt/Per</th>
                                    <th>GST Applicable</th>
                                    <th>Applicability</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Status</th>
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
{!!Helpers::makeIframePopup('addChargesFrame','Add Charges', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editChargesFrame','Edit Charges Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_charges_list: "{{ URL::route('get_ajax_charges_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/charges.js') }}"></script>
@endsection