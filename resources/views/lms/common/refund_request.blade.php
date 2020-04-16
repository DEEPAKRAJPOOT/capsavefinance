@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Request </h3>
            <small>(Refund/Adjust/Wave Off)</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Manage Request </li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">
        @include('lms.common.partial.status_links')
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-2" id="buttonDiv">
                            @if($userRole->role_id == 10)
                            <a data-url="{{ route('refund_confirm', ['disburse_type' => 2 ]) }}" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2 disburseClickBtn" >Send To Bank</a>
                            @endif
                            <a data-toggle="modal" data-target="#disburseInvoice" data-url ="" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisburseInvoice" style="display: none;" >Disburse Trigger</a>
                        </div>
                        <input type="hidden" value="" name="transaction_ids" id="transaction_ids"> 
                    </div>
                </div>
                <div class="row">
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
                                                        <th>Type</th>
    													<th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Assignee</th>	
                                                        <th>Assigned By</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
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
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('disburseInvoice','Refund Amount', 'modal-lg')!!}
{!!Helpers::makeIframePopup('lms_move_next_stage','Move to Next Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('lms_move_prev_stage','Move to Previous Stage', 'modal-md')!!}
{!!Helpers::makeIframePopup('lms_update_request_status','Update Status', 'modal-md')!!}
{!!Helpers::makeIframePopup('lms_view_process_refund','Process Refund', 'modal-lg')!!}
@endsection
   
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

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('lms_get_request_list') }}",
        lms_edit_batch: "{{ URL::route('lms_edit_batch') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        status:"6",
    };
</script>
<script src="{{ asset('backend/js/lms/request.js') }}" type="text/javascript"></script>
@endsection




