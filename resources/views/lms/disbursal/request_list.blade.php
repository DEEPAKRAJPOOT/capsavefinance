@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
	<section class="content-header">
		<div class="header-icon">
			<i class="fa  fa-list"></i>
		</div>
		<div class="header-title">
			<h3>Manage Sanction Cases </h3>
			<small>Customer List</small>
			<ol class="breadcrumb">
				<li style="color:#374767;"> Home </li>
				<li style="color:#374767;">Manage Customers</li>
				<li class="active">My Customers</li>
			</ol>
		</div>
	</section>

	<div class="card">
		<div class="card-body">
			<div id="Anchor-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">

				<div class="row">
					<div class="col-sm-12 col-md-3">
						<div class="dataTables_length" id="Anchor-listing_length">
							<label>
								Show
								<select name="Anchor-listing_length" aria-controls="Anchor-listing" class="form-control form-control-sm">
									<option value="10">10</option>
									<option value="25">25</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
								entries
							</label>
						</div>
					</div>
					<div class="col-md-7 ">
						<div class="row align-items-baseline">
							<div class="col-md-6 d-flex align-items-center justify-content-between">
								<span>From Date:</span>
								<input type="date" class="form-control  form-control-sm width-65">
							</div>
							<div class="col-md-6 d-flex align-items-center justify-content-between">
								<span class="text-right">To Date:</span>
								<input type="date" class="form-control  form-control-sm width-65">
							</div>
						</div>
					</div>
					<div class="col-sm-12 col-md-2">
						<div id="Anchor-listing_filter" class="dataTables_filter">
							<select class="form-control form-control-sm">
								<option>Select</option>
								<option>Ready To Process</option>
								<option>Completed</option>
							</select>
						</div>
					</div>
				</div>

				<!-- data table -->
				<div class="row">
					<div class="col-sm-12">
						<div class="table-responsive ps ps--theme_default mt-2" data-ps-id="2aab587e-2875-e85b-2324-03a84ee06a3b">
							<table id="disbursal-listing" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="disbursal-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
										<th>Customer Code</th>
										<th>Ben Name</th>
										<th>Ben Bank Name</th>
										<th>Ben IFSC</th>
										<th>Ben Account No.</th>
										<th>Total Invoice Amt.</th>
										<th>Total Invoice </th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<tr role="row" class="odd">
										<td>001</td>
										<td width="10%">Chandan</td>
										<td width="14%">HDFC</td>
										<td width="14%">HDFC000423</td>
										<td>042156958953</td>
										<td><i class="fa fa-inr"></i>96292</td>
										<td>2</td>
										<td width="18%">
											<a title="Create Disbursment" href="#" data-toggle="modal" data-target="#myModal1" class="btn btn-action-btn btn-sm"><i class="fa fa-plus-square-o" aria-hidden="true"></i></a>
											<a title="Download Disbursment Request" href="xls/disbursment-request.xls" class="btn btn-action-btn btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a>
											<a title="Download Disbursment Response" href="xls/disbursment-response.xls" class="btn btn-action-btn btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="accordion" class="accordion">
							<div class="card card-color mb-0">
								<div class="card-header pl-0 pr-0" data-toggle="collapse" href="#collapseOne" aria-expanded="true">
									<table cellspacing="0" cellpadding="0" width="100%" class="table-i">
										<tbody>
											<tr role="row" class="odd">
												<td>001</td>
												<td width="10%">Chandan</td>
												<td width="14%">HDFC</td>
												<td width="14%">HDFC000423</td>
												<td>042156958953</td>
												<td><i class="fa fa-inr"></i>96292</td>
												<td>2</td>
												<td width="18%">
													<a title="Create Disbursment" href="#" data-toggle="modal" data-target="#myModal1" class="btn btn-action-btn btn-sm"><i class="fa fa-plus-square-o" aria-hidden="true"></i></a>
													<a title="Download Disbursment Request" href="xls/disbursment-request.xls" class="btn btn-action-btn btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a>
													<a title="Download Disbursment Response" href="xls/disbursment-response.xls" class="btn btn-action-btn btn-sm"><i class="fa fa-download" aria-hidden="true"></i></a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div id="collapseOne" class="card-body bdr pt-2 pb-2 collapse show" data-parent="#accordion" style="">
									<ul class=" p-0 m-0 d-flex justify-content-between">
										<li><input type="checkbox"></li>
										<li>Invoice No. <br> <i class="fa fa-inr"></i> <b>1</b></li>
										<li>Invoice Date <br> <b>3-Jan 2020</b></li>
										<li>Invoice Due Date <br> <b>3-Mar 2020</b></li>
										<li>Invoice Amt. <br> <i class="fa fa-inr"></i><b>60,000</b></li>
										<li>Disburse Amt.  <br><i class="fa fa-inr"></i><b>48146</b></li>
										<li>Status  <br> <span class="badge badge-warning">Sent to Bank</span></li>
									</ul>
									<hr>
									<ul class=" p-0 mt-3 d-flex justify-content-between">
										<li><input type="checkbox"></li>
										<li>Invoice No. <br> <i class="fa fa-inr"></i> <b>1</b></li>
										<li>Invoice Date <br> <b>3-Jan 2020</b></li>
										<li>Invoice Due Date <br> <b>3-Mar 2020</b></li>
										<li>Invoice Amt. <br> <i class="fa fa-inr"></i><b>60,000</b></li>
										<li>Disburse Amt.  <br><i class="fa fa-inr"></i><b>48146</b></li>
										<li>Status  <br> <span class="badge badge-warning">Sent to Bank</span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


@endsection




