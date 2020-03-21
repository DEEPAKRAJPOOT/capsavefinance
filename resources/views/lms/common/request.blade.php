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
                                                    <th>Ref No</th>
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
{!!Helpers::makeIframePopup('lms_move_next_stage','Move to Next Stage', 'modal-lg')!!}
{!!Helpers::makeIframePopup('lms_move_prev_stage','Move to Previous Stage', 'modal-lg')!!}
{!!Helpers::makeIframePopup('lms_update_request_status','Update Status', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('lms_get_request_list') }}",
        lms_edit_batch: "{{ URL::route('lms_edit_batch') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/lms/request.js') }}" type="text/javascript"></script>
@endsection




