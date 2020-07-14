@extends('layouts.backend.admin-layout')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Refund </h3>
            <small>(New Request)</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Manage Refund </li>
            </ol>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            @include('lms.refund.common.status_links')
            <form id="refundReqForm" action="{{ route('lms_refund_request_udate',['status' => 1, 'newStatus' => 3]) }}" method="post">
            <div class="row">
                @csrf	
                <div class="card-body">
                    <div class="row pull-right">
                        <div class="col-md-2" id="buttonDiv">
                            <button type="button" class="btn btn-success btn-sm ml-2" id="pendingBtn">Generate Refund Request</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="requestList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
                                                    <th><input type="checkbox" id="chkAll"></th>
                                                    <th>Ref No</th>
                                                    <th>Customer ID</th>
                                                    <th>Entity Name</th>                                                    
													<th>Amount</th>
                                                    <th>Date</th>
                                                    {{-- <th>Assignee</th>	
                                                    <th>Assigned By</th> --}}
												</tr>
	                                        </thead>
	                                        <tbody>
	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="requestList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
{!!Helpers::makeIframePopup('lms_view_process_refund','View Refund Request', 'modal-lg')!!}
@endsection
@section('jscript')
    <script>
        var messages = {
            url: "{{ URL::route('lms_get_request_list') }}",
            data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
            status:"1",
        };
    </script>
    <script src="{{ asset('backend/js/lms/request.js') }}" type="text/javascript"></script>
@endsection




