@extends('layouts.backend.admin_popup_layout')
@section('additional_css')
@section('content')
<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">                                 
					@include('lms.refund.common.payment_advise')
					<div id="interestRefundList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
				</div>
            </div>
		</div>
	</div>
</div>
@endsection