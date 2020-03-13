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
                                <tr role="row" class="odd">
                                    <td class="sorting_1">03-Mar-2020</td>
                                    <td>05-Mar-2020</td>
                                    <td>Repayment</td>
                                    <td>INV000001</td>
                                    <td>3000.00</td>
                                    <td>233000</td>
                                </tr>

                                <!-- blank -->
                                <tr role="row" class="odd">
                                    <td></td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td>Total Factored</td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="font-weight:bold"><b>Non Factored</b></td>
                                </tr>

                                <!-- blank -->
                                <tr role="row" class="odd">
                                    <td></td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td>Total amt for Margin</td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td>% Margin</td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td>Overdue Interest</td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="font-weight:bold"><b>Margin Released</b></td>
                                </tr>

                                <!-- blank -->
                                <tr role="row" class="odd">
                                    <td></td>
                                </tr>

                                <tr role="row" class="odd">
                                    <td style="font-weight:bold"><b>Interest Refund</b></td>
                                </tr>
                                <tr role="row" class="odd">
                                    <td style="font-weight:bold; font-size: 15px"><b>Total Refund</b></td>
                                </tr>
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