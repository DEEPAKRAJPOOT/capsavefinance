@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
    <section class="content-header">
            <div class="header-icon">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
            </div>
            <div class="header-title">
                <h3 class="mt-2">Nach List</h3>
                <ol class="breadcrumb">
                    <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active"> Nach List</li>
                </ol>
            </div>
            <div class="clearfix"></div>
    </section>
    <div class="row grid-margin mt-2">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="form-fields">
                    <div class="active" id="details">
                        <div class="form-sections">
                            <form id="excelExportForm" action="{{Route('nach_download_reports_sheet')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">	          
                                        <input type="button" id="exportExcel" name="Submmit" class="btn btn-primary btn-sm ml-2 btn-app filter-abs-btn" value="Export Excel">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 dataTables_wrapper mt-2">
                                        <div class="overflow">
                                            <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <input type="hidden" name="front" value="front">
                                                        <table id="nachList" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                            <thead>
                                                                <tr role="row">
                                                                    <th><input type="checkbox" id="chkAll"></th>
                                                                    <th class="white-space">Nach Date</th>
                                                                    <th>Sponsor Bank Code</th>
                                                                    <th>Account Name</th>
                                                                    <th>Account No.</th>
                                                                    <th>IFSC Code</th>
                                                                    <th>Branch Name</th>
                                                                    <th>Amount</th>
                                                                    <th>Phone No.</th>
                                                                    <th>Email</th>
                                                                    <th class="white-space">Period From</th>
                                                                    <th class="white-space">Period To</th>
                                                                    <th>Debit Type</th>
                                                                    <th class="white-space">Created At</th> 
                                                                    <th>Nach Uploaded</th> 
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
                            </form>
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
	get_all_nach: "{{ URL::route('get_all_nach') }}",
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
 };
</script>
<script src="{{ asset('backend/js/ajax-js/nach_request.js') }}"></script>
@endsection
 