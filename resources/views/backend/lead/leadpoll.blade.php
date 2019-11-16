@extends('layouts.backend.admin-layout')

@section('content')

 @include('layouts.backend.partials.admin-sidebar')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
<style>
select[name='leadMaster_length']{
    height: calc(1.8125rem + 2px);
    margin: 0 10px 0 10px;
    width: 100px;
}
input[type='search']{
    height: calc(1.8125rem + 2px);
    display: inline;
    position: absolute;
    border: 1px solid rgba(0, 0, 0, 0.15);
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Leads</h3>
            <small>Supplier List</small>
            <ol class="breadcrumb">
                <li><a href="https://admin.zuron.in/admin/dashboard"><i class="mdi mdi-home"></i> Home</a></li>
                <li class="active">Manage Leads</li>
            </ol>
        </div>
    </section>




    <div class="card">
        <div class="card-body">

            <input type="hidden" name="status" value="">
            <input type="hidden" name="head" value="">

            <div class="head-sec">
                <div class="pull-right" style="margin-bottom: 10px;">
                    <a href="javascript:void(0);" id="register">
                        <button class="btn btn-labeled btn-success m-b-5" type="button">
                            <span class="btn-label">
                                <i class="fa fa-plus"></i>
                            </span>
                            Add Supplier
                        </button>
                    </a>
                </div>
            </div>



            <div class="row">
                <div class="col-12 dataTables_wrapper">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="leadpollMaster" class="table white-space table-striped cell-border dataTable no-footer" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                
                                                <th>Sr.No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                 <th>Assigned</th>
                                                <th>Created At</th>
                                                <th>Action</th>
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
    
    

    @endsection
    @section('pageTitle')
Admin- Login
@endsection
{!!Helpers::makeIframePopup('editLeadpoll','Edit Lead')!!}
    @section('jscript')
    <script>

        var messages = {
            get_lead_pool: "{{ URL::route('get_lead_pool') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",

        };
    </script>
    <script src="{{ asset('common/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('backend/js/ajax-js/leadpolls.js') }}" type="text/javascript"></script>
    @endsection