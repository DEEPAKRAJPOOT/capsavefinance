<?php include 'header.php';?>

<div class="container-fluid page-body-wrapper">
	<div class="  row-offcanvas row-offcanvas-right">
		<!-- =============================================== -->
		<!-- Left side column. contains the sidebar -->

		<?php include 'sidebar.php';?>


		<!-- partial dasboard content -->
		<div class="content-wrapper">
			<section class="content-header">
				<div class="header-icon">
					<i class="fa  fa-list"></i>
				</div>
				<div class="header-title">
					<h3>Manage Buyers</h3>
					<small>Buyers List</small>
					<ol class="breadcrumb">
						<li><a href="https://admin.zuron.in/admin/dashboard"><i class="mdi mdi-home"></i> Home</a></li>
						<li class="active">Manage Suppliers</li>
					</ol>
				</div>

				<!--<div class="register-text"> <p><a href="javascript:void(0);" id="register" class="mt-10">Supplier register</a></p></div>-->
			</section>




			<div class="card">
				<div class="card-body">

					<input type="hidden" name="status" value="">
					<input type="hidden" name="head" value="">

					<!---
                        local permission_has =64
                        stage permission_has=64
                        live permission_has=65
                        --->

					<div class="head-sec">
						<div class="pull-right" style="margin-bottom: 10px;">
							<a href="javascript:void(0);" id="register">
								<button class="btn btn-labeled btn-success m-b-5" type="button">
									<span class="btn-label">
										<i class="fa fa-plus"></i>
									</span>
									Add New
								</button>
							</a>
						</div>
					</div>



					<div class="row">
						<div class="col-12 dataTables_wrapper">
							<div class="row filtersec">
								<div class="col-sm-12 col-md-6">
									<div class="dataTables_length" id="supplier-listing_length"><label>Show <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
												<option value="10">10</option>
												<option value="25">25</option>
												<option value="50">50</option>
												<option value="100">100</option>
											</select> entries</label></div>
								</div>
								<div class="col-sm-12 col-md-6">
									<div id="supplier-listing_filter" class="dataTables_filter"><label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="supplier-listing"></label></div>
								</div>
							</div>
							<div class="overflow">
								<div id="supplier-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">

									<div class="row">
										<div class="col-sm-12">
											<table id="supplier-listing" class="table white-space table-striped cell-border dataTable no-footer" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
												<thead>
													<tr role="row">
														<th class="sorting_asc">Sr.No.</th>
														<th class="sorting">Company</th>
														<th class="sorting">Email</th>
														<th class="sorting">Mobile</th>
														<th class="white-space numericCol sorting">PAN Number</th>
														<th class="white-space sorting">GST Number</th>
														<th class="white-space sorting">GST Duscount</th>
														<th class="white-space sorting">Created On</th>
														<th class="sorting">Status</th>
														<th class="sorting">Action</th>
													</tr>
												</thead>
												<tbody>

													<tr role="row" class="odd">
														<td class="sorting_1">1.</td>
														<td>ANI TECHNOLOGIES PRIVATE LIMITED</td>
														<td>equinox@ajitrentals.com</td>
														<td>9324209806</td>
														<td class=" numericCol"></td>
														<td>27APTPB7717F2ZN</td>	<td>gst_file_JK GST.pdf</td>
														<td class=" white-space">Master Admin<br> 15-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-warning current-status">Pending&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="19">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Approve</a></li>
																	<li><a href="javascript:void(0);" data-val="3" class="change-status">Block</a></li>
																	<li><a href="javascript:void(0);" data-val="2" class="change-status">Freeze</a></li>
																	<li><a href="javascript:void(0);" data-val="4" class="change-status">Reject</a></li>
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> 
														<a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="even">
														<td class="sorting_1">2.</td>
														<td>PROCON RMC PLANTS PRIVATE LIMITED</td>
														<td>gagan@proconrmc.com</td>
														<td>9930817909</td>
														<td class=" numericCol"></td>
														<td>27APTPB7717F2ZN</td>	<td>gst_file_JK GST.pdf</td>
														<td class=" white-space">Master Admin<br> 15-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-warning current-status">Pending&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="18">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Approve</a></li>
																	<li><a href="javascript:void(0);" data-val="3" class="change-status">Block</a></li>
																	<li><a href="javascript:void(0);" data-val="2" class="change-status">Freeze</a></li>
																	<li><a href="javascript:void(0);" data-val="4" class="change-status">Reject</a></li>
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> 
															<a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="odd">
														<td class="sorting_1">3.</td>
														<td>SOFOCLE TECHNOLOGIES OPC PRIVATE LIMITED</td>
														<td>khushboo.sethi@sofodev.co</td>
														<td>6545676547</td>
														<td class=" numericCol">100,000.00</td>
														<td>27APTPB7717F2ZN</td>	<td>gst_file_JK GST.pdf</td>
														<td class=" white-space">Ravi<br> 03-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-success current-status">Approved&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="17">
																	<li><a href="javascript:void(0);" data-val="3" class="change-status">Block</a></li>
																	<li><a href="javascript:void(0);" data-val="2" class="change-status">Freeze</a></li>
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="even">
														<td class="sorting_1">4.</td>
														<td>Sofocle</td>
														<td>sofocle@sofodev.co</td>
														<td>8767564534</td>
														<td class=" numericCol"></td>
														<td>27APTPB7717F2ZN</td>	<td>gst_file_JK GST.pdf</td>
														<td class=" white-space">Ravi Chamria<br> 03-Oct-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-warning current-status">Pending&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="16">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Approve</a></li>
																	<li><a href="javascript:void(0);" data-val="3" class="change-status">Block</a></li>
																	<li><a href="javascript:void(0);" data-val="2" class="change-status">Freeze</a></li>
																	<li><a href="javascript:void(0);" data-val="4" class="change-status">Reject</a></li>
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>
													<tr role="row" class="odd">
														<td class="sorting_1">5.</td>
														<td>ANNONA IT SOLUTIONS PRIVATE LIMITED</td>
														<td>partho@annona.in</td>
														<td>9986322504</td>
														<td class=" numericCol"></td>
														<td>27APTPB7717F2ZN</td>
														<td>gst_file_JK GST.pdf</td>
														<td class=" white-space">Master Admin<br> 24-Sep-2019</td>
														<td>
															<div class="btn-group ml-2 mb-1"><label class="badge badge-rejected current-status">Rejected&nbsp; &nbsp; <i class="fa fa-caret-down"></i></label>
																<ul class="change-status-toggle current-status" data-id="15">
																	<li><a href="javascript:void(0);" data-val="1" class="change-status">Approve</a></li>
																</ul>
															</div>
														</td>

														<td><a href="#" class="btn btn-primary btn-sm">Edit</a> <a href="#" class="btn btn-danger btn-sm">Delete</a></td>
													</tr>


												</tbody>
											</table>
											<div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
										</div>
									</div>

								</div>
							</div>
							<div class="row mt-4">
								<div class="col-sm-12 col-md-5">
									<div class="dataTables_info" id="supplier-listing_info" role="status" aria-live="polite">Showing 1 to 10 of 12 entries</div>
								</div>
								<div class="col-sm-12 col-md-7">
									<div class="dataTables_paginate paging_simple_numbers" id="supplier-listing_paginate">
										<ul class="pagination">
											<li class="paginate_button page-item previous disabled" id="supplier-listing_previous"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
											<li class="paginate_button page-item active"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="1" tabindex="0" class="page-link">1</a></li>
											<li class="paginate_button page-item "><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="2" tabindex="0" class="page-link">2</a></li>
											<li class="paginate_button page-item next" id="supplier-listing_next"><a href="https://admin.zuron.in/admin/manage-supplier#" aria-controls="supplier-listing" data-dt-idx="3" tabindex="0" class="page-link">Next</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- register modal -->
			<div class="modal fade" id="mySuppModal" tabindex="-1" role="dialog">
				<div id="modalSize" class="modal-dialog modal-lg" role="document">
					<div class="modal-content no-padding">
						<div class="modal-header modal-title-top">
							<h4 class="modal-title resiter"> Registration</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div id="reg-box">
							<form name="frmRegister" id="frmRegister" action="https://admin.zuron.in/admin/sup-register" method="post" novalidate="novalidate">
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtCreditPeriod">Full Name
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Full Name" required="">
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtSupplierName">Business Name
																<span class="mandatory">*</span>
															</label>
															<input type="text" name="name" id="name" value="" class="form-control" tabindex="3" placeholder="Business Name" required="">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtEmail">Email
																<span class="mandatory">*</span>
															</label>
															<input type="hidden" name="send_otp" id="send-otp" value="">
															<input type="email" name="email" id="email" value="" class="form-control" tabindex="4" placeholder="Email" required="">
														</div>
													</div>
													<!-- <div class="col-md-6">
                              <div class="form-group password-input">
                                 <label for="txtPassword">Password
                                 <span class="mandatory">*</span>
                                 </label>
                     <input class="form-control" name="password" id="passwordRegistration" type="password" tabindex="5" placeholder="Password" oninput="removeSpace(this);">
                              </div>
                           </div>  -->
													<div class="col-md-6">
														<div class="form-group">
															<label for="txtMobile">Mobile
																<span class="mandatory">*</span>
															</label>
															<input class="form-control" name="phone-ext" id="phone-ext" type="text" value="+91" style="width: 60px; position: absolute;" readonly="">
															<input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required="">
															<div class="failed">
																<div style="color:#FF0000">
																	<small class="erro-sms" id="erro-sms">
																	</small>
																</div>
															</div>
														</div>
														<input name="password" id="passwordRegistration" type="hidden" oninput="removeSpace(this);" value="brl4bi88">
													</div>
												</div>
												<!--<div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="txtMobile">Mobile
                                 <span class="mandatory">*</span>
                                 </label>
                                 <input class="form-control" name="phone-ext" id="phone-ext" type="text" value="+91" style="width: 60px; position: absolute;" readonly>
                                 <input class="form-control numbercls" name="phone" id="phone" tabindex="6" type="text" maxlength="10" placeholder="Mobile" required>
                                 <div class="failed">
                                    <div style="color:#FF0000">
                                       <small class="erro-sms" id="erro-sms">
                                       </small>
                                    </div>
                                 </div>
                              </div>
                           </div>
                          </div>-->
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<div class="col-md-6">
											<label class="error">All fields are marked with * mandatory</label>
										</div>
										<div class="col-md-6">
											<button type="submit" class="btn btn-primary pull-right" id="submit-reg" tabindex="7" disabled="">Submit</button>
										</div>
									</div>

								</div>
							</form>

						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->


			</div>

 



		</div>


	</div>
</div>
</div>

<?php include 'footer.php';?>
