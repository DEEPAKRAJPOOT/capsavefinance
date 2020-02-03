@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage GST</h3>
            <small>Entity GST</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage GST</li>
                <li class="active">GST List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row" style="margin-bottom: 25px;">
                <!-- <div class="col-md-4">
                    <input class="form-control" placeholder="Search by GST Name" id="by_name" name="search_keyword" type="text">
                </div>
                <div class="col-md-1">
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
                </div> -->
                <div class="col-md-12 text-right">
                    <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addGSTFrame" data-url ="{{route('add_Gst') }}" data-height="310px" data-width="100%" data-placement="top" >
                            <i class="fa fa-plus"></i> Add GST
                    </a>
                </div>
           </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="GSTList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>ID. No.</th>
                                    <th>Tax Type</th>
                                    <th>TAX %</th>
                                    <th>CGST %</th>
                                    <th>SGST %</th>
                                    <th>IGST %</th>
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
{!!Helpers::makeIframePopup('addGSTFrame','Add GST Type', 'modal-md')!!}
{!!Helpers::makeIframePopup('editGSTFrame','Edit GST Detail', 'modal-md')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_gst_list: "{{ URL::route('get_ajax_gst_list') }}",       
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/gst.js') }}"></script>
@endsection