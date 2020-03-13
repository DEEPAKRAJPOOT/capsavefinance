@extends('layouts.backend.admin_popup_layout')
@section('additional_css')
@section('content')


<div class="col-12">
	<div class="overflow">
		<div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
						<table id="interestRefundList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
							<thead>
								<tr role="row">
									<th>Trans Date</th>
									<th>Value Date</th>
									<th>Tran Type</th>
									<th>Invoice No</th>
									<th>Debit</th>
									<th>Credit</th>
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





@endsection

@section('jscript')

@endsection