@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Refund </h3>
            <small>(Sent to Bank)</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Manage Refund </li>
            </ol>
        </div>
    </section>
    <div class="row grid-margin">
        <div class="col-md-12">
		    <div class="card">
		        <div class="card-body">
		            @include('lms.refund.common.status_links')
		            <div class="card">
		                <div class="card-body">
				            <div class="row">
								<div class="col-md-3">
								    <select class="form-control" id="batch_id" name="batch_id">
								        <option value="" selected="">All</option>
								        @foreach($batchData as $batch)
								        <option value="{{ $batch->batch_id }}">{{ $batch->batch_id }}</option>
								        @endforeach
								    </select>
								</div>
								<button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
							</div>
							<div class="col-12 dataTables_wrapper mt-4">
								<div class="overflow">
								    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
								        <div class="row">
								            <div class="col-sm-12">
								                <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
								                    <table id="refundBatchRequest"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
								                        <thead>
								                            <tr role="row">
								                                <th width="20%">Batch ID</th>
								                                <th width="5%">Total Customer </th>
								                                <th width="20%">Total Disburse Amt.</th>
								                               <th width="15%"> Created At</th> 
								                                <th width="10%">Action</th>
								                            </tr>
								                        </thead>
								                        <tbody>

								                        </tbody>
								                    </table>
								                </div>
								                <div id="refundBatchRequest_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
    </div>
</div>
{!!Helpers::makeIframePopup('lms_view_process_refund','Process Refund', 'modal-lg')!!}
{!!Helpers::makeIframePopup('invoiceDisbursalTxnUpdate','Update Trasaction Id', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

    var messages = {
        backend_ajax_get_refund_batch_request: "{{ URL::route('backend_ajax_get_refund_batch_request') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/lms/request.js') }}" type="text/javascript"></script>
@endsection