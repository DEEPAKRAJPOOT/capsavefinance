@extends('layouts.backend.admin_popup_layout')
@section('content')

        <div class="header-title">
            <small>Interes Amount</small>
        </div>
	
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="interestRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th>#</th>
									<th>Customer ID</th>
									<th>Trans Date</th>
									<th>Invoice No</th>
									<th>Amount</th>
									<th>Balance Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
			</div>
		</div>
	</div>
</div>


        <div class="header-title">
            <small>Non Factored Amount</small>
        </div>
	
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="nonFactoredRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th>#</th>
									<th>Customer ID</th>
									<th>Trans Date</th>
									<th>Amount</th>
									<th>Balance Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="selectChechbox">

							</tbody>
						</table>
					</div>
					<div id="nonFactoredRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection




@section('jscript')
<script>

    var messages = {
		action: "{{ $action }}",
		non_factored_id: "{{config('lms.TRANS_TYPE.NON_FACTORED_AMT')}}",
		interest_refund: "{{config('lms.TRANS_TYPE.INTEREST_REFUND')}}",
        lms_get_refund_adjust: "{{ URL::route('lms_get_refund_adjust') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>

<script src="{{ asset('backend/js/lms/refund.js') }}" type="text/javascript"></script>
<script>
	let selectChechbox = document.getElementById('selectChechbox');
	let chks = selectChechbox.getElementsByTagName('INPUT')

	for(let i = 0; i < chks.length; i++) {
		if(chks[i].checked) {
			console.log('he')
			console.log(chks)
		}
	}
</script>
@endsection