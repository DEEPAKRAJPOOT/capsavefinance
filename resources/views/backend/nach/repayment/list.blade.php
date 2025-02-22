@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage NACH Repayment </h3>
            <small>(Pending Request)</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Manage NACH Repayment</li>
            </ol>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            @include('layouts.backend.nach_status_links')
            <form id="nachReqForm" action="{{ route('create_nach_repayment_req') }}" method="post">
            <div class="row">
                @csrf	
                <div class="col-md-12 mt-4">
                    <div class="row pull-right">
                    @can('create_nach_repayment_req')
                        <div class="col-md-2" id="buttonDiv">
                            <button type="button" class="btn btn-success btn-sm ml-2" id="nachExpBtn">Generate Request</button>
                        </div>
                    </div>
                    @endcan
                    <div class="row col-6 pull-left">
                    @include('lms.refund.common.search')
                    </div>
                </div>
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="nachRepaymentList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
                                                    <th><input type="checkbox" id="chkAll"></th>
                                                    <th>Customer ID</th>
                                                    <th>UMR No</th>
													<th>NACH Max Amount</th>
													<th>Outstanding Amount</th>
												</tr>
	                                        </thead>
	                                        <tbody>

	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="nachRepaymentList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
        url: "{{ URL::route('lms_get_nach_repayment_list') }}",
        lms_edit_batch: "{{ URL::route('lms_edit_batch') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        status:"3",
        columns: [
            {data: 'id'},
            {data: 'customer_id'},
            {data: 'umr_no'},
            {data: 'nach_amount'},
            {data: 'amount'},                    
            // {data: 'assignee'},
            // {data: 'assignedBy'}
        ]
    };
</script>
<script src="{{ asset('backend/js/lms/nach.js') }}" type="text/javascript"></script>
@endsection




